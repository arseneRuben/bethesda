-- Procedure pour supprimer toutes les view 

DELIMITER //

CREATE PROCEDURE delete_all_views()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE view_name VARCHAR(255);
    DECLARE cur CURSOR FOR
        SELECT table_name
        FROM information_schema.tables
        WHERE table_type = 'VIEW' AND table_schema = DATABASE();
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO view_name;
        IF done THEN
            LEAVE read_loop;
        END IF;
        SET @stmt = CONCAT('DROP VIEW ', view_name);
        PREPARE stmt FROM @stmt;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END LOOP;
    CLOSE cur;
END//

DELIMITER ;


CALL delete_all_views();

ALTER TABLE evaluation
DROP FOREIGN KEY fk_evaluation_user;

ALTER TABLE evaluation
DROP COLUMN author;

ALTER TABLE evaluation
ADD COLUMN author_id INT DEFAULT 120,
ADD CONSTRAINT fk_evaluation_user
FOREIGN KEY (author_id) REFERENCES user(id);

SELECT * FROM evaluation
ORDER BY id DESC
LIMIT 10;

ALTER TABLE user MODIFY COLUMN security_question VARCHAR(200);
ALTER TABLE user MODIFY COLUMN security_answer VARCHAR(200);

/* supprimer les enregistrement superflues de la table user. */
DELETE FROM user WHERE  id NOT IN (SELECT teacher_id FROM attribution UNION SELECT author_id FROM evaluation );

SELECT full_name from user WHERE id IN (SELECT teacher_id FROM attribution  );

ALTER TABLE mark
ADD CONSTRAINT unics_evaluation_student UNIQUE (student_id, evaluation_id);

DELETE FROM mark
WHERE id NOT IN (
    SELECT id FROM (
        SELECT MAX(id) AS id
        FROM mark
        GROUP BY evaluation_id, student_id
    ) AS subquery
);


CREATE TABLE mark_temp AS
SELECT MIN(id) AS id, appreciation, evaluation_id, rank2, student_id, value, weight
FROM mark
GROUP BY evaluation_id, student_id;


ALTER TABLE mark
MODIFY COLUMN id INT AUTO_INCREMENT PRIMARY KEY;
