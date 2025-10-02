<?php
require __DIR__ . "/../logic/database/connect.php";

// --- Handle Ajax POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete
    if (isset($_POST['deleteId'])) {
        $id = intval($_POST['deleteId']);
        echo $conn->query("DELETE FROM produk WHERE id=$id") ? "deleted" : "Error";
        exit;
    }

    // Import Excel
    if (isset($_POST['importExcel'])) {
        $data = json_decode($_POST['importExcel'], true);
        $success = 0;
        foreach ($data as $row) {
            if (empty($row['nama']) || empty($row['harga'])) continue;
            $stmt = $conn->prepare("INSERT INTO produk(nama_produk,harga) VALUES(?,?)");
            $stmt->bind_param("ss", $row['nama'], $row['harga']);
            if ($stmt->execute()) $success++;
        }
        echo $success . " items imported";
        exit;
    }

    // Import PDF (converted to text)
    if (isset($_POST['importPDF'])) {
        $data = json_decode($_POST['importPDF'], true);
        $success = 0;
        foreach ($data as $row) {
            if (empty($row['nama']) || empty($row['harga'])) continue;
            $stmt = $conn->prepare("INSERT INTO produk(nama_produk,harga) VALUES(?,?)");
            $stmt->bind_param("ss", $row['nama'], $row['harga']);
            if ($stmt->execute()) $success++;
        }
        echo $success . " items imported";
        exit;
    }

    // Add/Edit
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nama = $_POST['nama'] ?? "";
    $harga = $_POST['harga'] ?? "";

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE produk SET nama_produk=?, harga=? WHERE id=?");
        $stmt->bind_param("ssi", $nama, $harga, $id);
        echo $stmt->execute() ? "success" : "Error";
    } else {
        $stmt = $conn->prepare("INSERT INTO produk(nama_produk,harga) VALUES(?,?)");
        $stmt->bind_param("ss", $nama, $harga);
        echo $stmt->execute() ? "success" : "Error";
    }
    exit;
}

// --- Ambil data dari database ---
$result = $conn->query("SELECT * FROM produk ORDER BY id ASC");
$produkList = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $produkList[] = $row;
    }
}

?>

<style>
body {font-family: Arial,sans-serif; background:#f4f6f9; margin:0;padding:0;}
.dashboard-container{display:flex;}
.main-content{margin:20px 40px 20px 280px;padding:20px;width:100%;}
.card{background:#fff;border-radius:8px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:20px;}
.card h3{margin:0 0 15px;}
.price-table{width:100%;border-collapse:collapse;}
.price-table th,.price-table td{padding:12px;border:1px solid #ddd;text-align:center;border-radius:4px;}
.price-table th{background:#1abc9c;color:#fff;}
.actions button{margin:0 3px;padding:5px 10px;border:none;border-radius:4px;cursor:pointer;}
.actions .edit{background:#3498db;color:#fff;}
.actions .delete{background:#e74c3c;color:#fff;}
.search-add{display:flex;justify-content:space-between;margin-bottom:15px;align-items:center;flex-wrap:wrap;gap:10px;}
.search-add input{padding:8px;width:200px;border:1px solid #ccc;border-radius:4px;}
.search-add button{background:#1abc9c;color:#fff;padding:8px 12px;border:none;border-radius:4px;cursor:pointer;}
.export-buttons{display:flex;gap:8px;}
.export-buttons button{padding:8px 12px;border:none;border-radius:4px;cursor:pointer;color:#fff;font-weight:bold;}
.export-excel{background:#27ae60;}
.export-excel:hover{background:#229954;}
.export-pdf{background:#e74c3c;}
.export-pdf:hover{background:#c0392b;}
.import-excel{background:#3498db;}
.import-excel:hover{background:#2980b9;}
.import-pdf{background:#9b59b6;}
.import-pdf:hover{background:#8e44ad;}
.modal{display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.6);justify-content:center;align-items:center;}
.modal-content{background:#fff;padding:20px;border-radius:8px;width:350px;}
.modal-content h4{margin-top:0;}
.modal-content input{width:100%;padding:8px;margin:5px 0 10px;border:1px solid #ccc;border-radius:4px;}
.modal-content button{padding:8px 12px;border:none;border-radius:8px;cursor:pointer;}
.close{float:right;font-size:20px;cursor:pointer;}
.hidden{display:none;}
</style>

<div class="dashboard-container">
    <?php include __DIR__ . '../../components/sidebar.php'; ?>
    <div class="main-content">
        <h1>Admin Dashboard</h1>
        <p>Manage your price list below.</p>

            <div class="card">
                <h3>Price List</h3>

            <div class="search-add">
                <input type="text" id="searchInput" placeholder="Search item...">
                <div style="display:flex;gap:8px;">
                    <div class="export-buttons">
                        <button class="export-excel" onclick="exportToExcel()">📊 Export Excel</button>
                        <button class="export-pdf" onclick="exportToPDF()">📄 Export PDF</button>
                        <button class="import-excel" onclick="document.getElementById('importExcelFile').click()">📥 Import Excel</button>
                        <button class="import-pdf" onclick="document.getElementById('importPDFFile').click()">📥 Import PDF</button>
                    </div>
                    <button onclick="openModal()">+ Add Item</button>
                </div>
            </div>

            <input type="file" id="importExcelFile" class="hidden" accept=".xlsx,.xls" onchange="importExcel(this)">
            <input type="file" id="importPDFFile" class="hidden" accept=".pdf" onchange="importPDF(this)">

            <table class="price-table" id="priceTable">
                <tr>
                    <th>No</th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
                <?php foreach($produkList as $index => $p): ?>
                <tr data-id="<?= $p['id'] ?>">
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($p['nama_produk']) ?></td>
                    <td><?= htmlspecialchars($p['harga']) ?></td>
                    <td class="actions">
                        <button class="edit" onclick="openEditModal(this)">Edit</button>
                        <button class="delete" onclick="deleteItem(this)">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal" id="itemModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h4 id="modalTitle">Add New Item</h4>
        <input type="hidden" id="itemId">
        <input type="text" id="itemName" placeholder="Item name">
        <input type="text" id="itemPrice" placeholder="Price">
        <button style="background:#1abc9c;color:#fff;" onclick="saveItem()">Save</button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<script>
// Search
document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll("#priceTable tr").forEach((row,index)=>{
        if(index===0) return;
        let text = row.cells[1].textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});

// Modal
function openModal() {
    document.getElementById("modalTitle").innerText="Add New Item";
    document.getElementById("itemId").value="";
    document.getElementById("itemName").value="";
    document.getElementById("itemPrice").value="";
    document.getElementById("itemModal").style.display="flex";
}
function closeModal() {
    document.getElementById("itemModal").style.display="none";
}
function openEditModal(btn) {
    let row = btn.closest("tr");
    document.getElementById("modalTitle").innerText="Edit Item";
    document.getElementById("itemId").value = row.dataset.id;
    document.getElementById("itemName").value = row.cells[1].textContent;
    document.getElementById("itemPrice").value = row.cells[2].textContent;
    document.getElementById("itemModal").style.display="flex";
}

// Save item (Add/Edit)
function saveItem() {
    let id = document.getElementById("itemId").value;
    let nama = document.getElementById("itemName").value;
    let harga = document.getElementById("itemPrice").value;

    if(!nama || !harga) { alert("Fill all fields"); return; }

    let formData = new FormData();
    formData.append("id", id);
    formData.append("nama", nama);
    formData.append("harga", harga);

    fetch("", {method:"POST", body:formData})
    .then(res=>res.text())
    .then(res=>{
        if(res==="success") location.reload();
        else alert("Error saving data");
    });
}

// Delete
function deleteItem(btn){
    if(!confirm("Are you sure?")) return;
    let row = btn.closest("tr");
    let id = row.dataset.id;

    let formData = new FormData();
    formData.append("deleteId", id);

    fetch("", {method:"POST", body:formData})
    .then(res=>res.text())
    .then(res=>{
        if(res==="deleted") row.remove();
        else alert("Error deleting data");
    });
}

// Export to Excel
function exportToExcel() {
    let table = document.getElementById("priceTable");
    let wb = XLSX.utils.book_new();
    
    let data = [];
    let rows = table.querySelectorAll("tr");
    
    rows.forEach((row, index) => {
        if(row.style.display === "none") return;
        
        let rowData = [];
        let cells = row.querySelectorAll("th, td");
        
        cells.forEach((cell, cellIndex) => {
            if(cellIndex < cells.length - 1) {
                rowData.push(cell.textContent);
            }
        });
        
        data.push(rowData);
    });
    
    let ws = XLSX.utils.aoa_to_sheet(data);
    XLSX.utils.book_append_sheet(wb, ws, "Price List");
    XLSX.writeFile(wb, "price_list.xlsx");
}

// Export to PDF
function exportToPDF() {
    const { jsPDF } = window.jspdf;
    let doc = new jsPDF();
    
    let rows = [];
    let table = document.getElementById("priceTable");
    let tableRows = table.querySelectorAll("tr");
    
    tableRows.forEach((row, index) => {
        if(row.style.display === "none") return;
        
        let rowData = [];
        let cells = row.querySelectorAll("th, td");
        
        cells.forEach((cell, cellIndex) => {
            if(cellIndex < cells.length - 1) {
                rowData.push(cell.textContent);
            }
        });
        
        rows.push(rowData);
    });
    
    doc.autoTable({
        head: [rows[0]],
        body: rows.slice(1),
        theme: 'grid',
        headStyles: { fillColor: [26, 188, 156] }
    });
    
    doc.save("price_list.pdf");
}

// Import Excel
function importExcel(input) {
    let file = input.files[0];
    if (!file) return;
    
    let reader = new FileReader();
    reader.onload = function(e) {
        let data = new Uint8Array(e.target.result);
        let workbook = XLSX.read(data, {type: 'array'});
        let firstSheet = workbook.Sheets[workbook.SheetNames[0]];
        let rows = XLSX.utils.sheet_to_json(firstSheet, {header: 1});
        
        // Skip header row
        let importData = [];
        for(let i = 1; i < rows.length; i++) {
            if(rows[i][1] && rows[i][2]) {
                importData.push({
                    nama: rows[i][1].toString(),
                    harga: rows[i][2].toString()
                });
            }
        }
        
        if(importData.length === 0) {
            alert("No valid data found in Excel file");
            return;
        }
        
        let formData = new FormData();
        formData.append("importExcel", JSON.stringify(importData));
        
        fetch("", {method:"POST", body:formData})
        .then(res=>res.text())
        .then(res=>{
            alert(res);
            location.reload();
        });
    };
    reader.readAsArrayBuffer(file);
    input.value = '';
}

// Import PDF
function importPDF(input) {
    let file = input.files[0];
    if (!file) return;
    
    let reader = new FileReader();
    reader.onload = function(e) {
        let typedarray = new Uint8Array(e.target.result);
        
        pdfjsLib.getDocument(typedarray).promise.then(function(pdf) {
            let maxPages = pdf.numPages;
            let allText = '';
            
            let getPageText = function(pageNum) {
                return pdf.getPage(pageNum).then(function(page) {
                    return page.getTextContent().then(function(textContent) {
                        return textContent.items.map(item => item.str).join(' ');
                    });
                });
            };
            
            let promises = [];
            for(let i = 1; i <= maxPages; i++) {
                promises.push(getPageText(i));
            }
            
            Promise.all(promises).then(function(texts) {
                allText = texts.join(' ');
                
                // Parse text to extract item and price
                let importData = [];
                let lines = allText.split(/\s+/);
                
                // Find pattern: number, item name, price
                for(let i = 0; i < lines.length; i++) {
                    if(/^\d+$/.test(lines[i])) {
                        let nama = lines[i+1];
                        let harga = lines[i+2];
                        
                        if(nama && harga && !/^(No|Item|Price|Action)$/i.test(nama)) {
                            importData.push({
                                nama: nama,
                                harga: harga
                            });
                        }
                    }
                }
                
                if(importData.length === 0) {
                    alert("No valid data found in PDF file");
                    return;
                }
                
                let formData = new FormData();
                formData.append("importPDF", JSON.stringify(importData));
                
                fetch("", {method:"POST", body:formData})
                .then(res=>res.text())
                .then(res=>{
                    alert(res);
                    location.reload();
                });
            });
        });
    };
    reader.readAsArrayBuffer(file);
    input.value = '';
}

// Set PDF.js worker
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
</script>

</html>