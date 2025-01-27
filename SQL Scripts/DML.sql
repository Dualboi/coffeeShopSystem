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

INSERT INTO wages (roleTypeID, wage)
VALUES
    (1, 12), -- Barista
    (2, 17), -- Manager
    (3, 12), -- Waiter
    (4, 13)  -- Chef
ON DUPLICATE KEY UPDATE wage = VALUES(wage);


DELIMITER //

CREATE TRIGGER calculate_hours_worked_before_insert
BEFORE INSERT ON rota
FOR EACH ROW
BEGIN
    IF NEW.shiftStartTime IS NOT NULL AND NEW.shiftEndTime IS NOT NULL THEN
        SET NEW.hoursWorked = SEC_TO_TIME(TIMESTAMPDIFF(SECOND, NEW.shiftStartTime, NEW.shiftEndTime));
    ELSE
        SET NEW.hoursWorked = NULL; -- Set to NULL if times are not provided
    END IF;
END; //

DELIMITER ;


DELIMITER //

CREATE TRIGGER calculate_hours_worked_before_insert
BEFORE INSERT ON rota
FOR EACH ROW
BEGIN
    IF NEW.shiftStartTime IS NOT NULL AND NEW.shiftEndTime IS NOT NULL THEN
        SET NEW.hoursWorked = SEC_TO_TIME(TIMESTAMPDIFF(SECOND, NEW.shiftStartTime, NEW.shiftEndTime));
    ELSE
        SET NEW.hoursWorked = NULL; -- Set to NULL if times are not provided
    END IF;
END; //

DELIMITER ;

