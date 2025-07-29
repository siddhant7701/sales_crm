<?php
class GroupMessage {
    private $conn;
    private $table_name = "group_messages";
    private $tags_table_name = "message_tags";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new group message
    public function create($user_id, $message_text) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, message_text) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("is", $user_id, $message_text);
        if ($stmt->execute()) {
            $message_id = $stmt->insert_id;
            $stmt->close();
            return $message_id;
        }
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        $stmt->close();
        return false;
    }

    // Get all group messages from the last month with sender info
    public function getAll() {
        $one_month_ago = date('Y-m-d H:i:s', strtotime('-1 month'));
        $query = "SELECT gm.id, gm.message_text, gm.created_at, 
                  u.username as sender_username, u.display_name as sender_display_name, 
                  u.id as sender_id,
                  CASE WHEN u.profile_image IS NOT NULL THEN 1 ELSE 0 END as sender_has_image,
                  u.profile_image_type as sender_profile_image_type
                  FROM " . $this->table_name . " gm
                  JOIN users u ON gm.user_id = u.id
                  WHERE gm.created_at >= ?
                  ORDER BY gm.created_at ASC";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed (getAll): (" . $this->conn->errno . ") " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("s", $one_month_ago);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    // Get group messages newer than a specific timestamp (within last month)
    public function getNewerThan($timestamp) {
        $one_month_ago = date('Y-m-d H:i:s', strtotime('-1 month'));
        $query = "SELECT gm.id, gm.message_text, gm.created_at, 
                  u.username as sender_username, u.display_name as sender_display_name, 
                  u.id as sender_id,
                  CASE WHEN u.profile_image IS NOT NULL THEN 1 ELSE 0 END as sender_has_image,
                  u.profile_image_type as sender_profile_image_type
                  FROM " . $this->table_name . " gm
                  JOIN users u ON gm.user_id = u.id
                  WHERE gm.created_at > ? AND gm.created_at >= ?
                  ORDER BY gm.created_at ASC";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed (getNewerThan): (" . $this->conn->errno . ") " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("ss", $timestamp, $one_month_ago);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    // Add a tag for a message
    public function addTag($message_id, $tagged_user_id) {
        $query = "INSERT INTO " . $this->tags_table_name . " (message_id, tagged_user_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed (addTag): (" . $this->conn->errno . ") " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("ii", $message_id, $tagged_user_id);
        if (@$stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    // Get tagged users for a message
    public function getTagsByMessageId($message_id) {
        $query = "SELECT mt.tagged_user_id, u.username FROM " . $this->tags_table_name . " mt JOIN users u ON mt.tagged_user_id = u.id WHERE mt.message_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed (getTagsByMessageId): (" . $this->conn->errno . ") " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tags = [];
        while ($row = $result->fetch_assoc()) {
            $tags[] = $row;
        }
        $stmt->close();
        return $tags;
    }

    // Delete individual messages that are older than 1 month
    public function deleteExpiredMessages() {
        $one_month_ago = date('Y-m-d H:i:s', strtotime('-1 month'));
        $query = "DELETE FROM " . $this->table_name . " WHERE created_at < ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed (deleteExpiredMessages): (" . $this->conn->errno . ") " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("s", $one_month_ago);
        if ($stmt->execute()) {
            $deleted_rows = $stmt->affected_rows;
            $stmt->close();
            return $deleted_rows;
        }
        error_log("Execute failed (deleteExpiredMessages): (" . $stmt->errno . ") " . $stmt->error);
        $stmt->close();
        return false;
    }

    // Clean up expired messages (this should be called regularly)
    public function cleanupExpiredMessages() {
        return $this->deleteExpiredMessages();
    }
}
?>
