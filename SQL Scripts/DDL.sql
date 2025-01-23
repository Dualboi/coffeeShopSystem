CREATE DATABASE coffee_shop_system;

-- Select the database
USE coffee_shop_system;

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

-- 3. Create the `product_categories` table (no foreign key dependencies yet)
CREATE TABLE product_categories (
    categoryID INT AUTO_INCREMENT PRIMARY KEY,
    categoryName VARCHAR(255)
);

-- 4. Create the `products` table (no foreign key dependencies yet)
CREATE TABLE products (
    productID INT AUTO_INCREMENT PRIMARY KEY,
    productName VARCHAR(255),
    categoryID INT,  -- Links to the product category
    price DECIMAL(10, 2),  -- Price per unit
    cost DECIMAL(10, 2) NOT NULL DEFAULT (0.00), -- cost of the product to the business
    FOREIGN KEY (categoryID) REFERENCES product_categories(categoryID)
);

-- 5. Create the `suppliers` table (no foreign key dependencies yet)
CREATE TABLE suppliers (
    supplierID INT AUTO_INCREMENT PRIMARY KEY,
    supplierName VARCHAR(255)
);

-- 6. Create the `inventory` table to link suppliers and products
CREATE TABLE inventory (
    inventoryID INT AUTO_INCREMENT PRIMARY KEY,
    supplierID INT,  -- Link to the supplier
    productID INT,  -- Link to the product
    stockAmount DECIMAL(10, 2),  -- Quantity in stock
    FOREIGN KEY (supplierID) REFERENCES suppliers(supplierID),
    FOREIGN KEY (productID) REFERENCES products(productID)
);

-- 7. Create the `sales` table to track sales
CREATE TABLE sales (
    saleID INT AUTO_INCREMENT PRIMARY KEY,
    productID INT,  -- Link to the product sold
    quantitySold DECIMAL(10, 2),  -- Quantity sold
    salePrice DECIMAL(10, 2),  -- Price at the time of sale
    revenue DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    totalCosts DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    profit DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (productID) REFERENCES products(productID)
);

-- 8. Create the `clientUserInfo` table without foreign keys to `rota` and `wages` (to avoid circular dependencies)
CREATE TABLE clientUserInfo (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    addressID INT,
    password VARCHAR(255),
    email VARCHAR(255),
    forname VARCHAR(255),
    surname VARCHAR(255),
    isAdmin BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (addressID) REFERENCES address(addressID)
);

-- 9. Create the `rota` table without the foreign key to `clientUserInfo` (for now)
CREATE TABLE rota (
    rotaID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    roleTypeID INT DEFAULT NULL,
    shiftDate DATE NOT NULL,
    shiftStartTime DATETIME NOT NULL,
    shiftEndTime DATETIME NOT NULL,
    hoursWorked DECIMAL(5, 2) NULL
);

-- 10. Create the `wages` table without the foreign key to `rota` (for now)
CREATE TABLE wages (
    wagesID INT AUTO_INCREMENT PRIMARY KEY,
    rotaID INT,  -- Link to the rota
    wage DECIMAL(10, 2)  -- wage, adjusted precision
);


-- Now, add foreign keys to `clientUserInfo`, `rota`, and `wages`

-- 11. Add foreign keys to `clientUserInfo`
ALTER TABLE clientUserInfo
ADD COLUMN rotaID INT DEFAULT NULL,
ADD COLUMN wagesID INT,
ADD FOREIGN KEY (rotaID) REFERENCES rota(rotaID) ON DELETE SET NULL,
ADD FOREIGN KEY (wagesID) REFERENCES wages(wagesID);

-- 12. Add foreign keys to `rota`
ALTER TABLE rota
ADD FOREIGN KEY (userID) REFERENCES clientUserInfo(userID),
ADD FOREIGN KEY (roleTypeID) REFERENCES roletype(roleTypeID) ON DELETE SET NULL;

-- 13. Add foreign keys to `wages`
ALTER TABLE wages
ADD FOREIGN KEY (rotaID) REFERENCES rota(rotaID);
