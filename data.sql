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

delete from mark where student_id = 1774;



SELECT DISTINCT seq.id, eval.id as eval,crs.id as crs, crs.wording, room.id as room,  teach.full_name as teacher    , modu.id as module,m.value as value, m.weight as weight
                    FROM  mark  m   JOIN  student    std     ON  (m.student_id        =   std.id AND  std.id = 1664)
                    JOIN  evaluation eval    ON  m.evaluation_id     =   eval.id
                    JOIN  class_room room    ON   eval.class_room_id     =   room.id
                    JOIN  course     crs     ON  eval.course_id      =   crs.id
                    JOIN  attribution att    ON  att.course_id      =   crs.id
                    JOIN  user  teach ON  att.teacher_id  =   teach.id
                    JOIN  module     modu    ON  modu.id       =   crs.module_id
                    JOIN  sequence   seq     ON  seq.id     =   eval.sequence_id
                    JOIN  quater   qt     ON  seq.quater_id    =   qt.id
                    WHERE     qt.id = 20
                    ORDER BY crs.id;


select attribution.teacher_id from attribution where attribution.year_id = 7 and attribution.course_id=181;

SELECT DISTINCT sequence.id as sequence, course.wording , course.coefficient, mark.value, mark.weight, mark.rank2, evaluation.competence, attribution.teacher_id, school_year.id, user.full_name
FROM sequence 
JOIN evaluation ON evaluation.sequence_id = sequence.id
JOIN course ON evaluation.course_id = course.id
JOIN attribution on attribution.course_id = course.id
JOIN user ON user.id = attribution.teacher_id
JOIN mark ON evaluation.id = mark.evaluation_id
JOIN quater ON sequence.quater_id = quater.id
JOIN school_year on quater.school_year_id= school_year.id and school_year.id = attribution.year_id
WHERE quater.id = 19 AND   mark.student_id=39
ORDER BY course.id,sequence.id;




ALTER TABLE evaluation ADD mini FLOAT default 0;
ALTER TABLE evaluation ADD maxi FLOAT default 20;

ALTER TABLE evaluation
RENAME COLUMN min TO mini;
ALTER TABLE evaluation
RENAME COLUMN max TO maxi;


SELECT DISTINCT evaluation.id ,student.id as student_id, mark.id as mark_id,  sequence.id as sequence, course.id as course_id ,course.wording , course.coefficient, mark.value, mark.weight, mark.rank2, evaluation.mini as mini, evaluation.maxi as maxi, evaluation.competence, attribution.teacher_id, school_year.id, user.full_name
        FROM sequence 
        JOIN evaluation ON evaluation.sequence_id = sequence.id AND evaluation.class_room_id = 16
        JOIN course ON evaluation.course_id = course.id
        JOIN attribution on attribution.course_id = course.id
        JOIN user ON user.id = attribution.teacher_id
        JOIN mark ON evaluation.id = mark.evaluation_id
        JOIN student ON mark.student_id = student.id
        JOIN quater ON sequence.quater_id = quater.id
        JOIN school_year on quater.school_year_id= school_year.id and school_year.id = attribution.year_id
        WHERE quater.id = 20 AND course.wording != "LCN"
        ORDER BY student_id, course.id,sequence.id; 

update evaluation set course_id = 7 where id = 12532;
