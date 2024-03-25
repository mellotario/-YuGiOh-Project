<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    th {
        cursor: pointer;
    }

    .asc::after {
        content: '▲';
        margin-left: 5px;
    }

    .desc::after {
        content: '▼';
        margin-left: 5px;
    }

    .disabled {
        pointer-events: none;
        color: #ccc;
    }
</style>

<main>
    <h2>Page List</h2>
    <table id="page-list">
        <thead>
            <tr>
                <th><a href="#" data-sort="title" class="<?php echo isset($_SESSION['user_id']) ? '' : 'disabled'; ?>" onclick="<?php echo isset($_SESSION['user_id']) ? "sortPages('title')" : "event.preventDefault();" ?>">Title</a></th>
                <th><a href="#" data-sort="created_at" class="<?php echo isset($_SESSION['user_id']) ? '' : 'disabled'; ?>" onclick="<?php echo isset($_SESSION['user_id']) ? "sortPages('created_at')" : "event.preventDefault();" ?>">Created At</a></th>
                <th><a href="#" data-sort="updated_at" class="<?php echo isset($_SESSION['user_id']) ? '' : 'disabled'; ?>" onclick="<?php echo isset($_SESSION['user_id']) ? "sortPages('updated_at')" : "event.preventDefault();" ?>">Updated At</a></th>
            </tr>
        </thead>
        <tbody id="page-list-body">
            <!-- Pages will be dynamically added here -->
        </tbody>
    </table>
</main>

<script>
    function sortPages(column) {
        let currentOrder = 'ASC'; 
        let sortIndicator = document.querySelector(`th a[data-sort="${column}"]`);
        if (sortIndicator.classList.contains('asc')) {
            currentOrder = 'DESC';
        }

        let xhr = new XMLHttpRequest();
        xhr.open('GET', `get-pages.php?sort_column=${column}&sort_order=${currentOrder}`, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                let pages = JSON.parse(xhr.responseText);
                updatePageList(pages);
                updateSortIndicator(column, currentOrder);
            } else {
                console.error('Error fetching sorted pages');
            }
        };
        xhr.send();
    }

    function updatePageList(pages) {
        let tbody = document.getElementById('page-list-body');
        tbody.innerHTML = ''; // Clear existing rows
        pages.forEach(page => {
            let row = document.createElement('tr');
            row.innerHTML = `
            <td>${page.title}</td>
            <td>${page.created_at}</td>
            <td>${page.updated_at}</td>
        `;
            tbody.appendChild(row);
        });
    }

    function updateSortIndicator(column, order) {
        document.querySelectorAll('th a').forEach(link => link.classList.remove('asc', 'desc'));
        let sortIndicator = document.querySelector(`th a[data-sort="${column}"]`);
        if (sortIndicator) {
            sortIndicator.classList.add(order.toLowerCase());
        }
    }


    document.addEventListener('DOMContentLoaded', function() {
        // Initial load of page list
        sortPages('title'); // Sort by title by default
    });
</script>