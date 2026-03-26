<?php
include 'db.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role']!='user'){
    header("Location: login.php");
    exit;
}
/* ================= IP RANGE FUNCTION ================= */
function ipInRange($ip, $cidrMask) {

    $ip_long = ip2long($ip);

    if ($ip_long === false) {
        return false;
    }

    $mask = (int)$cidrMask;

    if ($mask < 0 || $mask > 32) {
        return false;
    }

    $mask_long = -1 << (32 - $mask);

    $network = $ip_long & $mask_long;
    $broadcast = $network | (~$mask_long);

    return ($ip_long > $network && $ip_long < $broadcast);
}


/* ================= SAVE IP ================= */
if(isset($_POST['save_ip'])){

    $ip_address = $_POST['ip_address'];
    $cidr       = $_POST['cidr'];
    $vlan_id    = $_POST['vlan_id'];
    $vlan_name  = $_POST['vlan_name'];
    $purpose    = $_POST['purpose'];
    $description= $_POST['description'];

    // Validate IP format
    if (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
        echo "<script>alert('Invalid IP format!');</script>";
        exit;
    }

    // Validate CIDR range
    if (!ipInRange($ip_address, $cidr)) {
        echo "<script>alert('IP Address does NOT belong to this CIDR range!');</script>";
        exit;
    }

    // Check duplicate IP
    $check_ip = mysqli_query($conn, 
        "SELECT * FROM ip_allocations WHERE ip_address='$ip_address'");

    if(mysqli_num_rows($check_ip) > 0){
        echo "<script>alert('IP already assigned!');</script>";
    } else {

        // Check duplicate VLAN ID
        $check_vlan = mysqli_query($conn, 
            "SELECT * FROM ip_allocations WHERE vlan_id='$vlan_id'");

        if(mysqli_num_rows($check_vlan) > 0){
            echo "<script>alert('VLAN ID already exists!');</script>";
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
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link " href="assets.php">Asset Management</a></li>
		<li class="nav-item"><a class="nav-link " href="assets_report.php">Asset Report</a></li>
        <li class="nav-item"><a class="nav-link active" href="ip_allocation.php">IP Allocation</a></li>
        <li class="nav-item"><a class="nav-link" href="wishlist.php">Wishlist</a></li>
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

<button type="submit" name="save_ip" class="btn btn-success">
    Save IP
</button>

</form>
</div>
</div>

<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>IP Address</th>
    <th>CIDR</th>
    <th>VLAN ID</th>
    <th>VLAN Name</th>
    <th>Purpose</th>
    <th>Description</th>
</tr>
</thead>
<tbody>
<?php while($row=mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['ip_address'] ?></td>
    <td><?= $row['cidr'] ?></td>
    <td><?= $row['vlan_id'] ?></td>
    <td><?= $row['vlan_name'] ?></td>
    <td><?= $row['purpose'] ?></td>
    <td><?= $row['description'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
