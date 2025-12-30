DELIMITER $$

CREATE PROCEDURE PopulateDummyData()
BEGIN
    DECLARE i INT DEFAULT 1;
    WHILE i <= 100000 DO
        INSERT INTO dummy_data (name, email, bio) 
        VALUES (
            CONCAT('User_', i), 
            CONCAT('user', i, '@example.com'),
            REPEAT('This is a long bio to consume some memory in the PHP loop. ', 5)
        );
        SET i = i + 1;
    END WHILE;
END$$

DELIMITER ;

-- Execute the procedure
CALL PopulateDummyData();