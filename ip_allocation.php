<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
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
    $ip_address  = trim($_POST['ip_address']);
    $cidr        = trim($_POST['cidr']);
    $vlan_id     = trim($_POST['vlan_id']);
    $vlan_name   = trim($_POST['vlan_name']);
    $purpose     = trim($_POST['purpose']);
    $description = trim($_POST['description']);

    if (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
        $_SESSION['message'] = "❌ Invalid IP format!";
        header("Location: admin_ip_allocation.php");
        exit;
    }

    $check_ip = mysqli_query($conn,"SELECT id FROM ip_allocations WHERE ip_address='$ip_address'");
    if(mysqli_num_rows($check_ip) > 0){
        $_SESSION['message'] = "⚠️ IP already exists!";
        header("Location: admin_ip_allocation.php");
        exit;
    }

    $check_vlan = mysqli_query($conn,"SELECT id FROM ip_allocations WHERE vlan_id='$vlan_id'");
    if(mysqli_num_rows($check_vlan) > 0){
        $_SESSION['message'] = "⚠️ VLAN already exists!";
        header("Location: admin_ip_allocation.php");
        exit;
    }

    mysqli_query($conn,"INSERT INTO ip_allocations
        (ip_address,cidr,vlan_id,vlan_name,purpose,description)
        VALUES
        ('$ip_address','$cidr','$vlan_id','$vlan_name','$purpose','$description')");

    $_SESSION['message'] = "✅ IP saved successfully!";
    header("Location: admin_ip_allocation.php");
    exit;
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

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="admin_dashboard.php">Xpie</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="assets.php">Asset Management</a></li>
        <li class="nav-item"><a class="nav-link" href="assets_report.php">Asset Report</a></li>
        <li class="nav-item"><a class="nav-link active" href="ip_allocation.php">IP Allocation</a></li>
        <li class="nav-item"><a class="nav-link" href="wishlist.php">Wishlist</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">

<?php
if(!empty($_SESSION['message'])){
    echo "<div class='alert alert-info'>".$_SESSION['message']."</div>";
    unset($_SESSION['message']);
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>IP Allocation</h3>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addIPForm">➕ Add IP</button>
</div>

<div class="collapse mb-4" id="addIPForm">
<div class="card p-3 shadow-sm">
<form method="POST">
<div class="row mb-3">
    <div class="col-md-4">
        <label>IP Address</label>
        <input type="text" name="ip_address" class="form-control" required>
    </div>
    <div class="col-md-2">
        <label>CIDR</label>
        <input type="number" name="cidr" class="form-control" placeholder="24" required>
    </div>
    <div class="col-md-3">
        <label>VLAN ID</label>
        <input type="number" name="vlan_id" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label>VLAN Name</label>
        <input type="text" name="vlan_name" class="form-control" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label>Usage Purpose</label>
        <input type="text" name="purpose" class="form-control" placeholder="e.g. Nurse Calling / WIFI">
    </div>
    <div class="col-md-6">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="2"></textarea>
    </div>
</div>
<button type="submit" name="save_ip" class="btn btn-success">Save IP</button>
</form>
</div>
</div>

<table class="table table-striped table-bordered">
<thead class="table-dark">
<tr>
    <th>ID</th><th>IP</th><th>CIDR</th><th>VLAN</th>
    <th>Name</th><th>Purpose</th><th>Description</th><th>Action</th>
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
    <input class="form-control mb-2" name="ip_address" id="edit_ip" placeholder="IP Address">
    <input class="form-control mb-2" name="cidr" id="edit_cidr" placeholder="CIDR">
    <input class="form-control mb-2" name="vlan_id" id="edit_vlan_id" placeholder="VLAN ID">
    <input class="form-control mb-2" name="vlan_name" id="edit_vlan_name" placeholder="VLAN Name">
    <input class="form-control mb-2" name="purpose" id="edit_purpose" placeholder="Purpose">
    <textarea class="form-control" name="description" id="edit_description" placeholder="Description"></textarea>
</div>
<div class="modal-footer">
    <button class="btn btn-success">Update</button>
</div>
</form>
</div>
</div>
</div>

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
        $.get('', {action:'delete', id:id}, function(){
            $('#row'+id).remove();
        });
    });
});
</script>
</body>
</html>
