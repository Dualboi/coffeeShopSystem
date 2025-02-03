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

    // Ensure the sold amount is a valid number
    if ($soldAmount <= 0) {
        $_SESSION['errorMessage'] = "Invalid quantity entered.";
        header("Location: inventory.php");
        exit;
    }

    // Fetch the current stock from the database
    $stockQuery = "SELECT stockAmount FROM inventory WHERE productID = ?";
    $stmtStock = $conn->prepare($stockQuery);
    $stmtStock->bind_param("i", $productID);
    $stmtStock->execute();
    $stmtStock->store_result();
    $stmtStock->bind_result($stockAmount);
    $stmtStock->fetch();

    // Check if product exists
    if ($stmtStock->num_rows === 0) {
        $_SESSION['errorMessage'] = "Product not found in inventory.";
        $stmtStock->close();
        header("Location: inventory.php");
        exit;
    }

    $stmtStock->close();

    // Check if enough stock is available
    if ($stockAmount < $soldAmount) {
        $_SESSION['errorMessage'] = "Not enough stock available.";
        header("Location: inventory.php");
        exit;
    }

    // Update inventory after the sale
    $updateQuery = "UPDATE inventory SET stockAmount = stockAmount - ? WHERE productID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ii", $soldAmount, $productID);

    if ($stmt->execute()) {
        // Check if stockAmount is now 0, and delete the row if it is
        $checkStockQuery = "SELECT stockAmount FROM inventory WHERE productID = ?";
        $stmtCheck = $conn->prepare($checkStockQuery);
        $stmtCheck->bind_param("i", $productID);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        $stmtCheck->bind_result($remainingStock);
        $stmtCheck->fetch();

        if ($remainingStock <= 0) {
            // Delete the product from inventory if sold out
            $deleteInventoryQuery = "DELETE FROM inventory WHERE productID = ?";
            $deleteStmt = $conn->prepare($deleteInventoryQuery);
            $deleteStmt->bind_param("i", $productID);
            $deleteStmt->execute();
            $deleteStmt->close();
        }

        $_SESSION['successMessage'] = "Inventory updated successfully!";
    } else {
        $_SESSION['errorMessage'] = "Failed to update inventory.";
    }

    $stmtCheck->close();
    $stmt->close();

    // Redirect admin users to sales data page
    if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {
        header("Location: salesData.php");
        exit;
    } else {
        header("Location: inventory.php");
        exit;
    }
}
// Handling the creation of sales (adding sales data)
if (isset($_POST['sold_product_id']) && isset($_POST['sold_amount'])) {
    $productID = (int)$_POST['sold_product_id'];
    $soldAmount = (float)$_POST['sold_amount'];

    // Fetch product details: price, cost, and stock amount
    $query = "
        SELECT 
            p.price AS salePrice, 
            p.cost AS cost, 
            i.stockAmount AS stockAmount
        FROM inventory i
        JOIN products p ON i.productID = p.productID
        WHERE p.productID = ?
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing query: " . $conn->error);
    }

    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($salePrice, $cost, $stockAmount);
        $stmt->fetch();

        if ($stockAmount < $soldAmount) {
            $errorMessage = "Not enough stock available.";
        } else {
            // Calculate revenue, total cost, and profit
            $revenue = $salePrice * $soldAmount;
            $totalCost = $cost * $soldAmount;
            $profit = $revenue - $totalCost;

            // Insert new sales data into the sales table
            $insertSalesQuery = "
                INSERT INTO sales (productID, quantitySold, salePrice, revenue, totalCosts, profit) 
                VALUES (?, ?, ?, ?, ?, ?)
            ";
            $stmtSales = $conn->prepare($insertSalesQuery);

            if ($stmtSales) {
                $stmtSales->bind_param("iddddd", $productID, $soldAmount, $salePrice, $revenue, $totalCost, $profit);

                if ($stmtSales->execute()) {
                    // Update inventory table
                    $updateInventoryQuery = "UPDATE inventory SET stockAmount = stockAmount - ? WHERE productID = ?";
                    $stmtInventory = $conn->prepare($updateInventoryQuery);

                    if ($stmtInventory) {
                        $stmtInventory->bind_param("di", $soldAmount, $productID);

                        if ($stmtInventory->execute()) {
                            // Delete the product from inventory if stock is now zero
                            if ($stockAmount - $soldAmount <= 0) {
                                $deleteInventoryQuery = "DELETE FROM inventory WHERE productID = ?";
                                $deleteStmt = $conn->prepare($deleteInventoryQuery);

                                if ($deleteStmt) {
                                    $deleteStmt->bind_param("i", $productID);
                                    $deleteStmt->execute();
                                    $deleteStmt->close();
                                }
                            }

                            $successMessage = "Sale recorded and inventory updated successfully!";
                        } else {
                            $errorMessage = "Failed to update inventory: " . $conn->error;
                        }

                        $stmtInventory->close();
                    } else {
                        $errorMessage = "Failed to prepare inventory update query: " . $conn->error;
                    }
                } else {
                    $errorMessage = "Failed to execute sales insertion: " . $stmtSales->error;
                }

                $stmtSales->close();
            } else {
                $errorMessage = "Failed to prepare sales query: " . $conn->error;
            }
        }
    } else {
        $errorMessage = "Product not found in inventory.";
    }

    $stmt->close();
} else {
    $errorMessage = "Invalid input data.";
}
// Handle adding new inventory (adding new product)
if (isset($_POST['new_product_name']) && isset($_POST['new_product_amount']) && isset($_POST['new_category_id']) && isset($_POST['new_supplier_id']) && isset($_POST['new_product_price']) && isset($_POST['new_product_cost'])) {
    $productName = $_POST['new_product_name'];
    $productAmount = $_POST['new_product_amount'];
    $categoryID = $_POST['new_category_id'];
    $supplierID = $_POST['new_supplier_id'];
    $productPrice = $_POST['new_product_price'];
    $productCost = $_POST['new_product_cost'];

    // Insert new product into Products table
    $insertQuery = "INSERT INTO products (productName, categoryID, price, cost) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sidd", $productName, $categoryID, $productPrice, $productCost);

    if ($stmt->execute()) {
        $newProductID = $conn->insert_id;

        // Add to inventory table
        $insertInventoryQuery = "INSERT INTO inventory (supplierID, productID, stockAmount) VALUES (?, ?, ?)";
        $stmtInventory = $conn->prepare($insertInventoryQuery);
        $stmtInventory->bind_param("iii", $supplierID, $newProductID, $productAmount);

        if ($stmtInventory->execute()) {
            $successMessage = "New product added to inventory successfully!";
        } else {
            $errorMessage = "Failed to add product to inventory.";
        }

        $stmtInventory->close();
    } else {
        $errorMessage = "Failed to add new product.";
    }
}



// Query to fetch product-related data: product name, price, amount, and supplier
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

// Check if data is returned
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

// Close the database connection
$conn->close();
