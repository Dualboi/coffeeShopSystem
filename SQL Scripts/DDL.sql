-- 1. Create the `roleType` table first as it is referenced by other tables.
CREATE TABLE roletype (
    roleTypeID INT AUTO_INCREMENT PRIMARY KEY,
    roleType ENUM('Barista', 'Manager', 'Waiter', 'Chef')
);

-- 2. Create the `address` table first as it is referenced by clientUserInfo
CREATE TABLE address (
    addressID INT AUTO_INCREMENT PRIMARY KEY,
    streetAddress VARCHAR(255),
    postCode VARCHAR(10),
    city VARCHAR(255)
);

-- 3. Create the `rota` table (This must come before `clientUserInfo`)
CREATE TABLE rota (
    rotaID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    roleTypeID INT DEFAULT NULL,
    shiftDate DATE NOT NULL,
    shiftStartTime DATETIME NOT NULL,
    shiftEndTime DATETIME NOT NULL,
    hoursWorked DECIMAL(5, 2) NULL,
    FOREIGN KEY (userID) REFERENCES clientUserInfo(userID),
    FOREIGN KEY (roleTypeID) REFERENCES roleType(roleTypeID) ON DELETE SET NULL
);


-- 4. Create the `clientUserInfo` table (Now that `address` and `rota` are created)
CREATE TABLE clientUserInfo (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    addressID INT,
    rotaID INT DEFAULT NULL,
    wagesID INT,
    password VARCHAR(255),
    email VARCHAR(255),
    forname VARCHAR(255),
    surname VARCHAR(255),
    isAdmin BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (addressID) REFERENCES address(addressID),
    FOREIGN KEY (rotaID) REFERENCES rota(rotaID) ON DELETE SET NULL
);

-- 5. Table for product categories
CREATE TABLE product_categories (
    categoryID INT AUTO_INCREMENT PRIMARY KEY,
    categoryName VARCHAR(255) -- e.g., 'coffee', 'tea'
);

-- 6. Products table, simplified with references to categories
CREATE TABLE products (
    productID INT AUTO_INCREMENT PRIMARY KEY,
    productName VARCHAR(255),
    categoryID INT,  -- Links to the product category
    price DECIMAL(10, 2),  -- Price per unit
    cost DECIMAL(10, 2) NOT NULL DEFAULT 0.00; -- cost of the product to the business
    FOREIGN KEY (categoryID) REFERENCES product_categories(categoryID)
);

-- 7. Suppliers table
CREATE TABLE suppliers (
    supplierID INT AUTO_INCREMENT PRIMARY KEY,
    supplierName VARCHAR(255)
);

-- 8. Inventory table to link suppliers and products
CREATE TABLE inventory (
    inventoryID INT AUTO_INCREMENT PRIMARY KEY,
    supplierID INT,  -- Link to the supplier
    productID INT,  -- Link to the product
    stockAmount DECIMAL(10, 2),  -- Quantity in stock
    FOREIGN KEY (supplierID) REFERENCES suppliers(supplierID),
    FOREIGN KEY (productID) REFERENCES products(productID)
);

-- 9. Sales data table to track sales
CREATE TABLE sales (
    saleID INT AUTO_INCREMENT PRIMARY KEY,
    productID INT,  -- Link to the product sold
    quantitySold DECIMAL(10, 2),  -- Quantity sold
    salePrice DECIMAL(10, 2),  -- Price at the time of sale
    revenue DECIMAL(0) NOT NULL DEFAULT 0.00;
    totalCosts DECIMAL(0) NOT null DEFAULT 0.00;
    profit DECIMAL(0) NOT null DEFAULT 0.00;
    FOREIGN KEY (productID) REFERENCES products(productID)
);

-- 10. Wages to be used to display the amount of pay for a user
create TABLE wages (
	wagesID INT AUTO_INCREMENT PRIMARY KEY,
    rotaID INT,  -- Link to the rots
    wage DECIMAL(0,0) -- wage
);