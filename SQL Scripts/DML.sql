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

-- Creates trigger for calculating total hours worked when shift start and shift end are inserted
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

BEGIN
    DECLARE emailError VARCHAR(500);
    SET emailError = '';

    -- Skip validation if email contains '@coffee.co'
    IF NEW.email NOT LIKE '%@coffee.co%' THEN
        -- Validate non-admin email: Must follow standard email format with valid TLDs
        IF NEW.email NOT REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+.(com|net|org|edu|gov|mil|info|io)$' THEN
            SET emailError = CONCAT(emailError, 'Invalid email format: must be a standard email format with valid TLDs. ');
        END IF;
    END IF;

    -- If any errors exist, raise the error message
    IF emailError != '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = emailError;
    END IF;
END

-- Name Validation Trigger
CREATE TRIGGER validate_name_before_insert
BEFORE INSERT ON clientUserInfo
FOR EACH ROW
BEGIN
    DECLARE nameError VARCHAR(500);
    SET nameError = '';

    -- Validate First Name
    IF NEW.forname NOT REGEXP '^[A-Za-z]+([-\'][A-Za-z]+)*$' THEN
        SET nameError = CONCAT(nameError, 'First Name can only contain letters, apostrophes, and hyphens. ');
    END IF;

    -- Validate Last Name
    IF NEW.surname NOT REGEXP '^[A-Za-z]+([-\'][A-Za-z]+)*$' THEN
        SET nameError = CONCAT(nameError, 'Last Name can only contain letters, apostrophes, and hyphens. ');
    END IF;

    -- If any errors exist, raise the error message
    IF nameError != '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = nameError;
    END IF;
END$$

DELIMITER ;
