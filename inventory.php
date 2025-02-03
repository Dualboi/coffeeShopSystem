<?php
session_start();

// Ensure that the userID is set
if (!isset($_SESSION['userID'])) {
    echo "No userID found in session.";
    exit;  // Stop execution if userID is not set
}

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit;
}
include "php_scripts/inventoryScript.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Coffee Shop System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="stylesheet.css">
    <script src="https://kit.fontawesome.com/4a263b5a4b.js" crossorigin="anonymous"></script>
</head>

<body>
    <header>
        <h2 class="logo">coffee.co</h2>
        <div class="navbar">
            <a href="<?php echo isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] ? 'adminPage.php' : 'ClientPage.php'; ?>">Home</a>
            <a href="index.php">Logout</a>
            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']): ?>
                <a href="salesData.php"> Sales data </a>
            <?php endif; ?>
            <a href="inventory.php">Inventory</a>
            <a href="rota.php">Rota</a>
            <a href="wages.php">Wages</a>
        </div>
    </header>
    <h1 class="login_txt">Inventory Dashboard</h1>
    <section class="inventory-section">
        <article class="inventory-table">
            <table class="inventory-dataTable" border="1">
                <thead>
                    <tr>
                        <th colspan="5" class="inventory-table-header">Current Inventory</th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Cost</th>
                        <th>Quantity in Inventory</th>
                        <th>Supplier Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventoryItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['productName']); ?></td>
                            <td>£<?php echo htmlspecialchars($item['price']); ?></td>
                            <td>£<?php echo htmlspecialchars($item['cost']); ?></td>
                            <td><?php echo htmlspecialchars($item['stockAmount']); ?></td>
                            <td><?php echo htmlspecialchars($item['supplierName']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="inventory-form-container">
                <form class="inventory-remove-items" method="POST" action="inventory.php">
                    <h3>Remove Sold Items</h3>
                    <label for="sold_product_id">Product</label>
                    <select name="sold_product_id" id="sold_product_id" required>
                        <?php foreach ($inventoryItems as $item): ?>
                            <option value="<?php echo $item['productID']; ?>"><?php echo htmlspecialchars($item['productName']); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="sold_amount">Amount Sold</label>
                    <input type="number" id="sold_amount" name="sold_amount" id="positiveNumber" name="positiveNumber" min="0" required>

                    <input class="inventory-submit-inven" type="submit"
                        value="<?php echo isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] ? 'Update Inventory and Show Sales Data' : 'Update Inventory'; ?>">
                    <?php if (isset($_SESSION['errorMessage'])) {
                        echo '<p style="color: red;">' . $_SESSION['errorMessage'] . '</p>';
                        unset($_SESSION['errorMessage']);  // Clear the error message after displaying
                    } ?>
                </form>

                <form class="inventory-add-items" method="POST" action="inventory.php">
                    <h3>Add New Product to Inventory</h3>
                    <label for="new_product_name">Product Name</label>
                    <input type="text" id="new_product_name" name="new_product_name" required>

                    <label for="new_product_amount">Product Amount</label>
                    <input type="number" id="new_product_amount" name="new_product_amount" id="positiveNumber" name="positiveNumber" min="0" required>

                    <label for="new_product_price">Product Price</label>
                    <input type="number" step="0.01" id="new_product_price" name="new_product_price" id="positiveNumber" name="positiveNumber" min="0.01" required>

                    <label for="new_product_cost">Product Cost</label>
                    <input type="number" step="0.01" id="new_product_cost" name="new_product_cost" id="positiveNumber" name="positiveNumber" min="0.01" required>

                    <label for="new_category_id">Category</label>
                    <select name="new_category_id" id="new_category_id" required>
                        <option value="1">Coffee</option>
                        <option value="2">Tea</option>
                    </select>

                    <label for="new_supplier_id">Supplier</label>
                    <select name="new_supplier_id" id="new_supplier_id" required>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?php echo $supplier['supplierID']; ?>"><?php echo $supplier['supplierName']; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <input class="inventory-submit-inven" type="submit" value="Add Product">
                </form>
            </div>
        </article>
    </section>

    <footer class="mainfooter">
        <a class="logofooter"><i class="fa-regular fa-copyright"></i> 2025 coffee.co All Rights Reserved.</a>
    </footer>

</body>

</html>