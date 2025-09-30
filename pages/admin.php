<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #2c3e50;
        }

        .dashboard-container {
            display: flex;
        }

        .sidebar {
            width: 220px;
            background: #2c3e50;
            color: white;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar h2 {
            font-size: 22px;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: #ecf0f1;
            text-decoration: none;
            padding: 10px 0;
            transition: 0.3s;
        }

        .sidebar a:hover {
            color: #1abc9c;
            padding-left: 10px;
        }

        .main-content {
            margin-left: 240px;
            padding: 20px;
            width: 100%;
        }

        .main-content h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .main-content p {
            margin-bottom: 20px;
            color: #666;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card h3 {
            margin: 0 0 15px;
        }

        .price-table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 8px;
        }

        .price-table th,
        .price-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .price-table th {
            background: #1abc9c;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
            position: relative;
        }

        .price-table th:hover {
            background: #16a085;
        }

        .price-table th::after {
            content: ' ⇅';
            font-size: 12px;
            opacity: 0.5;
        }

        .price-table th.sort-asc::after {
            content: ' ▲';
            opacity: 1;
        }

        .price-table th.sort-desc::after {
            content: ' ▼';
            opacity: 1;
        }

        .price-table tbody tr {
            background: #fff;
            cursor: move;
            transition: background 0.2s;
        }

        .price-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .price-table tbody tr:hover {
            background: #f1f7fd;
        }

        .price-table tbody tr.dragging {
            opacity: 0.5;
            background: #e8f5e9;
        }

        .price-table tbody tr.drag-over {
            border-top: 3px solid #1abc9c;
        }

        .drag-handle {
            cursor: move;
            color: #95a5a6;
            margin-right: 5px;
        }

        .actions button {
            margin: 0 3px;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .actions .edit {
            background: #3498db;
            color: #fff;
        }

        .actions .delete {
            background: #e74c3c;
            color: #fff;
        }

        .search-add {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .search-add input {
            padding: 8px;
            width: 220px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-add button {
            background: #1abc9c;
            color: #fff;
            padding: 8px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 350px;
        }

        .modal-content h4 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .modal-content input {
            width: 100%;
            padding: 8px;
            margin: 6px 0 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .modal-content button {
            padding: 8px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .close {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }

        .info-text {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="./">Dashboard</a>
            <a href="#">Manage Price List</a>
            <a href="?page=add-user">Users</a>
            <a href="#">Settings</a>
            <a href="./logic/auth/logout.php">Logout</a>
        </div>

        <div class="main-content">
            <h1>Admin Dashboard</h1>
            <p>Welcome to the admin dashboard. Here you can manage the price list.</p>

            <div class="card">
                <h3>Price List</h3>

                <div class="search-add">
                    <input type="text" id="searchInput" placeholder="Search item...">
                    <button onclick="openModal()">+ Add Item</button>
                </div>

                <table class="price-table" id="priceTable">
                    <thead>
                        <tr>
                            <th data-column="0">No</th>
                            <th data-column="1">Item</th>
                            <th data-column="2">Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Data will be loaded from localStorage -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal" id="itemModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h4 id="modalTitle">Add New Item</h4>
            <input type="hidden" id="editIndex">
            <input type="text" id="newItem" placeholder="Item name">
            <input type="text" id="newPrice" placeholder="Price (e.g., 100k)">
            <button style="background:#1abc9c; color:#fff;" onclick="saveItem()">Save</button>
        </div>
    </div>

    <script>
        let draggedElement = null;
        let currentSort = { column: -1, direction: 'none' };
        let priceData = [];

        // Initialize data
        function initData() {
            const savedData = localStorage.getItem('priceListData');
            
            if (savedData) {
                priceData = JSON.parse(savedData);
            } else {
                // Default data
                priceData = [
                    { id: 1, item: 'Property Of Allah', price: '100k', priceNum: 100 },
                    { id: 2, item: 'ToteBag', price: '120k', priceNum: 120 },
                    { id: 3, item: 'Jaket TNF', price: '180k', priceNum: 180 },
                    { id: 4, item: 'Oxva Slim Go', price: '150k', priceNum: 150 },
                    { id: 5, item: 'Hoodie Adidas', price: '200k', priceNum: 200 }
                ];
                saveToLocalStorage();
            }
            
            renderTable();
        }

        // Save to localStorage
        function saveToLocalStorage() {
            localStorage.setItem('priceListData', JSON.stringify(priceData));
        }

        // Render table
        function renderTable() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            
            priceData.forEach((data, index) => {
                const row = tbody.insertRow();
                row.draggable = true;
                row.setAttribute('data-id', data.id);
                
                row.innerHTML = `
                    <td><span class="drag-handle">☰</span>${index + 1}</td>
                    <td>${data.item}</td>
                    <td data-price="${data.priceNum}">${data.price}</td>
                    <td class="actions">
                        <button class="edit" onclick="editItem(${data.id})">Edit</button>
                        <button class="delete" onclick="confirmDelete(${data.id})">Delete</button>
                    </td>`;
                
                // Add drag and drop
                row.addEventListener('dragstart', handleDragStart);
                row.addEventListener('dragend', handleDragEnd);
                row.addEventListener('dragover', handleDragOver);
                row.addEventListener('drop', handleDrop);
                row.addEventListener('dragleave', handleDragLeave);
            });
        }

        // Search functionality
        document.getElementById("searchInput").addEventListener("keyup", function () {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#tableBody tr");
            rows.forEach((row) => {
                let item = row.cells[1].textContent.toLowerCase();
                row.style.display = item.includes(filter) ? "" : "none";
            });
        });

        // Sorting functionality
        document.querySelectorAll('.price-table th[data-column]').forEach(header => {
            header.addEventListener('click', function() {
                const column = parseInt(this.getAttribute('data-column'));
                sortTable(column);
            });
        });

        function sortTable(column) {
            // Determine sort direction
            let direction = 'asc';
            if (currentSort.column === column) {
                if (currentSort.direction === 'asc') direction = 'desc';
                else if (currentSort.direction === 'desc') direction = 'none';
            }
            
            // Update header classes
            document.querySelectorAll('.price-table th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
            });
            
            if (direction !== 'none') {
                const header = document.querySelector(`.price-table th[data-column="${column}"]`);
                header.classList.add(direction === 'asc' ? 'sort-asc' : 'sort-desc');
                
                priceData.sort((a, b) => {
                    let aValue, bValue;
                    
                    if (column === 2) { // Price column
                        aValue = a.priceNum;
                        bValue = b.priceNum;
                    } else if (column === 0) { // No column
                        aValue = priceData.indexOf(a);
                        bValue = priceData.indexOf(b);
                    } else { // Item column
                        aValue = a.item.toLowerCase();
                        bValue = b.item.toLowerCase();
                    }
                    
                    if (aValue < bValue) return direction === 'asc' ? -1 : 1;
                    if (aValue > bValue) return direction === 'asc' ? 1 : -1;
                    return 0;
                });
                
                renderTable();
            } else {
                // Reset to original order
                initData();
            }
            
            currentSort = { column, direction };
        }

        // Drag and Drop functionality
        function handleDragStart(e) {
            draggedElement = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        }

        function handleDragEnd(e) {
            this.classList.remove('dragging');
            document.querySelectorAll('#tableBody tr').forEach(row => {
                row.classList.remove('drag-over');
            });
        }

        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over');
            return false;
        }

        function handleDrop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }
            
            if (draggedElement !== this) {
                const tbody = document.getElementById('tableBody');
                const allRows = [...tbody.querySelectorAll('tr')];
                const draggedIndex = allRows.indexOf(draggedElement);
                const targetIndex = allRows.indexOf(this);
                
                // Reorder data array
                const draggedData = priceData[draggedIndex];
                priceData.splice(draggedIndex, 1);
                priceData.splice(targetIndex, 0, draggedData);
                
                saveToLocalStorage();
                renderTable();
            }
            
            return false;
        }

        function handleDragLeave(e) {
            this.classList.remove('drag-over');
        }

        // Modal functions
        function openModal() {
            document.getElementById('modalTitle').textContent = 'Add New Item';
            document.getElementById('editIndex').value = '';
            document.getElementById('newItem').value = '';
            document.getElementById('newPrice').value = '';
            document.getElementById("itemModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("itemModal").style.display = "none";
        }

        function editItem(id) {
            const data = priceData.find(item => item.id === id);
            
            if (data) {
                document.getElementById('modalTitle').textContent = 'Edit Item';
                document.getElementById('editIndex').value = id;
                document.getElementById('newItem').value = data.item;
                document.getElementById('newPrice').value = data.price;
                document.getElementById("itemModal").style.display = "flex";
            }
        }

        function saveItem() {
            const item = document.getElementById("newItem").value.trim();
            const price = document.getElementById("newPrice").value.trim();
            const editIndex = document.getElementById('editIndex').value;
            
            if (item && price) {
                const priceNum = parseInt(price.replace(/\D/g, ''));
                
                if (editIndex !== '') {
                    // Edit existing item
                    const data = priceData.find(d => d.id === parseInt(editIndex));
                    if (data) {
                        data.item = item;
                        data.price = price;
                        data.priceNum = priceNum;
                    }
                } else {
                    // Add new item
                    const newId = priceData.length > 0 ? Math.max(...priceData.map(d => d.id)) + 1 : 1;
                    priceData.push({
                        id: newId,
                        item: item,
                        price: price,
                        priceNum: priceNum
                    });
                }
                
                saveToLocalStorage();
                renderTable();
                closeModal();
            } else {
                alert('Please fill in all fields');
            }
        }

        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this item?")) {
                priceData = priceData.filter(data => data.id !== id);
                saveToLocalStorage();
                renderTable();
            }
        }

        // Initialize on page load
        initData();
    </script>
</body>
</html>