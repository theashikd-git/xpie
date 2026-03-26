<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role']!='admin'){
    header("Location: login.php");
    exit;
}
/* ================= IP RANGE FUNCTION ================= */
function ipInRange($ip, $cidrMask) {
    $ip_long = ip2long($ip);
    if ($ip_long === false) return false;

    $mask = (int)$cidrMask;
    if ($mask < 0 || $mask > 32) return false;

    $mask_long = -1 << (32 - $mask);
    $network = $ip_long & $mask_long;
    $broadcast = $network | (~$mask_long);

    return ($ip_long > $network && $ip_long < $broadcast);
}

/* ================= AJAX UPDATE ================= */
if(isset($_POST['action']) && $_POST['action']=='update'){
    $id = $_POST['id'];

    mysqli_query($conn,"UPDATE ip_allocations SET 
        ip_address='{$_POST['ip_address']}',
        cidr='{$_POST['cidr']}',
        vlan_id='{$_POST['vlan_id']}',
        vlan_name='{$_POST['vlan_name']}',
        purpose='{$_POST['purpose']}',
        description='{$_POST['description']}'
        WHERE id=$id");

    echo json_encode($_POST);
    exit;
}

/* ================= AJAX DELETE ================= */
if(isset($_GET['action']) && $_GET['action']=='delete'){
    $id = $_GET['id'];
    mysqli_query($conn,"DELETE FROM ip_allocations WHERE id=$id");
    echo json_encode(['status'=>'success']);
    exit;
}

/* ================= SAVE IP ================= */
if(isset($_POST['save_ip'])){
    $ip_address = $_POST['ip_address'];
    $cidr       = $_POST['cidr'];
    $vlan_id    = $_POST['vlan_id'];
    $vlan_name  = $_POST['vlan_name'];
    $purpose    = $_POST['purpose'];
    $description= $_POST['description'];

    if (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
        echo "<script>alert('Invalid IP format!');</script>";
    } elseif (!ipInRange($ip_address, $cidr)) {
        echo "<script>alert('IP not in CIDR range!');</script>";
    } else {
        $check = mysqli_query($conn,"SELECT * FROM ip_allocations WHERE ip_address='$ip_address'");
        if(mysqli_num_rows($check)>0){
            echo "<script>alert('IP already exists!');</script>";
        } else {
            mysqli_query($conn,"INSERT INTO ip_allocations
            (ip_address,cidr,vlan_id,vlan_name,purpose,description)
            VALUES
            ('$ip_address','$cidr','$vlan_id','$vlan_name','$purpose','$description')");
            header("Location: ip_allocation.php");
            exit();
        }
    }
}

/* ================= FETCH ================= */
$result = mysqli_query($conn,"SELECT * FROM ip_allocations ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>IP Allocation</title>
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
        <li class="nav-item"><a class="nav-link" href="ammin_dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link " href="admin_assets.php">Asset Management</a></li>
		<li class="nav-item"><a class="nav-link " href="admin_assets_report.php">Asset Report</a></li>
        <li class="nav-item"><a class="nav-link active" href="admin_ip_allocation.php">IP Allocation</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_wishlist.php">Wishlist</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_panel.php">Admin Panel</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>IP Allocation</h3>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addIPForm">
        ➕ Add IP
    </button>
</div>

<!-- ADD FORM -->
<div class="collapse mb-4" id="addIPForm">
<div class="card p-3 shadow-sm">
<form method="POST">

<div class="row mb-3">
    <div class="col-md-4">
        <input type="text" name="ip_address" class="form-control" placeholder="IP Address" required>
    </div>
    <div class="col-md-2">
        <input type="number" name="cidr" class="form-control" placeholder="CIDR" required>
    </div>
    <div class="col-md-3">
        <input type="number" name="vlan_id" class="form-control" placeholder="VLAN ID" required>
    </div>
    <div class="col-md-3">
        <input type="text" name="vlan_name" class="form-control" placeholder="VLAN Name" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <input type="text" name="purpose" class="form-control" placeholder="Purpose">
    </div>
    <div class="col-md-6">
        <textarea name="description" class="form-control" placeholder="Description"></textarea>
    </div>
</div>

<button type="submit" name="save_ip" class="btn btn-success">Save IP</button>

</form>
</div>
</div>

<!-- TABLE -->
<table class="table table-striped table-bordered">
<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>IP</th>
    <th>CIDR</th>
    <th>VLAN</th>
    <th>Name</th>
    <th>Purpose</th>
    <th>Description</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php while($row=mysqli_fetch_assoc($result)){ ?>
<tr id="row<?= $row['id'] ?>">
    <td><?= $row['id'] ?></td>
    <td><?= $row['ip_address'] ?></td>
    <td><?= $row['cidr'] ?></td>
    <td><?= $row['vlan_id'] ?></td>
    <td><?= $row['vlan_name'] ?></td>
    <td><?= $row['purpose'] ?></td>
    <td><?= $row['description'] ?></td>
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
    <h5>Edit IP</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <input type="hidden" name="id" id="edit_id">

    <input class="form-control mb-2" name="ip_address" id="edit_ip">
    <input class="form-control mb-2" name="cidr" id="edit_cidr">
    <input class="form-control mb-2" name="vlan_id" id="edit_vlan_id">
    <input class="form-control mb-2" name="vlan_name" id="edit_vlan_name">
    <input class="form-control mb-2" name="purpose" id="edit_purpose">
    <textarea class="form-control" name="description" id="edit_description"></textarea>
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
    $('#edit_ip').val(row.find('td:eq(1)').text());
    $('#edit_cidr').val(row.find('td:eq(2)').text());
    $('#edit_vlan_id').val(row.find('td:eq(3)').text());
    $('#edit_vlan_name').val(row.find('td:eq(4)').text());
    $('#edit_purpose').val(row.find('td:eq(5)').text());
    $('#edit_description').val(row.find('td:eq(6)').text());

    new bootstrap.Modal(document.getElementById('editModal')).show();
});

$('#editForm').submit(function(e){
    e.preventDefault();

    $.post('', $(this).serialize()+'&action=update', function(res){
        var data = JSON.parse(res);
        var row = $('#row'+data.id);

        row.find('td:eq(1)').text(data.ip_address);
        row.find('td:eq(2)').text(data.cidr);
        row.find('td:eq(3)').text(data.vlan_id);
        row.find('td:eq(4)').text(data.vlan_name);
        row.find('td:eq(5)').text(data.purpose);
        row.find('td:eq(6)').text(data.description);

        bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
    });
});

$('.delete-btn').click(function(){
    if(!confirm('Delete this IP?')) return;

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