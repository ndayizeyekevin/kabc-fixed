// customTable.js

export function initTable(tableId, searchInputId, paginationId) {
    // Sorting Function
    const sortTable = (n) => {
        const table = document.getElementById(tableId);
        let rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        switching = true;
        dir = "asc";
        while (switching) {
            switching = false;
            rows = table.rows;
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("TD")[n];
                y = rows[i + 1].getElementsByTagName("TD")[n];
                if (dir == "asc") {
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == "desc") {
                    if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchcount++;
            } else {
                if (switchcount === 0 && dir === "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }
    };

    // Add sorting event listeners
    document.querySelectorAll(`#${tableId} th`).forEach((header, index) => {
        header.addEventListener('click', () => sortTable(index));
    });

    // Search Function
    const searchInput = document.getElementById(searchInputId);
    searchInput.addEventListener('keyup', function () {
        const value = searchInput.value.toLowerCase();
        document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    // Pagination Function
    const paginateTable = (rowsPerPage) => {
        const rows = document.querySelectorAll(`#${tableId} tbody tr`);
        const rowsCount = rows.length;
        const pageCount = Math.ceil(rowsCount / rowsPerPage);
        const pagination = document.getElementById(paginationId);

        pagination.innerHTML = '';
        for (let i = 1; i <= pageCount; i++) {
            const pageItem = document.createElement('li');
            pageItem.classList.add('page-item');
            const pageLink = document.createElement('a');
            pageLink.classList.add('page-link');
            pageLink.href = '#';
            pageLink.textContent = i;
            pageItem.appendChild(pageLink);
            pagination.appendChild(pageItem);
        }

        const displayRows = (index) => {
            const start = (index - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            rows.forEach((row, idx) => {
                row.style.display = idx >= start && idx < end ? '' : 'none';
            });
        };

        // Initialize pagination
        pagination.querySelector('li a').classList.add('active');
        displayRows(1);

        pagination.querySelectorAll('li a').forEach((link, index) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                pagination.querySelectorAll('li a').forEach(a => a.classList.remove('active'));
                link.classList.add('active');
                displayRows(index + 1);
            });
        });
    };

    // Initialize pagination
    paginateTable(5); // Adjust the number of rows per page as needed
}