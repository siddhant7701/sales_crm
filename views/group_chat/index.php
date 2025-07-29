<?php
$pageTitle = "Group Chat";
require_once BASE_PATH . '/views/layout/header.php';
?>

<div class="chat-wrapper">
    <div class="chat-container">
        <div class="chat-header">
            <div class="chat-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <h2>Team Chat</h2>
            </div>
            <div class="chat-status">
                <span class="status-indicator"></span>
                <span>Online</span>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            <?php if (!empty($messages_data)): ?>
                <?php foreach ($messages_data as $message): ?>
                    <div class="message <?php echo ($message['sender_id'] == $_SESSION['id']) ? 'message-own' : 'message-other'; ?>" data-message-id="<?php echo $message['id']; ?>">
                        <div class="message-avatar">
                            <?php if ($message['sender_has_image']): ?>
                                <img src="index.php?action=user_image&id=<?php echo $message['sender_id']; ?>" alt="<?php echo htmlspecialchars($message['sender_username']); ?>" class="avatar-img">
                            <?php else: ?>
                                <div class="avatar-placeholder">
                                    <?php echo strtoupper(substr($message['sender_display_name'] ?? $message['sender_username'], 0, 2)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-sender"><?php echo htmlspecialchars($message['sender_display_name'] ?? $message['sender_username']); ?></span>
                                <span class="message-time"><?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?></span>
                            </div>
                            <div class="message-text">
                                <?php 
                                $message_text = htmlspecialchars($message['message_text']);
                                // Convert @mentions to highlighted text
                                $message_text = preg_replace('/@([a-zA-Z0-9_]+)/', '<span class="mention">@$1</span>', $message_text);
                                // Convert line breaks to <br> tags
                                $message_text = nl2br($message_text);
                                echo $message_text;
                                ?>
                            </div>
                            <?php if (!empty($message['tags'])): ?>
                                <div class="message-tags">
                                    <small>Tagged: 
                                        <?php 
                                        $tagged_users = array_map(function($tag) {
                                            return '@' . $tag['username'];
                                        }, $message['tags']);
                                        echo implode(', ', $tagged_users);
                                        ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-messages">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <p>No messages yet. Start the conversation!</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="chat-input-container">
            <form class="chat-form" action="index.php?action=group_chat_send" method="POST" id="messageForm">
                <div class="input-wrapper">
                    <textarea 
                        id="messageInput" 
                        name="message_text"
                        placeholder="Type your message... (Use @username to mention someone)" 
                        rows="1"
                        maxlength="1000"
                        required
                    ></textarea>
                    <button type="submit" class="send-button" id="sendButton">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 2L11 13"/>
                            <path d="M22 2L15 22L11 13L2 9L22 2Z"/>
                        </svg>
                    </button>
                </div>
                <div class="mention-suggestions" id="mentionSuggestions" style="display: none;">
                    <!-- Mention suggestions will be populated here -->
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const chatMessages = document.getElementById('chatMessages');
    const sendButton = document.getElementById('sendButton');
    
    let lastMessageId = <?php echo !empty($messages_data) ? max(array_column($messages_data, 'id')) : 0; ?>;
    let isPolling = false;

    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // Handle form submission with AJAX
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });

    // Handle Enter key (Shift+Enter for new line)
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    function sendMessage() {
        const message = messageInput.value.trim();
        if (!message) return;

        sendButton.disabled = true;
        sendButton.innerHTML = '<div class="loading-spinner"></div>';

        fetch('index.php?action=send_message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                messageInput.style.height = 'auto';
                addMessageToChat(data.message);
                lastMessageId = data.message.id;
                scrollToBottom();
            } else {
                alert('Failed to send message: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback to regular form submission
            messageForm.submit();
        })
        .finally(() => {
            sendButton.disabled = false;
            sendButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 2L11 13"/>
                    <path d="M22 2L15 22L11 13L2 9L22 2Z"/>
                </svg>
            `;
        });
    }

    function addMessageToChat(message) {
        // Remove "no messages" placeholder if it exists
        const noMessages = chatMessages.querySelector('.no-messages');
        if (noMessages) {
            noMessages.remove();
        }

        const messageDiv = document.createElement('div');
        messageDiv.className = 'message message-own';
        messageDiv.setAttribute('data-message-id', message.id);
        
        const avatarHtml = message.has_profile_image 
            ? `<img src="index.php?action=user_image&id=${message.user_id}" alt="${message.username}" class="avatar-img">`
            : `<div class="avatar-placeholder">${message.username.substring(0, 2).toUpperCase()}</div>`;

        messageDiv.innerHTML = `
            <div class="message-avatar">
                ${avatarHtml}
            </div>
            <div class="message-content">
                <div class="message-header">
                    <span class="message-sender">${message.username}</span>
                    <span class="message-time">${formatMessageTime(message.created_at)}</span>
                </div>
                <div class="message-text">${message.message_text.replace(/\n/g, '<br>')}</div>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
    }

    function formatMessageTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        });
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function pollForNewMessages() {
        if (isPolling) return;
        isPolling = true;

        fetch(`index.php?action=get_messages&last_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        if (message.user_id !== <?php echo $_SESSION['id']; ?>) {
                            addOtherUserMessage(message);
                        }
                        lastMessageId = Math.max(lastMessageId, message.id);
                    });
                    scrollToBottom();
                }
            })
            .catch(error => {
                console.error('Error polling for messages:', error);
            })
            .finally(() => {
                isPolling = false;
            });
    }

    function addOtherUserMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message message-other';
        messageDiv.setAttribute('data-message-id', message.id);
        
        const avatarHtml = message.has_profile_image 
            ? `<img src="index.php?action=user_image&id=${message.user_id}" alt="${message.username}" class="avatar-img">`
            : `<div class="avatar-placeholder">${message.username.substring(0, 2).toUpperCase()}</div>`;

        messageDiv.innerHTML = `
            <div class="message-avatar">
                ${avatarHtml}
            </div>
            <div class="message-content">
                <div class="message-header">
                    <span class="message-sender">${message.username}</span>
                    <span class="message-time">${formatMessageTime(message.created_at)}</span>
                </div>
                <div class="message-text">${message.message_text.replace(/\n/g, '<br>')}</div>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
    }

    // Poll for new messages every 3 seconds
    setInterval(pollForNewMessages, 3000);

    // Initial scroll to bottom
    scrollToBottom();

    // Mention functionality
    const users = <?php echo json_encode($users_map); ?>;
    const usernames = Object.keys(users);

    messageInput.addEventListener('input', function() {
        const text = this.value;
        const cursorPos = this.selectionStart;
        const textBeforeCursor = text.substring(0, cursorPos);
        const mentionMatch = textBeforeCursor.match(/@([a-zA-Z0-9_]*)$/);
        
        if (mentionMatch) {
            const query = mentionMatch[1].toLowerCase();
            const suggestions = usernames.filter(username => 
                username.toLowerCase().includes(query)
            ).slice(0, 5);
            
            showMentionSuggestions(suggestions, mentionMatch[0]);
        } else {
            hideMentionSuggestions();
        }
    });

    function showMentionSuggestions(suggestions, currentMention) {
        const suggestionsDiv = document.getElementById('mentionSuggestions');
        
        if (suggestions.length === 0) {
            hideMentionSuggestions();
            return;
        }
        
        suggestionsDiv.innerHTML = suggestions.map(username => 
            `<div class="mention-suggestion" data-username="${username}">@${username}</div>`
        ).join('');
        
        suggestionsDiv.style.display = 'block';
        
        // Add click handlers
        suggestionsDiv.querySelectorAll('.mention-suggestion').forEach(suggestion => {
            suggestion.addEventListener('click', function() {
                const username = this.getAttribute('data-username');
                const textarea = messageInput;
                const text = textarea.value;
                const cursorPos = textarea.selectionStart;
                const textBeforeCursor = text.substring(0, cursorPos);
                const textAfterCursor = text.substring(cursorPos);
                
                const newTextBefore = textBeforeCursor.replace(/@[a-zA-Z0-9_]*$/, `@${username} `);
                textarea.value = newTextBefore + textAfterCursor;
                textarea.focus();
                textarea.setSelectionRange(newTextBefore.length, newTextBefore.length);
                
                hideMentionSuggestions();
            });
        });
    }

    function hideMentionSuggestions() {
        document.getElementById('mentionSuggestions').style.display = 'none';
    }

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.chat-input-container')) {
            hideMentionSuggestions();
        }
    });
});
</script>

<?php require_once BASE_PATH . '/views/layout/footer.php'; ?>
