document.addEventListener("DOMContentLoaded", () => {
  const themeToggleBtn = document.getElementById("theme-toggle")
  const body = document.body

  // Set initial theme based on cookie
  if (document.cookie.includes("theme=dark")) {
    body.classList.add("dark-mode")
  } else {
    body.classList.remove("dark-mode")
  }

  themeToggleBtn.addEventListener("click", () => {
    if (body.classList.contains("dark-mode")) {
      body.classList.remove("dark-mode")
      document.cookie = "theme=light; path=/; max-age=" + 365 * 24 * 60 * 60 // 1 year
    } else {
      body.classList.add("dark-mode")
      document.cookie = "theme=dark; path=/; max-age=" + 365 * 24 * 60 * 60 // 1 year
    }
  })
})
