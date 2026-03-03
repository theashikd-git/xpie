<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

/* ================= ADD CATEGORY ================= */
if(isset($_POST['save_category'])){
    $new_cat = $_POST['new_category'];
    mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$new_cat')");
    header("Location: assets.php"); exit;
}

/* ================= ADD DEPARTMENT ================= */
if(isset($_POST['save_department'])){
    $new_dep = $_POST['new_department'];
    mysqli_query($conn, "INSERT INTO departments (name) VALUES ('$new_dep')");
    header("Location: assets.php"); exit;
}

/* ================= ADD PLACE ================= */
if(isset($_POST['save_place'])){
    $new_place = $_POST['new_place'];
    mysqli_query($conn, "INSERT INTO places (name) VALUES ('$new_place')");
    header("Location: assets.php"); exit;
}

/* ================= ADD ASSET ================= */
if (isset($_POST['save_asset'])) {
    $product_name = $_POST['product_name'];
    $brand        = $_POST['brand'];
    $category     = $_POST['category'];
    $quantity     = $_POST['quantity'];
    $department   = $_POST['department'];
    $place        = $_POST['place'];
    $note         = $_POST['note'];

    $w_start = $_POST['warranty_start'];
    $w_value = $_POST['warranty_value'];
    $w_unit  = $_POST['warranty_unit'];

    if ($w_unit == 'year') {
        $w_end = date('Y-m-d', strtotime("+$w_value years", strtotime($w_start)));
    } else {
        $w_end = date('Y-m-d', strtotime("+$w_value months", strtotime($w_start)));
    }

    $sql = "INSERT INTO assets
        (product_name, brand, category, quantity, department, place, warranty_start, warranty_end, note)
        VALUES
        ('$product_name','$brand','$category','$quantity','$department','$place','$w_start','$w_end','$note')";

    if (!mysqli_query($conn, $sql)) {
        die("INSERT ERROR: " . mysqli_error($conn));
    }
	header("Location: assets.php");
exit();
}

/* ================= FETCH ================= */
$categories  = mysqli_query($conn, "SELECT name FROM categories");
$departments = mysqli_query($conn, "SELECT name FROM departments");
$places      = mysqli_query($conn, "SELECT name FROM places");
$assets      = mysqli_query($conn, "SELECT * FROM assets ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assets Management</title>
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
        <li class="nav-item"><a class="nav-link active" href="assets.php">Assets</a></li>
        <li class="nav-item"><a class="nav-link" href="ip_allocation.php">IP Allocation</a></li>
        <li class="nav-item"><a class="nav-link" href="wishlist.php">Wishlist</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_panel.php">Admin Panel</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Asset Management</h3>
  <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#addAssetForm">
    ➕ Add Asset
  </button>
</div>

<!-- ADD ASSET FORM -->
<div class="collapse mb-4" id="addAssetForm">
<form method="POST" class="card p-3 shadow-sm">

<div class="row mb-3">

    <div class="col-md-5">
        <label class="form-label">Product Name</label>
        <input type="text" name="product_name" class="form-control" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Brand Name</label>
        <input type="text" name="brand_name" class="form-control">
    </div>

    <div class="col-md-3">
        <label class="form-label">Quantity</label>
        <input type="number" name="quantity" class="form-control" min="1" value="1" required>
    </div>

</div>


<div class="row mb-3">
  <!-- Category with Add button -->
  <div class="col-md-4 d-flex align-items-end">
    <div class="flex-grow-1">
      <label>Category</label>
      <select name="category" class="form-select" required>
        <option value="">Select</option>
        <?php while($c = mysqli_fetch_assoc($categories)) { ?>
          <option><?= $c['name'] ?></option>
        <?php } ?>
      </select>
    </div>
    <button type="button" class="btn btn-outline-primary ms-2" data-bs-toggle="collapse" data-bs-target="#addCategoryForm">➕</button>
  </div>

  <!-- Under Department with Add button -->
  <div class="col-md-4 d-flex align-items-end">
    <div class="flex-grow-1">
      <label>Under Department</label>
      <select name="department" class="form-select" required>
        <option value="">Select</option>
        <?php while($d = mysqli_fetch_assoc($departments)) { ?>
          <option><?= $d['name'] ?></option>
        <?php } ?>
      </select>
    </div>
    <button type="button" class="btn btn-outline-primary ms-2" data-bs-toggle="collapse" data-bs-target="#addDepartmentForm">➕</button>
  </div>

  <!-- Place Allocation with Add button -->
  <div class="col-md-4 d-flex align-items-end">
    <div class="flex-grow-1">
      <label>Place Allocation</label>
      <select name="place" class="form-select" required>
        <option value="">Select</option>
        <?php while($p = mysqli_fetch_assoc($places)) { ?>
          <option><?= $p['name'] ?></option>
        <?php } ?>
      </select>
    </div>
    <button type="button" class="btn btn-outline-primary ms-2" data-bs-toggle="collapse" data-bs-target="#addPlaceForm">➕</button>
  </div>
</div>
<!-- Hidden Add Category Form -->
<!-- Hidden Add Category -->
<div class="collapse mt-2 mb-2" id="addCategoryForm">
    <div class="d-flex">
        <input type="text" name="new_category" class="form-control me-2" placeholder="Enter category">
       <button type="submit" name="save_category"
        class="btn btn-success"
        formnovalidate>
			Save Category
		</button>

    </div>
</div>



<!-- Hidden Add Department Form -->
<!-- Hidden Add Department -->
<div class="collapse mt-2 mb-2" id="addDepartmentForm">
    <div class="d-flex">
        <input type="text" name="new_department" class="form-control me-2" placeholder="Enter department">
        <button type="submit" name="save_department"
        class="btn btn-success"
        formnovalidate>
    Save Department
</button>

    </div>
</div>


<!-- Hidden Add Place Form -->
<!-- Hidden Add Place -->
<div class="collapse mt-2 mb-3" id="addPlaceForm">
    <div class="d-flex">
        <input type="text" name="new_place" class="form-control me-2" placeholder="Enter place">
        <button type="submit" name="save_place"
        class="btn btn-success"
        formnovalidate>
    Save Place
</button>

    </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label>Warranty Start</label>
    <input type="date" name="warranty_start" class="form-control" >
  </div>
  <div class="col-md-6">
    <label>Warranty Period</label>
    <div class="input-group">
      <input type="number" name="warranty_value" class="form-control" >
      <select name="warranty_unit" class="form-select">
        <option value="year">Year(s)</option>
        <option value="month">Month(s)</option>
      </select>
    </div>
  </div>
</div>

<div class="mb-3">
  <label>Note</label>
  <textarea name="note" class="form-control"></textarea>
</div>

<button class="btn btn-success" name="save_asset">Save Asset</button>
</form>
</div>

<!-- ASSET LIST -->
<table class="table table-bordered">
<thead class="table-dark">
<tr>
  <th>ID</th>
  <th>Product</th>
  <th>Category</th>
  <th>Qty</th>
  <th>Department</th>
  <th>Place</th>
  <th>Warranty End</th>
  <th>Note</th>
</tr>
</thead>
<tbody>
<?php if (mysqli_num_rows($assets) > 0): ?>
<?php while($a = mysqli_fetch_assoc($assets)) { ?>
<tr>
  <td><?= $a['id'] ?></td>
  <td><?= $a['product_name'] ?></td>
  <td><?= $a['category'] ?></td>
  <td><?= $a['quantity'] ?></td>
  <td><?= $a['department'] ?></td>
  <td><?= $a['place'] ?></td>
  <td><?= $a['warranty_end'] ?></td>
  <td><?= $a['note'] ?></td>
</tr>
<?php } else: ?>
<tr>
  <td colspan="7" class="text-center text-muted">No assets</td>
</tr>
<?php endif; ?>
</tbody>
</table>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

