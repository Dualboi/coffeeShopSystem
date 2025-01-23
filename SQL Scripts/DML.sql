-- Insert the values
INSERT INTO roleType (roleType)
VALUES
    ('Barista'),
    ('Chef'),
    ('Waiter'),
    ('Manager');

-- Insert product categories
INSERT INTO product_categories (categoryName) VALUES
('coffee'),
('tea');

-- Insert some products
INSERT INTO products (productName, categoryID, price, cost) VALUES
('Coffee Beans', 1, 10.00, 4),
('Green Tea', 2, 5.00, 2);

-- Insert some suppliers
INSERT INTO suppliers (supplierName) VALUES
('Supplier A'),
('Supplier B');

-- Insert inventory data
INSERT INTO inventory (supplierID, productID, stockAmount) VALUES
(1, 1, 100),  -- 100 units of Coffee Beans from Supplier A
(2, 2, 50);   -- 50 units of Green Tea from Supplier B

-- Insert a sale
INSERT INTO sales (productID, quantitySold, salePrice) VALUES
(1, 10, 10.00);  -- Sold 10 units of Coffee Beans

DELIMITER //

CREATE TRIGGER calculate_hours_worked_before_update
BEFORE UPDATE ON rota
FOR EACH ROW
BEGIN
    IF NEW.shiftStartTime IS NOT NULL AND NEW.shiftEndTime IS NOT NULL THEN
        SET NEW.hoursWorked = TIMESTAMPDIFF(MINUTE, NEW.shiftStartTime, NEW.shiftEndTime) / 60;
    ELSE
        SET NEW.hoursWorked = NULL;
    END IF;
END; //

DELIMITER ;

DELIMITER //

CREATE TRIGGER calculate_hours_worked_before_insert
BEFORE INSERT ON rota
FOR EACH ROW
BEGIN
    IF NEW.shiftStartTime IS NOT NULL AND NEW.shiftEndTime IS NOT NULL THEN
        SET NEW.hoursWorked = TIMESTAMPDIFF(MINUTE, NEW.shiftStartTime, NEW.shiftEndTime) / 60;
    ELSE
        SET NEW.hoursWorked = NULL; -- Set to NULL if times are not provided
    END IF;
END; //

DELIMITER ;
