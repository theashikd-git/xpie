<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role']!='admin'){
    header("Location: login.php");
    exit;
}
/* ================= AJAX UPDATE ================= */
if(isset($_POST['action']) && $_POST['action']=='update'){
    $id = $_POST['id'];

    mysqli_query($conn, "UPDATE assets SET 
        product_name='{$_POST['product_name']}',
        category='{$_POST['category']}',
        quantity='{$_POST['quantity']}',
        department='{$_POST['department']}',
        place='{$_POST['place']}',
        warranty_start='{$_POST['warranty_start']}',
        warranty_end='{$_POST['warranty_end']}',
        note='{$_POST['note']}'
        WHERE id=$id");

    echo json_encode($_POST);
    exit;
}

/* ================= AJAX DELETE ================= */
if(isset($_GET['action']) && $_GET['action']=='delete'){
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM assets WHERE id=$id");
    echo json_encode(['status'=>'success']);
    exit;
}

/* ================= ADD ASSET ================= */
if(isset($_POST['save_asset'])){
    mysqli_query($conn, "INSERT INTO assets 
    (product_name, category, quantity, department, place, warranty_start, warranty_end, note)
    VALUES
    ('{$_POST['product_name']}', '{$_POST['category']}', '{$_POST['quantity']}', '{$_POST['department']}', '{$_POST['place']}', '{$_POST['warranty_start']}', '{$_POST['warranty_end']}', '{$_POST['note']}')");

    header("Location: assets.php");
    exit;
}
/* ================= ADD CATEGORY ================= */
if(isset($_POST['save_category'])){
    mysqli_query($conn,"INSERT INTO categories(name) VALUES('{$_POST['new_category']}')");
    header("Location: assets.php");
    exit;
}

/* ================= ADD DEPARTMENT ================= */
if(isset($_POST['save_department'])){
    mysqli_query($conn,"INSERT INTO departments(name) VALUES('{$_POST['new_department']}')");
    header("Location: assets.php");
    exit;
}

/* ================= ADD PLACE ================= */
if(isset($_POST['save_place'])){
    mysqli_query($conn,"INSERT INTO places(name) VALUES('{$_POST['new_place']}')");
    header("Location: assets.php");
    exit;
}
/* ================= FETCH ================= */
$assets = mysqli_query($conn, "SELECT * FROM assets ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Asset Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">NAMIAS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="admin_assets.php">Asset Management</a></li>
		<li class="nav-item"><a class="nav-link " href="admin_assets_report.php">Asset Report</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_ip_allocation.php">IP Allocation</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_wishlist.php">Wishlist</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_panel.php">Admin Panel</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Asset Management</h3>
  <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addForm">
    ➕ Add Asset
  </button>
</div>

<!-- ADD FORM -->
<div class="collapse mb-4" id="addForm">
<form method="POST" class="card p-3 shadow-sm">

<div class="row mb-3">
  <div class="col-md-4">
    <input type="text" name="product_name" class="form-control" placeholder="Product Name" required>
  </div>

  <div class="col-md-4">
    <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
  </div>

  <div class="col-md-4">
    <input type="date" name="warranty_start" class="form-control">
  </div>
</div>

<!-- DROPDOWNS -->
<div class="row mb-3">

<!-- CATEGORY -->
<div class="col-md-4">
  <label>Category</label>
  <div class="d-flex">
    <select name="category" class="form-select me-2" required>
      <option value="">Select</option>
      <?php
      $cat = mysqli_query($conn,"SELECT name FROM categories");
      while($c=mysqli_fetch_assoc($cat)){
        echo "<option>{$c['name']}</option>";
      }
      ?>
    </select>
    <button type="button" class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#catBox">➕</button>
  </div>

  <div class="collapse mt-2" id="catBox">
    <div class="d-flex">
      <input type="text" name="new_category" class="form-control me-2" placeholder="New Category">
      <button class="btn btn-success" name="save_category" formnovalidate>Save</button>
    </div>
  </div>
</div>

<!-- DEPARTMENT -->
<div class="col-md-4">
  <label>Department</label>
  <div class="d-flex">
    <select name="department" class="form-select me-2" required>
      <option value="">Select</option>
      <?php
      $dep = mysqli_query($conn,"SELECT name FROM departments");
      while($d=mysqli_fetch_assoc($dep)){
        echo "<option>{$d['name']}</option>";
      }
      ?>
    </select>
    <button type="button" class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#depBox">➕</button>
  </div>

  <div class="collapse mt-2" id="depBox">
    <div class="d-flex">
      <input type="text" name="new_department" class="form-control me-2" placeholder="New Department">
      <button class="btn btn-success" name="save_department" formnovalidate>Save</button>
    </div>
  </div>
</div>

<!-- PLACE -->
<div class="col-md-4">
  <label>Place</label>
  <div class="d-flex">
    <select name="place" class="form-select me-2" required>
      <option value="">Select</option>
      <?php
      $pl = mysqli_query($conn,"SELECT name FROM places");
      while($p=mysqli_fetch_assoc($pl)){
        echo "<option>{$p['name']}</option>";
      }
      ?>
    </select>
    <button type="button" class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#placeBox">➕</button>
  </div>

  <div class="collapse mt-2" id="placeBox">
    <div class="d-flex">
      <input type="text" name="new_place" class="form-control me-2" placeholder="New Place">
      <button class="btn btn-success" name="save_place" formnovalidate>Save</button>
    </div>
  </div>
</div>

</div>

<div class="row mb-3">
  <div class="col-md-6">
    <input type="date" name="warranty_end" class="form-control">
  </div>

  <div class="col-md-6">
    <textarea name="note" class="form-control" placeholder="Note"></textarea>
  </div>
</div>

<button class="btn btn-success" name="save_asset">Save Asset</button>

</form>
</div>

<!-- TABLE -->
<table class="table table-striped table-bordered">
<thead class="table-dark">
<tr>
  <th>ID</th>
  <th>Product</th>
  <th>Category</th>
  <th>Qty</th>
  <th>Department</th>
  <th>Place</th>
  <th>W.Start</th>
  <th>W.End</th>
  <th>Note</th>
  <th>Action</th>
</tr>
</thead>

<tbody>
<?php while($a=mysqli_fetch_assoc($assets)){ ?>
<tr id="row<?= $a['id'] ?>">
  <td><?= $a['id'] ?></td>
  <td><?= $a['product_name'] ?></td>
  <td><?= $a['category'] ?></td>
  <td><?= $a['quantity'] ?></td>
  <td><?= $a['department'] ?></td>
  <td><?= $a['place'] ?></td>
  <td><?= $a['warranty_start'] ?></td>
  <td><?= $a['warranty_end'] ?></td>
  <td><?= $a['note'] ?></td>
  <td>
    <button class="btn btn-warning btn-sm edit-btn" data-id="<?= $a['id'] ?>">Edit</button>
    <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $a['id'] ?>">Delete</button>
  </td>
</tr>
<?php } ?>
</tbody>
</table>

</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal">
<div class="modal-dialog">
<div class="modal-content">

<form id="editForm">
<div class="modal-header">
  <h5>Edit Asset</h5>
  <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
  <input type="hidden" name="id" id="edit_id">

  <input class="form-control mb-2" name="product_name" id="edit_product">
  <input class="form-control mb-2" name="category" id="edit_category">
  <input class="form-control mb-2" name="quantity" id="edit_qty">
  <input class="form-control mb-2" name="department" id="edit_department">
  <input class="form-control mb-2" name="place" id="edit_place">
  <input type="date" class="form-control mb-2" name="warranty_start" id="edit_ws">
  <input type="date" class="form-control mb-2" name="warranty_end" id="edit_we">
  <textarea class="form-control" name="note" id="edit_note"></textarea>
</div>

<div class="modal-footer">
  <button class="btn btn-success">Update</button>
</div>
</form>

</div>
</div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){

$('.edit-btn').click(function(){
  var row = $(this).closest('tr');

  $('#edit_id').val($(this).data('id'));
  $('#edit_product').val(row.find('td:eq(1)').text());
  $('#edit_category').val(row.find('td:eq(2)').text());
  $('#edit_qty').val(row.find('td:eq(3)').text());
  $('#edit_department').val(row.find('td:eq(4)').text());
  $('#edit_place').val(row.find('td:eq(5)').text());
  $('#edit_ws').val(row.find('td:eq(6)').text());
  $('#edit_we').val(row.find('td:eq(7)').text());
  $('#edit_note').val(row.find('td:eq(8)').text());

  new bootstrap.Modal(document.getElementById('editModal')).show();
});

$('#editForm').submit(function(e){
  e.preventDefault();

  $.post('', $(this).serialize()+'&action=update', function(res){
    var data = JSON.parse(res);
    var row = $('#row'+data.id);

    row.find('td:eq(1)').text(data.product_name);
    row.find('td:eq(2)').text(data.category);
    row.find('td:eq(3)').text(data.quantity);
    row.find('td:eq(4)').text(data.department);
    row.find('td:eq(5)').text(data.place);
    row.find('td:eq(6)').text(data.warranty_start);
    row.find('td:eq(7)').text(data.warranty_end);
    row.find('td:eq(8)').text(data.note);

    bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
  });
});

$('.delete-btn').click(function(){
  if(!confirm('Delete this asset?')) return;

  var id = $(this).data('id');
  var row = $('#row'+id);

  $.get('', {action:'delete', id:id}, function(){
    row.remove();
  });
});

});
</script>

</body>
</html>
