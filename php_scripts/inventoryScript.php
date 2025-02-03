<?php
include "php_scripts/db_connection.php"; // Include your database connection

// Initialize variables
$inventoryItems = [];
$successMessage = "";
$errorMessage = "";

// Connect to the database
$db = Database::getInstance();
$conn = $db->getConnection();

// Handle removing sold items (inventory update)
if (isset($_POST['sold_product_id']) && isset($_POST['sold_amount'])) {
    $productID = intval($_POST['sold_product_id']);
    $soldAmount = intval($_POST['sold_amount']);

    if ($soldAmount <= 0) {
        $_SESSION['errorMessage'] = "Invalid quantity entered.";
        header("Location: inventory.php");
        exit;
    }

    // Fetch product details: stock, price, and cost
    $query = "
        SELECT p.price, p.cost, i.stockAmount
        FROM inventory i
        JOIN products p ON i.productID = p.productID
        WHERE i.productID = ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        $_SESSION['errorMessage'] = "Product not found in inventory.";
        header("Location: inventory.php");
        exit;
    }

    $stmt->bind_result($salePrice, $cost, $stockAmount);
    $stmt->fetch();
    $stmt->close();

    if ($stockAmount < $soldAmount) {
        $_SESSION['errorMessage'] = "Not enough stock available.";
        header("Location: inventory.php");
        exit;
    }

    // Begin MySQL Transaction
    $conn->begin_transaction();

    try {
        // Calculate revenue, cost, and profit
        $revenue = $salePrice * $soldAmount;
        $totalCost = $cost * $soldAmount;
        $profit = $revenue - $totalCost;

        // Insert sales data
        $insertSalesQuery = "
            INSERT INTO sales (productID, quantitySold, salePrice, revenue, totalCosts, profit) 
            VALUES (?, ?, ?, ?, ?, ?)
        ";
        $stmtSales = $conn->prepare($insertSalesQuery);
        $stmtSales->bind_param("iddddd", $productID, $soldAmount, $salePrice, $revenue, $totalCost, $profit);
        $stmtSales->execute();
        $stmtSales->close();

        // Update inventory
        $updateInventoryQuery = "UPDATE inventory SET stockAmount = stockAmount - ? WHERE productID = ?";
        $stmtInventory = $conn->prepare($updateInventoryQuery);
        $stmtInventory->bind_param("di", $soldAmount, $productID);
        $stmtInventory->execute();
        $stmtInventory->close();

        // Check if stock is zero, then delete product
        if ($stockAmount - $soldAmount <= 0) {
            $deleteInventoryQuery = "DELETE FROM inventory WHERE productID = ?";
            $stmtDelete = $conn->prepare($deleteInventoryQuery);
            $stmtDelete->bind_param("i", $productID);
            $stmtDelete->execute();
            $stmtDelete->close();
        }

        // Commit transaction
        $conn->commit();
        $_SESSION['successMessage'] = "Sale recorded and inventory updated successfully!";
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $_SESSION['errorMessage'] = "Transaction failed: " . $e->getMessage();
    }

    header("Location: salesData.php");
    exit;
}

// Query to fetch inventory items
$query = "
    SELECT 
        p.productID, 
        p.productName, 
        p.price, 
        p.cost, 
        i.stockAmount, 
        s.supplierName
    FROM inventory i
    JOIN products p ON i.productID = p.productID
    JOIN suppliers s ON i.supplierID = s.supplierID
";

$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $inventoryItems[] = $row;
    }
} else {
    $errorMessage = "No inventory items found.";
}

// Fetch suppliers before closing the connection
$suppliers = [];
$querySuppliers = "SELECT supplierID, supplierName FROM suppliers";
$suppliersResult = $conn->query($querySuppliers);
if ($suppliersResult && $suppliersResult->num_rows > 0) {
    while ($row = $suppliersResult->fetch_assoc()) {
        $suppliers[] = $row;
    }
}

// Handle adding new inventory (adding a new product)
if (
    isset($_POST['new_product_name']) &&
    isset($_POST['new_product_amount']) &&
    isset($_POST['new_category_id']) &&
    isset($_POST['new_supplier_id']) &&
    isset($_POST['new_product_price']) &&
    isset($_POST['new_product_cost'])
) {
    $productName = trim($_POST['new_product_name']);
    $productAmount = intval($_POST['new_product_amount']);
    $categoryID = intval($_POST['new_category_id']);
    $supplierID = intval($_POST['new_supplier_id']);
    $productPrice = floatval($_POST['new_product_price']);
    $productCost = floatval($_POST['new_product_cost']);

    // Check for empty or invalid values
    if (empty($productName) || $productAmount < 0 || $categoryID <= 0 || $supplierID <= 0 || $productPrice <= 0 || $productCost < 0) {
        $_SESSION['errorMessage'] = "Invalid input: Please check all fields.";
        header("Location: inventory.php");
        exit;
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert new product into Products table
        $insertProductQuery = "INSERT INTO products (productName, categoryID, price, cost) VALUES (?, ?, ?, ?)";
        $stmtProduct = $conn->prepare($insertProductQuery);
        $stmtProduct->bind_param("sidd", $productName, $categoryID, $productPrice, $productCost);
        
        if (!$stmtProduct->execute()) {
            throw new Exception("Failed to add new product.");
        }

        $newProductID = $conn->insert_id; // Get the newly created product's ID
        $stmtProduct->close();

        // Add to inventory table
        $insertInventoryQuery = "INSERT INTO inventory (supplierID, productID, stockAmount) VALUES (?, ?, ?)";
        $stmtInventory = $conn->prepare($insertInventoryQuery);
        $stmtInventory->bind_param("iii", $supplierID, $newProductID, $productAmount);

        if (!$stmtInventory->execute()) {
            throw new Exception("Failed to add product to inventory.");
        }
        $stmtInventory->close();

        // Commit transaction
        $conn->commit();
        $_SESSION['successMessage'] = "New product added to inventory successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['errorMessage'] = "Error: " . $e->getMessage();
    }

    // Redirect to inventory page
    header("Location: inventory.php");
    exit;
}

$conn->close();
?>
