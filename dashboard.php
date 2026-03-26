<?php
include 'db.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role']!='user'){
    header("Location: login.php");
    exit;
}
/* ================= ASSET OVERVIEW ================= */
$assets = mysqli_query($conn,"
SELECT product_name, department, SUM(quantity) as total_qty
FROM assets
GROUP BY product_name, department
ORDER BY product_name ASC
LIMIT 5
");

/* ================= IP ALLOCATION ================= */
$ips = mysqli_query($conn,"
SELECT ip_address, cidr, vlan_id, vlan_name
FROM ip_allocations
ORDER BY id DESC
LIMIT 5
");

/* ================= WISHLIST ================= */
$wishlist = mysqli_query($conn,"
SELECT product_name, brand, quantity, desired_department
FROM wishlist
ORDER BY id DESC
LIMIT 5
");

/* ================= RECENT ASSETS ================= */
$recent_assets = mysqli_query($conn,"
SELECT product_name, created_at
FROM assets
ORDER BY created_at DESC
LIMIT 5
");

/* ================= WARRANTY ================= */
$warranty = mysqli_query($conn,"
SELECT product_name, warranty_end
FROM assets
WHERE warranty_end IS NOT NULL
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>XPie Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background-color: #f4f6f9;
    overflow-x: hidden;
}

/* Sidebar */
.sidebar {
    min-height: 100vh;
    background: #1f2d3d;
}
.sidebar h4 {
    color: #fff;
}
.sidebar a {
    color: #c2c7d0;
    padding: 12px 20px;
    display: block;
    font-size: 15px;
	text-decoration:none;
}
.sidebar a:hover {
    background: #343a40;
    color: #fff;
	text-decoration:none;
}

/* Cards */
.card {
    border: none;
    border-radius: 10px;
}
.card-header {
    background: #ffffff;
    font-weight: 600;
    border-bottom: 1px solid #e9ecef;
}
.card-body {
    font-size: 14px;
}
.item-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 6px;
}
.small-text {
    font-size: 13px;
    color: #6c757d;
}
.sidebar-logout {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #ff6b6b;
    font-weight: 600;
    text-decoration: none;
    border-top: 1px solid #343a40;
    transition: background 0.2s, color 0.2s;
}

.sidebar-logout:hover {
    background: #343a40;
    color: #ff4c4c;
}

.sidebar-logout svg {
    fill: currentColor;
   
</style>
</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- Sidebar -->
<div class="col-md-2 sidebar p-0 d-flex flex-column">
    <h4 class="text-center py-3 border-bottom">XPie</h4>
    <a href="dashboard.php">Dashboard</a>
    <a href="assets.php">Asset Management</a>
    <a href="assets_report.php">Asset Report</a>
    <a href="ip_allocation.php">IP Allocation</a>
    <a href="wishlist.php">Wishlist</a>

    <!-- Spacer -->
    <div class="mt-auto"></div>

<!-- Sidebar Logout -->
<a href="logout.php" class="sidebar-logout">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right me-2" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M6 3a.5.5 0 0 1 .5.5v2.5H13a.5.5 0 0 1 0 1H6.5V10a.5.5 0 0 1-1 0V3.5A.5.5 0 0 1 6 3zm-1 4.5a.5.5 0 0 0-.5-.5H2.5a.5.5 0 0 0 0 1H4.5a.5.5 0 0 0 .5-.5z"/>
    </svg>
    Logout
</a>
</div>

<!-- Main Content -->
<div class="col-md-10 p-4">

<h4 class="mb-4">Dashboard Overview</h4>

<!-- Top Row -->
<div class="row g-4">

<!-- Asset Overview -->
<div class="col-md-6">
<div class="card h-100">
<div class="card-header">Asset Overview</div>
<div class="card-body">
<?php while($row=mysqli_fetch_assoc($assets)){ ?>
<div class="item-row">
<div>
<strong><?= $row['product_name'] ?></strong>
<div class="small-text"><?= $row['department'] ?></div>
</div>
<div>Qty: <?= $row['total_qty'] ?></div>
</div>
<?php } ?>
</div>
</div>
</div>

<!-- IP Allocation -->
<div class="col-md-6">
<div class="card h-100">
<div class="card-header">IP Allocation</div>
<div class="card-body">
<?php while($row=mysqli_fetch_assoc($ips)){ ?>
<div class="item-row">
<div>
<strong><?= $row['ip_address'] ?>/<?= $row['cidr'] ?></strong>
<div class="small-text">VLAN <?= $row['vlan_id'] ?> (<?= $row['vlan_name'] ?>)</div>
</div>
</div>
<?php } ?>
</div>
</div>
</div>

</div>

<!-- Middle Row -->
<div class="row g-4 mt-1">

<!-- Recently Added -->
<div class="col-md-6">
<div class="card h-100">
<div class="card-header">Recently Added</div>
<div class="card-body">
<?php while($row=mysqli_fetch_assoc($recent_assets)){ ?>
<div class="item-row">
<strong><?= $row['product_name'] ?></strong>
<div class="small-text">
<?= date("d M Y", strtotime($row['created_at'])) ?>
</div>
</div>
<?php } ?>
</div>
</div>
</div>

<!-- Warranty -->
<div class="col-md-6">
<div class="card h-100">
<div class="card-header">Warranty Remaining</div>
<div class="card-body">
<?php 
$today = new DateTime();
while($row=mysqli_fetch_assoc($warranty)){ 
$expiry = new DateTime($row['warranty_end']);
if($expiry < $today) continue;
$interval = $today->diff($expiry);
$remaining = ($interval->m > 0)
? $interval->m . " month " . $interval->d . " days"
: $interval->d . " days";
?>
<div class="item-row">
<strong><?= $row['product_name'] ?></strong>
<span class="text-danger small-text"><?= $remaining ?> left</span>
</div>
<?php } ?>
</div>
</div>
</div>

</div>

<!-- Bottom Row -->
<div class="row g-4 mt-1">
<div class="col-md-12">
<div class="card">
<div class="card-header">Wishlist Requests</div>
<div class="card-body">

<table class="table table-sm mb-0">
    <thead>
        <tr>
            <th>Product</th>
            <th>Brand</th>
            <th>Qty</th>
            <th>Department</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row=mysqli_fetch_assoc($wishlist)){ ?>
        <tr>
            <td><?= $row['product_name'] ?></td>
            <td><?= $row['brand'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= $row['desired_department'] ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

</div>
</div>
</div>
</div>

</div>
</div>
</div>

</body>
</html>
