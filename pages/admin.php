<?php
// file: produk_full.php
// Pastikan file ini diletakkan di folder yang sesuai dan koneksi DB benar.
require __DIR__ . "/../logic/database/connect.php";

// --- Handle Ajax POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete (prepared)
    if (isset($_POST['deleteId'])) {
        $id = intval($_POST['deleteId']);
        $stmt = $conn->prepare("DELETE FROM produk WHERE id = ?");
        $stmt->bind_param("i", $id);
        echo $stmt->execute() ? "deleted" : "Error";
        exit;
    }

    // Import Excel (JSON payload from client)
    if (isset($_POST['importExcel'])) {
        $data = json_decode($_POST['importExcel'], true);
        $success = 0;
        if (is_array($data)) {
            foreach ($data as $row) {
                $nama = $row['nama'] ?? "";
                $harga = isset($row['harga']) ? floatval($row['harga']) : 0;
                $stok = isset($row['stok']) ? intval($row['stok']) : 0;
                $kategori = isset($row['kategori']) ? intval($row['kategori']) : 0;
                $deskripsi = $row['deskripsi'] ?? "";
                $gambar = $row['gambar'] ?? "";

                if ($nama === "" || $harga <= 0) continue;

                $stmt = $conn->prepare("INSERT INTO produk (nama_produk, harga, stok, id_kategori, deskripsi, gambar_url) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sdiiss", $nama, $harga, $stok, $kategori, $deskripsi, $gambar);
                if ($stmt->execute()) $success++;
            }
        }
        echo $success . " items imported";
        exit;
    }

    // Import PDF (JSON payload from client after client-side parsing)
    if (isset($_POST['importPDF'])) {
        $data = json_decode($_POST['importPDF'], true);
        $success = 0;
        if (is_array($data)) {
            foreach ($data as $row) {
                $nama = $row['nama'] ?? "";
                $harga = isset($row['harga']) ? floatval($row['harga']) : 0;
                $stok = isset($row['stok']) ? intval($row['stok']) : 0;
                $kategori = isset($row['kategori']) ? intval($row['kategori']) : 0;
                $deskripsi = $row['deskripsi'] ?? "";
                $gambar = $row['gambar'] ?? "";

                if ($nama === "" || $harga <= 0) continue;

                $stmt = $conn->prepare("INSERT INTO produk (nama_produk, harga, stok, id_kategori, deskripsi, gambar_url) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sdiiss", $nama, $harga, $stok, $kategori, $deskripsi, $gambar);
                if ($stmt->execute()) $success++;
            }
        }
        echo $success . " items imported";
        exit;
    }

    // Add/Edit
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nama = $_POST['nama'] ?? "";
    $harga = isset($_POST['harga']) ? floatval($_POST['harga']) : 0;
    $stok = isset($_POST['stok']) ? intval($_POST['stok']) : 0;
    $kategori = isset($_POST['kategori']) ? intval($_POST['kategori']) : 0;
    $deskripsi = $_POST['deskripsi'] ?? "";
    $gambar = $_POST['gambar'] ?? "";

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE produk SET nama_produk = ?, harga = ?, stok = ?, id_kategori = ?, deskripsi = ?, gambar_url = ? WHERE id = ?");
        // types: s (nama), d (harga), i (stok), i (kategori), s (deskripsi), s (gambar), i (id)
        $stmt->bind_param("sdiissi", $nama, $harga, $stok, $kategori, $deskripsi, $gambar, $id);
        echo $stmt->execute() ? "success" : "Error";
    } else {
        $stmt = $conn->prepare("INSERT INTO produk (nama_produk, harga, stok, id_kategori, deskripsi, gambar_url) VALUES (?, ?, ?, ?, ?, ?)");
        // types: s (nama), d (harga), i (stok), i (kategori), s (deskripsi), s (gambar)
        $stmt->bind_param("sdiiss", $nama, $harga, $stok, $kategori, $deskripsi, $gambar);
        echo $stmt->execute() ? "success" : "Error";
    }
    exit;
}

// --- Ambil data dari database ---
$result = $conn->query("SELECT produk.id, produk.nama_produk, produk.harga, produk.stok, produk.id_kategori, kategori.nama_kategori as kategori, produk.deskripsi, produk.gambar_url 
    FROM produk 
    LEFT JOIN kategori ON produk.id_kategori = kategori.id 
    ORDER BY produk.id ASC");

$produkList = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $produkList[] = $row;
    }
}

// Ambil semua kategori
$kategoriList = [];
$resKat = $conn->query("SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");
if ($resKat) {
    while ($r = $resKat->fetch_assoc()) {
        $kategoriList[] = $r;
    }
}
?>


<style>
body {font-family: Arial,sans-serif; background:#f4f6f9; margin:0;padding:0;}
.dashboard-container{display:flex;}
.main-content{margin:20px 40px 20px 280px;padding:20px;width:100%;transition:margin 0.3s;}
.card{background:#fff;border-radius:8px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:20px;}
.card h3{margin:0 0 15px;}
.price-table{width:100%;border-collapse:collapse;table-layout:auto;}
.price-table th,.price-table td{padding:12px;border:1px solid #ddd;text-align:center;border-radius:4px;font-size:14px;}
.price-table th{background:#1abc9c;color:#fff;}
.actions button{margin:0 3px;padding:5px 10px;border:none;border-radius:4px;cursor:pointer;font-size:13px;}
.actions .edit{background:#3498db;color:#fff;}
.actions .delete{background:#e74c3c;color:#fff;}
.search-add{display:flex;justify-content:space-between;margin-bottom:15px;align-items:center;flex-wrap:wrap;gap:10px;}
.search-add input{padding:8px;min-width:160px;border:1px solid #ccc;border-radius:4px;}
.search-add button{background:#1abc9c;color:#fff;padding:8px 12px;border:none;border-radius:4px;cursor:pointer;}
.export-buttons{display:flex;gap:8px;flex-wrap:wrap;}
.export-buttons button{padding:8px 12px;border:none;border-radius:4px;cursor:pointer;color:#fff;font-weight:bold;font-size:13px;}
.export-excel{background:#27ae60;}
.export-excel:hover{background:#229954;}
.export-pdf{background:#e74c3c;}
.export-pdf:hover{background:#c0392b;}
.import-excel{background:#3498db;}
.import-excel:hover{background:#2980b9;}
.import-pdf{background:#9b59b6;}
.import-pdf:hover{background:#8e44ad;}
.modal{display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.6);justify-content:center;align-items:center;}
.modal-content{background:#fff;padding:25px;border-radius:8px;width:90%;max-width:400px;transition:all 0.3s;}
.modal-content h4{margin-top:0;font-size:18px;}
.modal-content input{width:100%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:6px;font-size:14px;}
.modal-content button{padding:10px 14px;border:none;border-radius:8px;cursor:pointer;font-size:14px;}
.close{float:right;font-size:22px;cursor:pointer;}
.hidden{display:none;}

/* ================= Responsive ================= */

/* Mobile: up to 480px */
@media screen and (max-width:480px) {
    .main-content{margin:20px 10px 20px 10px;padding:15px;}
    .price-table th,.price-table td{padding:8px;font-size:12px;}
    .search-add input{width:100%;}
    .modal-content{padding:20px;width:95%;}
}

/* Tablet: up to 768px */
@media screen and (max-width:768px) {
    .main-content{margin:20px 15px 20px 15px;padding:18px;}
    .price-table th,.price-table td{padding:10px;font-size:13px;}
    .modal-content{padding:22px;width:90%;}
}

/* Laptop: up to 1164px */
@media screen and (max-width:1164px) {
    .main-content{margin-left:0;padding:20px;}
    .price-table th,.price-table td{padding:12px;font-size:14px;}
    .modal-content{width:400px;}
}

/* Desktop >1164px tetap default */
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

            <div class="table-wrapper">
    <table class="price-table" id="priceTable">
        <tr>
            <th>No</th>
            <th>Item</th>
            <th>Price</th>
            <th>Stok</th>
            <th>Action</th>
        </tr>
        <?php foreach($produkList as $index => $p): ?>
        <tr data-id="<?= $p['id'] ?>">
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($p['nama_produk']) ?></td>
            <td><?= htmlspecialchars($p['harga']) ?></td>
            <td><?= htmlspecialchars($p['stok']) ?></td>
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
// === Search
document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll("#priceTable tr").forEach((row,index)=>{
        if(index===0) return;
        let text = row.cells[1].textContent.toLowerCase() + ' ' + row.cells[4].textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});

// === Modal functions
function openModal() {
    document.getElementById("modalTitle").innerText="Add New Item";
    document.getElementById("itemId").value="";
    document.getElementById("itemName").value="";
    document.getElementById("itemPrice").value="";
    document.getElementById("itemStock").value="";
    document.getElementById("itemCategory").value="";
    document.getElementById("itemDesc").value="";
    document.getElementById("itemImage").value="";
    document.getElementById("itemModal").style.display="flex";
}
function closeModal() {
    document.getElementById("itemModal").style.display="none";
}
function openEditModal(btn) {
    let row = btn.closest("tr");
    document.getElementById("modalTitle").innerText="Edit Item";
    document.getElementById("itemId").value = row.dataset.id || "";
    document.getElementById("itemName").value = row.cells[1].textContent.trim();
    // Harga cell shows formatted Rp style; remove non-digits
    document.getElementById("itemPrice").value = row.cells[2].textContent.replace(/[^\d]/g,"");
    document.getElementById("itemStock").value = row.cells[3].textContent.trim();
    document.getElementById("itemCategory").value = row.dataset.kat || "";
    document.getElementById("itemDesc").value = row.cells[5].textContent.trim();
    let img = row.cells[6].querySelector("img");
    document.getElementById("itemImage").value = img ? img.src : "";
    document.getElementById("itemModal").style.display="flex";
}

// === Save item (Add/Edit)
function saveItem() {
    let id = document.getElementById("itemId").value;
    let nama = document.getElementById("itemName").value.trim();
    let harga = document.getElementById("itemPrice").value;
    let stok = document.getElementById("itemStock").value || 0;
    let kategori = document.getElementById("itemCategory").value || 0;
    let deskripsi = document.getElementById("itemDesc").value.trim();
    let gambar = document.getElementById("itemImage").value.trim();

    if(!nama || !harga) { alert("Nama dan harga wajib diisi"); return; }

    let formData = new FormData();
    formData.append("id", id);
    formData.append("nama", nama);
    formData.append("harga", harga);
    formData.append("stok", stok);
    formData.append("kategori", kategori);
    formData.append("deskripsi", deskripsi);
    formData.append("gambar", gambar);

    fetch("", {method:"POST", body:formData})
    .then(res=>res.text())
    .then(res=>{
        if(res==="success") location.reload();
        else alert("Error saving data: " + res);
    })
    .catch(err => {
        console.error(err);
        alert("Network error");
    });
}

// === Delete
function deleteItem(btn){
    if(!confirm("Are you sure?")) return;
    let row = btn.closest("tr");
    let id = row.dataset.id;

    let formData = new FormData();
    formData.append("deleteId", id);

    fetch("", {method:"POST", body:formData})
    .then(res=>res.text())
    .then(res=>{
        if(res==="deleted") {
            row.remove();
        } else {
            alert("Error deleting data");
        }
    });
}

// === Export to Excel (client-side)
function exportToExcel() {
    let table = document.getElementById("priceTable");
    let wb = XLSX.utils.book_new();

    // Build 2D array from visible rows
    let data = [];
    let rows = table.querySelectorAll("tr");
    rows.forEach((row, index) => {
        if(index === 0) {
            // header
            let headers = Array.from(row.querySelectorAll("th")).map(h => h.textContent.trim());
            data.push(headers.slice(0, -1)); // drop action column
            return;
        }
        if(row.style.display === "none") return;
        let cells = row.querySelectorAll("td");
        if(cells.length === 0) return;
        let rowData = [
            cells[0].textContent.trim(), // No
            cells[1].textContent.trim(), // Item
            cells[2].textContent.trim().replace(/[^\d]/g, ""), // Price numeric
            cells[3].textContent.trim(), // Stok
            cells[4].textContent.trim(), // Kategori
            cells[5].textContent.trim(), // Deskripsi
            cells[6].querySelector("img") ? cells[6].querySelector("img").src : "" // Gambar URL
        ];
        data.push(rowData);
    });

    let ws = XLSX.utils.aoa_to_sheet(data);
    XLSX.utils.book_append_sheet(wb, ws, "Price List");
    XLSX.writeFile(wb, "price_list.xlsx");
}

// === Export to PDF (client-side)
function exportToPDF() {
    const { jsPDF } = window.jspdf;
    let doc = new jsPDF();
    let table = document.getElementById("priceTable");
    let rows = [];
    let tableRows = table.querySelectorAll("tr");
    tableRows.forEach((row, index) => {
        if(index === 0) {
            // header
            let headers = Array.from(row.querySelectorAll("th")).map(h => h.textContent.trim());
            rows.push(headers.slice(0, -1)); // drop action
            return;
        }
        if(row.style.display === "none") return;
        let cells = row.querySelectorAll("td");
        let rowData = [
            cells[0].textContent.trim(),
            cells[1].textContent.trim(),
            cells[2].textContent.trim(),
            cells[3].textContent.trim(),
            cells[4].textContent.trim(),
            cells[5].textContent.trim(),
            cells[6].querySelector("img") ? "[img]" : ""
        ];
        rows.push(rowData);
    });

    // Use autoTable
    doc.autoTable({
        head: [rows[0]],
        body: rows.slice(1),
        theme: 'grid',
        headStyles: { fillColor: [26, 188, 156] }
    });
    doc.save("price_list.pdf");
}

// === Import Excel (client-side reads file -> sends JSON to server)
function importExcel(input) {
    let file = input.files[0];
    if (!file) return;

    let reader = new FileReader();
    reader.onload = function(e) {
        let data = new Uint8Array(e.target.result);
        let workbook = XLSX.read(data, {type: 'array'});
        let firstSheet = workbook.Sheets[workbook.SheetNames[0]];
        let rows = XLSX.utils.sheet_to_json(firstSheet, {header: 1}); // array of arrays

        // Expect header row, columns: No | Nama | Harga | Stok | Kategori | Deskripsi | Gambar
        let importData = [];
        for (let i = 1; i < rows.length; i++) {
            let r = rows[i];
            if (!r) continue;
            // some columns may be missing; guard index access
            let nama = (r[1] || "").toString().trim();
            let harga = (r[2] || "").toString().replace(/[^\d.]/g, "");
            let stok = (r[3] || "0").toString().replace(/[^\d]/g, "");
            let kategori = (r[4] || "").toString().trim(); // if user wrote category name, not id
            let deskripsi = (r[5] || "").toString().trim();
            let gambar = (r[6] || "").toString().trim();

            if (!nama || !harga) continue;

            // If kategori is a name, try to find its id from select options
            let katId = 0;
            if (kategori) {
                let opt = Array.from(document.querySelectorAll("#itemCategory option")).find(o => o.textContent.trim().toLowerCase() === kategori.toLowerCase());
                if (opt) katId = opt.value;
                else if (!isNaN(parseInt(kategori))) katId = parseInt(kategori);
            }

            importData.push({
                nama: nama,
                harga: parseFloat(harga),
                stok: parseInt(stok) || 0,
                kategori: katId,
                deskripsi: deskripsi,
                gambar: gambar
            });
        }

        if (importData.length === 0) {
            alert("No valid data found in Excel file");
            input.value = '';
            return;
        }

        if (!confirm(`Ditemukan ${importData.length} item. Lanjutkan import?`)) {
            input.value = '';
            return;
        }

        let formData = new FormData();
        formData.append("importExcel", JSON.stringify(importData));

        fetch("", {method:"POST", body:formData})
        .then(res => res.text())
        .then(res => {
            alert(res);
            location.reload();
        })
        .catch(err => { console.error(err); alert("Error uploading"); });

    };
    reader.readAsArrayBuffer(file);
    input.value = '';
}

// === Import PDF (client-side: parse text from PDF pages using pdf.js then build items)
function importPDF(input) {
    let file = input.files[0];
    if (!file) return;
    if (file.type !== 'application/pdf') { alert('Harap upload file PDF yang valid'); input.value = ''; return; }

    let reader = new FileReader();
    reader.onload = function(e) {
        let typedarray = new Uint8Array(e.target.result);
        pdfjsLib.getDocument({data: typedarray}).promise.then(function(pdf) {
            let maxPages = pdf.numPages;
            let promises = [];
            for (let i = 1; i <= maxPages; i++) {
                promises.push(pdf.getPage(i).then(function(page) {
                    return page.getTextContent().then(function(textContent) {
                        // Build lines grouping by y position
                        let items = textContent.items;
                        let lastY = null;
                        let lines = [];
                        let currentLine = '';
                        for (let it of items) {
                            let curY = it.transform[5];
                            if (lastY !== null && Math.abs(lastY - curY) > 5) {
                                if (currentLine.trim()) lines.push(currentLine.trim());
                                currentLine = it.str;
                            } else {
                                currentLine += (currentLine ? ' ' : '') + it.str;
                            }
                            lastY = curY;
                        }
                        if (currentLine.trim()) lines.push(currentLine.trim());
                        return lines.join('\n');
                    });
                }));
            }
            Promise.all(promises).then(function(texts) {
                let allText = texts.join('\n');
                let lines = allText.split('\n');
                let importData = [];
                for (let i = 0; i < lines.length; i++) {
                    let line = lines[i].trim();
                    if (!line) continue;
                    // Skip headers (common words)
                    if (/^(No|Item|Price|Stock|Stok|Action|Harga|Nama)/i.test(line)) continue;
                    // Try parse patterns like: "1 Laptop Gaming 5000000 10"
                    let parts = line.split(/\s+/);
                    if (parts.length >= 4 && /^\d+$/.test(parts[0])) {
                        let stokStr = parts[parts.length - 1];
                        let hargaStr = parts[parts.length - 2];
                        let nama = parts.slice(1, -2).join(' ');
                        let harga = parseFloat(hargaStr.replace(/[^\d.]/g, ''));
                        let stok = parseInt(stokStr.replace(/[^\d]/g, ''));
                        if (nama && !isNaN(harga) && harga > 0) {
                            importData.push({ nama: nama, harga: harga, stok: isNaN(stok) ? 0 : stok, kategori: 0, deskripsi: '', gambar: '' });
                        }
                    } else {
                        // Try other heuristics: maybe "Laptop Gaming - 5.000.000 - stok 10"
                        let m = line.match(/(.+)[\-\|,]\s*([\d\.,]+)[^\d]*?(\d+)$/);
                        if (m) {
                            let nama = m[1].trim();
                            let harga = parseFloat(m[2].replace(/[^\d.]/g,''));
                            let stok = parseInt(m[3].replace(/[^\d]/g,''));
                            if (nama && !isNaN(harga)) importData.push({ nama, harga, stok: isNaN(stok)?0:stok, kategori:0, deskripsi:'', gambar:'' });
                        }
                    }
                }

                if (importData.length === 0) {
                    alert("Tidak ada data valid yang ditemukan di file PDF.\nFormat yang didukung: No Nama Harga Stok (per baris).");
                    input.value = '';
                    return;
                }

                if (!confirm(`Ditemukan ${importData.length} item. Lanjutkan import?`)) {
                    input.value = '';
                    return;
                }

                let formData = new FormData();
                formData.append("importPDF", JSON.stringify(importData));
                fetch("", {method:"POST", body:formData})
                .then(res => res.text())
                .then(res => { alert(res); location.reload(); })
                .catch(err => { console.error(err); alert('Gagal mengupload data: ' + err.message); });

            }).catch(err => {
                console.error('Parse pages error', err);
                alert('Gagal memproses PDF');
            });
        }).catch(err => {
            console.error('Load PDF error', err);
            alert('Gagal memuat file PDF.');
        });
    };
    reader.readAsArrayBuffer(file);
    input.value = '';
}

// PDF.js worker
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
</script>

</html>