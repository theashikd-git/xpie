<?php
include 'db.php';

$result = mysqli_query($conn,"
SELECT 
    a.product_name,
    a.department,
    a.place,
    a.quantity,
    t.total_quantity
FROM assets a
JOIN (
    SELECT product_name, SUM(quantity) AS total_quantity
    FROM assets
    GROUP BY product_name
) t ON a.product_name = t.product_name
ORDER BY a.product_name ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Asset Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.html">NAMIAS</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="assets.php">Assets</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="asset_report.php">Asset Report</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">

<h3 class="mb-4">Asset Quantity Report</h3>

<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
    <th>Product</th>
    <th>Department</th>
    <th>Place</th>
    <th>Qty</th>
    <th>Total</th>
</tr>
</thead>

<tbody>
<?php 
$previous_product = "";
$color_toggle = false;

while($row = mysqli_fetch_assoc($result)) {

    if($previous_product != $row['product_name']) {
        $color_toggle = !$color_toggle;
        $previous_product = $row['product_name'];
    }

    $total_class = $color_toggle ? "bg-warning text-dark" : "bg-info text-white";
?>
<tr>
    <td><?= $row['product_name'] ?></td>
    <td><?= $row['department'] ?></td>
    <td><?= $row['place'] ?></td>
    <td><?= $row['quantity'] ?></td>
    <td class="<?= $total_class ?>"><strong><?= $row['total_quantity'] ?></strong></td>
</tr>
<?php } ?>
</tbody>


</table>

</div>

</body>
</html>
