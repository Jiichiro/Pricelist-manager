<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f9;
        margin: 0;
        padding: 0;
    }

    .dashboard-container {
        display: flex;
    }

    /* Sidebar */
    .sidebar {
        width: 220px;
        background: #2c3e50;
        color: #fff;
        height: 100vh;
        padding: 20px;
        box-sizing: border-box;
        position: fixed;
        top: 0;
        left: 0;
    }

    .sidebar h2 {
        font-size: 20px;
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

    /* Main content */
    .main-content {
        margin-left: 240px;
        padding: 20px;
        width: 100%;
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
    }

    .actions button {
        margin: 0 3px;
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .actions .edit {
        background: #3498db;
        color: #fff;
    }

    .actions .delete {
        background: #e74c3c;
        color: #fff;
    }

    /* Search bar */
    .search-add {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .search-add input {
        padding: 8px;
        width: 200px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .search-add button {
        background: #1abc9c;
        color: #fff;
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    /* Modal */
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
    }

    .modal-content input {
        width: 100%;
        padding: 8px;
        margin: 5px 0 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .modal-content button {
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .close {
        float: right;
        font-size: 20px;
        cursor: pointer;
    }
</style>

<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="#">Dashboard</a>
        <a href="#">Manage Price List</a>
        <a href="#">Users</a>
        <a href="#">Settings</a>
        <a href="./logic/auth/logout.php">Logout</a>
    </div>

    <!-- Main Content -->
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
                <tr>
                    <th>No</th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Property Of Allah</td>
                    <td>100k</td>
                    <td class="actions">
                        <button class="edit">Edit</button>
                        <button class="delete" onclick="confirmDelete(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>ToteBag</td>
                    <td>120k</td>
                    <td class="actions">
                        <button class="edit">Edit</button>
                        <button class="delete" onclick="confirmDelete(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Jaket TNF</td>
                    <td>180k</td>
                    <td class="actions">
                        <button class="edit">edit</button>
                        <button class="delete" onclick="confirmDelete(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Oxva Slim Go</td>
                    <td>150k</td>
                    <td class="actions">
                        <button class="edit">Edit</button>
                        <button class="delete" onclick="confirmDelete(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>Hoodie Adidas</td>
                    <td>200k</td>
                    <td class="actions">
                        <button class="edit">Edit</button>
                        <button class="delete" onclick="confirmDelete(this)">Delete</button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal" id="itemModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h4>Add New Item</h4>
        <input type="text" id="newItem" placeholder="Item name">
        <input type="text" id="newPrice" placeholder="Price">
        <button style="background:#1abc9c; color:#fff;" onclick="addItem()">Save</button>
    </div>
</div>

<script>
    // Search
    document.getElementById("searchInput").addEventListener("keyup", function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#priceTable tr");
        rows.forEach((row, index) => {
            if (index === 0) return; // skip header
            let item = row.cells[1].textContent.toLowerCase();
            row.style.display = item.includes(filter) ? "" : "none";
        });
    });

    // Modal
    function openModal() {
        document.getElementById("itemModal").style.display = "flex";
    }
    function closeModal() {
        document.getElementById("itemModal").style.display = "none";
    }

    // Add Item
    function addItem() {
        let item = document.getElementById("newItem").value;
        let price = document.getElementById("newPrice").value;
        if (item && price) {
            let table = document.getElementById("priceTable");
            let rowCount = table.rows.length;
            let row = table.insertRow(rowCount);
            row.innerHTML = `
                <td>${rowCount}</td>
                <td>${item}</td>
                <td>${price}</td>
                <td class="actions">
                    <button class="edit">Edit</button>
                    <button class="delete" onclick="confirmDelete(this)">Delete</button>
                </td>`;
            closeModal();
            document.getElementById("newItem").value = "";
            document.getElementById("newPrice").value = "";
        }
    }

    // Delete confirm
    function confirmDelete(btn) {
        if (confirm("Are you sure you want to delete this item?")) {
            let row = btn.parentNode.parentNode;
            row.remove();
        }
    }
</script>