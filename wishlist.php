<?php
include 'db.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role']!='user'){
    header("Location: login.php");
    exit;
}
/* ================= SAVE ITEM ================= */
if(isset($_POST['save_item'])){

    $product_name       = mysqli_real_escape_string($conn, $_POST['product_name']);
    $brand              = mysqli_real_escape_string($conn, $_POST['brand']);
    $quantity           = (int)$_POST['quantity'];
    $desired_department = mysqli_real_escape_string($conn, $_POST['desired_department']);
    $details            = mysqli_real_escape_string($conn, $_POST['details']);

    if($quantity < 1){
        echo "<script>alert('Quantity must be at least 1');</script>";
    } else {

        mysqli_query($conn,"INSERT INTO wishlist
        (product_name, brand, quantity, desired_department, details)
        VALUES
        ('$product_name','$brand','$quantity','$desired_department','$details')");

        header("Location: wishlist.php");
        exit();
    }
}

/* ================= FETCH DATA ================= */
$result = mysqli_query($conn,"SELECT * FROM wishlist ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Wishlist</title>
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
        <li class="nav-item"><a class="nav-link" href="ip_allocation.php">IP Allocation</a></li>
        <li class="nav-item"><a class="nav-link active" href="wishlist.php">Wishlist</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_panel.php">Admin Panel</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Wishlist Items</h3>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addWishlistForm">
        ➕ Add Item
    </button>
</div>

<!-- Add Form -->
<div class="collapse mb-4" id="addWishlistForm">
    <div class="card card-body">
        <h5>Add New Wishlist Item</h5>
        <form method="POST">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="product_name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Brand</label>
                    <input type="text" name="brand" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Desired Department</label>
                    <input type="text" name="desired_department" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Details</label>
                <textarea name="details" class="form-control" rows="2"></textarea>
            </div>

            <button type="submit" name="save_item" class="btn btn-success">
                Save Item
            </button>

        </form>
    </div>
</div>

<!-- TABLE -->
<table class="table table-bordered table-striped mt-3">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Brand</th>
            <th>Quantity</th>
            <th>Desired Department</th>
            <th>Details</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row=mysqli_fetch_assoc($result)){ ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['product_name'] ?></td>
            <td><?= $row['brand'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= $row['desired_department'] ?></td>
            <td><?= $row['details'] ?></td>
            <td>
                <span class="badge bg-warning text-dark">
                    <?= $row['status'] ?>
                </span>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
