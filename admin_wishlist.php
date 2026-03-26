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

    mysqli_query($conn,"UPDATE wishlist SET
        product_name='{$_POST['product_name']}',
        brand='{$_POST['brand']}',
        quantity='{$_POST['quantity']}',
        desired_department='{$_POST['desired_department']}',
        details='{$_POST['details']}',
        status='{$_POST['status']}'
        WHERE id=$id");

    echo json_encode($_POST);
    exit;
}

/* ================= AJAX DELETE ================= */
if(isset($_GET['action']) && $_GET['action']=='delete'){
    $id = $_GET['id'];
    mysqli_query($conn,"DELETE FROM wishlist WHERE id=$id");
    echo json_encode(['status'=>'success']);
    exit;
}

/* ================= SAVE ITEM ================= */
if(isset($_POST['save_item'])){
    mysqli_query($conn,"INSERT INTO wishlist
    (product_name,brand,quantity,desired_department,details,status)
    VALUES
    ('{$_POST['product_name']}','{$_POST['brand']}','{$_POST['quantity']}','{$_POST['desired_department']}','{$_POST['details']}','Pending')");

    header("Location: wishlist.php");
    exit;
}

/* ================= FETCH ================= */
$result = mysqli_query($conn,"SELECT * FROM wishlist ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Wishlist</title>
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
        <li class="nav-item"><a class="nav-link " href="admin_assets.php">Asset Management</a></li>
		<li class="nav-item"><a class="nav-link " href="admin_assets_report.php">Asset Report</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_ip_allocation.php">IP Allocation</a></li>
        <li class="nav-item"><a class="nav-link active" href="admin_wishlist.php">Wishlist</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_panel.php">Admin Panel</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">

<div class="d-flex justify-content-between mb-3">
<h3>Wishlist</h3>
<button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addForm">➕ Add</button>
</div>

<!-- ADD FORM -->
<div class="collapse mb-4" id="addForm">
<form method="POST" class="card p-3">

<div class="row">
<div class="col-md-6 mb-2">
<input type="text" name="product_name" class="form-control" placeholder="Product Name" required>
</div>

<div class="col-md-6 mb-2">
<input type="text" name="brand" class="form-control" placeholder="Brand">
</div>
</div>

<div class="row">
<div class="col-md-6 mb-2">
<input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
</div>

<div class="col-md-6 mb-2">
<input type="text" name="desired_department" class="form-control" placeholder="Department" required>
</div>
</div>

<div class="mb-2">
<textarea name="details" class="form-control" placeholder="Details"></textarea>
</div>

<button class="btn btn-success" name="save_item">Save</button>

</form>
</div>

<!-- TABLE -->
<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
<th>ID</th>
<th>Product</th>
<th>Brand</th>
<th>Qty</th>
<th>Department</th>
<th>Details</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php while($row=mysqli_fetch_assoc($result)){ ?>
<tr id="row<?= $row['id'] ?>">
<td><?= $row['id'] ?></td>
<td><?= $row['product_name'] ?></td>
<td><?= $row['brand'] ?></td>
<td><?= $row['quantity'] ?></td>
<td><?= $row['desired_department'] ?></td>
<td><?= $row['details'] ?></td>
<td><?= $row['status'] ?></td>
<td>
<button class="btn btn-warning btn-sm edit-btn" data-id="<?= $row['id'] ?>">Edit</button>
<button class="btn btn-danger btn-sm delete-btn" data-id="<?= $row['id'] ?>">Delete</button>
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
<h5>Edit Wishlist</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="hidden" name="id" id="edit_id">

<input class="form-control mb-2" name="product_name" id="edit_product">
<input class="form-control mb-2" name="brand" id="edit_brand">
<input class="form-control mb-2" name="quantity" id="edit_qty">
<input class="form-control mb-2" name="desired_department" id="edit_dep">
<textarea class="form-control mb-2" name="details" id="edit_details"></textarea>

<select class="form-select" name="status" id="edit_status">
<option>Pending</option>
<option>Approved</option>
<option>Ordered</option>
</select>
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

// EDIT
$('.edit-btn').click(function(){
var row=$(this).closest('tr');

$('#edit_id').val($(this).data('id'));
$('#edit_product').val(row.find('td:eq(1)').text());
$('#edit_brand').val(row.find('td:eq(2)').text());
$('#edit_qty').val(row.find('td:eq(3)').text());
$('#edit_dep').val(row.find('td:eq(4)').text());
$('#edit_details').val(row.find('td:eq(5)').text());
$('#edit_status').val(row.find('td:eq(6)').text());

new bootstrap.Modal(document.getElementById('editModal')).show();
});

// UPDATE
$('#editForm').submit(function(e){
e.preventDefault();

$.post('', $(this).serialize()+'&action=update', function(res){
var data=JSON.parse(res);
var row=$('#row'+data.id);

row.find('td:eq(1)').text(data.product_name);
row.find('td:eq(2)').text(data.brand);
row.find('td:eq(3)').text(data.quantity);
row.find('td:eq(4)').text(data.desired_department);
row.find('td:eq(5)').text(data.details);
row.find('td:eq(6)').text(data.status);

bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
});
});

// DELETE
$('.delete-btn').click(function(){
if(!confirm('Delete item?')) return;

var id=$(this).data('id');
var row=$('#row'+id);

$.get('',{action:'delete',id:id},function(){
row.remove();
});
});

});
</script>

</body>
</html>