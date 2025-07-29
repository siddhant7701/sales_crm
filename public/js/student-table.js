document.addEventListener("DOMContentLoaded", () => {
  const studentTable = document.getElementById("studentTable")
  if (!studentTable) return

  const searchInput = document.getElementById("studentSearch")
  const tableRows = studentTable.querySelectorAll("tbody tr")
  const tableHeaders = studentTable.querySelectorAll("th[data-sort]")

  let currentSortColumn = null
  let currentSortDirection = "asc" // 'asc' or 'desc'

  // Function to filter table rows based on search input
  const filterTable = () => {
    const searchTerm = searchInput.value.toLowerCase()
    tableRows.forEach((row) => {
      const rowText = row.textContent.toLowerCase()
      if (rowText.includes(searchTerm)) {
        row.style.display = ""
      } else {
        row.style.display = "none"
      }
    })
  }

  // Function to sort table rows
  const sortTable = (column, direction) => {
    const tbody = studentTable.querySelector("tbody")
    const rowsArray = Array.from(tbody.children)
    const columnIndex = Array.from(studentTable.querySelectorAll("th")).findIndex((th) => th.dataset.sort === column)

    rowsArray.sort((a, b) => {
      const aText = a.children[columnIndex].textContent.trim()
      const bText = b.children[columnIndex].textContent.trim()

      // Basic string comparison, can be extended for numbers/dates
      if (direction === "asc") {
        return aText.localeCompare(bText)
      } else {
        return bText.localeCompare(aText)
      }
    })

    // Clear existing rows and append sorted ones
    rowsArray.forEach((row) => tbody.appendChild(row))
  }

  // Event listener for search input
  searchInput.addEventListener("keyup", filterTable)

  // Event listeners for sortable headers
  tableHeaders.forEach((header) => {
    header.addEventListener("click", () => {
      const column = header.dataset.sort

      // Reset sort icons for other columns
      tableHeaders.forEach((h) => {
        if (h !== header) {
          h.querySelector(".sort-icon").innerHTML = ""
        }
      })

      if (currentSortColumn === column) {
        currentSortDirection = currentSortDirection === "asc" ? "desc" : "asc"
      } else {
        currentSortColumn = column
        currentSortDirection = "asc"
      }

      // Update sort icon
      const sortIconSpan = header.querySelector(".sort-icon")
      if (currentSortDirection === "asc") {
        sortIconSpan.innerHTML =
          '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="m6 15 6-6 6 6"/></svg>'
      } else {
        sortIconSpan.innerHTML =
          '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="m18 9-6 6-6-6"/></svg>'
      }

      sortTable(currentSortColumn, currentSortDirection)
    })
  })
})
