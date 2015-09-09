DROP SCHEMA IF EXISTS `eol` ;
CREATE SCHEMA IF NOT EXISTS `eol` DEFAULT CHARACTER SET latin1 ;
USE `eol` ;

-- -----------------------------------------------------
-- Table `eol`.`Languages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Languages` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Languages` (
  `idLanguage` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Language''s ID' ,
  `alias` CHAR(5) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'Language''s alias (e.g. en_UK)' ,
  `description` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT 'Language''s description' ,
  PRIMARY KEY (`idLanguage`) ,
  UNIQUE INDEX `alias_UNIQUE` (`alias` ASC) )
ENGINE = InnoDB

 
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Availables languages';


-- -----------------------------------------------------
-- Table `eol`.`Subjects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Subjects` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Subjects` (
  `idSubject` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Subject''s ID' ,
  `name` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'Subject''s name' ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT 'Subject''s description' ,
  `fkLanguage` INT(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Main language''s ID' ,
  PRIMARY KEY (`idSubject`) ,
  INDEX `fk_Subjects_Language_idx` (`fkLanguage` ASC) ,
  CONSTRAINT `fk_Subjects_Language`
    FOREIGN KEY (`fkLanguage` )
    REFERENCES `eol`.`Languages` (`idLanguage` )
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Subjects';


-- -----------------------------------------------------
-- Table `eol`.`Topics`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Topics` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Topics` (
  `idTopic` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Topic''s ID' ,
  `name` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'Topic''s name' ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT 'Topic''s description' ,
  `fkSubject` INT(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Subject''s ID' ,
  PRIMARY KEY (`idTopic`) ,
  INDEX `fk_Topics_Subjects_idx` (`fkSubject` ASC) ,
  CONSTRAINT `fk_Topics_Subjects`
    FOREIGN KEY (`fkSubject` )
    REFERENCES `eol`.`Subjects` (`idSubject` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Topics respective to a subject';


-- -----------------------------------------------------
-- Table `eol`.`Questions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Questions` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Questions` (
  `idQuestion` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Question''s ID' ,
  `type` CHAR(5) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'Question''s type' ,
  `difficulty` TINYINT(4) UNSIGNED NOT NULL COMMENT 'Question''s difficulty' ,
  `status` CHAR(1) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'i' COMMENT 'Question''s status (a->active, i->inactive, e->error)' ,
  `extra` varchar(10) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Question''s extra (c->calculator, p->periodic table, ...)',
  `shortText` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT 'Question''s description' ,
  `fkRootQuestion` int(10) unsigned DEFAULT '0' COMMENT 'Root question''s ID',
  `fkTopic` INT(10) UNSIGNED DEFAULT NULL COMMENT 'Topic''s ID' ,
  PRIMARY KEY (`idQuestion`) ,
  INDEX `fk_Questions_Topics_idx` (`fkTopic` ASC) ,
  CONSTRAINT `fk_Questions_Topics`
    FOREIGN KEY (`fkTopic` )
    REFERENCES `eol`.`Topics` (`idTopic` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Questions relevant to a topic';


-- -----------------------------------------------------
-- Table `eol`.`Answers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Answers` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Answers` (
  `idAnswer` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Answer''s ID' ,
  `score` VARCHAR(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT 'Answer''s score',
  `fkQuestion` INT(10) UNSIGNED NOT NULL COMMENT 'Question''s ID' ,
  PRIMARY KEY (`idAnswer`) ,
  INDEX `fk_Answers_Questions_idx` (`fkQuestion` ASC) ,
  CONSTRAINT `fk_Answers_Question`
    FOREIGN KEY (`fkQuestion` )
    REFERENCES `eol`.`Questions` (`idQuestion` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Answers respective to a question';


-- -----------------------------------------------------
-- Table `eol`.`TestSettings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`TestSettings` ;

CREATE  TABLE IF NOT EXISTS `eol`.`TestSettings` (
  `idTestSetting` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Test setting''s ID' ,
  `name` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'Test setting''s name' ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT 'Test setting''s description' ,
  `questions` SMALLINT(5) UNSIGNED NOT NULL COMMENT 'Test setting''s question''s number' ,
  `scoreType` VARCHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' COMMENT 'Test setting''s score type' ,
  `scoreMin` VARCHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' COMMENT 'Test setting''s minimum score' ,
  `scale` DOUBLE NOT NULL DEFAULT '1' COMMENT 'Test setting''s score factor scale',
  `bonus` DOUBLE NULL DEFAULT '0' COMMENT 'Test setting''s bonus' ,
  `duration` INT(10) UNSIGNED NOT NULL COMMENT 'Test setting''s duration time in mins' ,
  `numEasy` INT(10) NOT NULL COMMENT 'Test setting''s easy questions' ,
  `numMedium` INT(10) NOT NULL COMMENT 'Test setting''s medium questions' ,
  `numHard` INT(10) NOT NULL COMMENT 'Test setting''s hard questions' ,
  `fkSubject` INT(10) UNSIGNED NOT NULL COMMENT 'Test setting''s subject' ,
  PRIMARY KEY (`idTestSetting`) ,
  INDEX `fk_TestSettings_Subject_idx` (`fkSubject` ASC) ,
  CONSTRAINT `fk_TestSettings_Subject`
    FOREIGN KEY (`fkSubject` )
    REFERENCES `eol`.`Subjects` (`idSubject` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Configuration setting of exam for specific subject';


-- -----------------------------------------------------
-- Table `eol`.`Exams`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Exams` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Exams` (
  `idExam` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Exam''s ID' ,
  `name` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'Exam''s name' ,
  `datetime` DATETIME NOT NULL COMMENT 'Exam''s day and time' ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT 'Exam''s description' ,
  `regStart` DATETIME NULL DEFAULT NULL COMMENT 'Exam''s registration start time' ,
  `regEnd` DATETIME NULL DEFAULT NULL COMMENT 'Exam''s registration end time' ,
  `password` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'Exam''s password' ,
  `status` CHAR(1) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'w' COMMENT 'Exam''s status (w -> waiting, s -> started, e -> ended, a -> archived)' ,
  `fkTestSetting` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Exam''s test setting' ,
  `fkSubject` INT(10) UNSIGNED NOT NULL COMMENT 'Exam''s subject' ,
  PRIMARY KEY (`idExam`) ,
  INDEX `fk_Exams_TestSetting_idx` (`fkTestSetting` ASC) ,
  INDEX `fk_Exams_Subject_idx` (`fkSubject` ASC) ,
  CONSTRAINT `fk_Exams_Subject`
    FOREIGN KEY (`fkSubject` )
    REFERENCES `eol`.`Subjects` (`idSubject` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Exams_TestSetting`
    FOREIGN KEY (`fkTestSetting` )
    REFERENCES `eol`.`TestSettings` (`idTestSetting` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Teacher''s Exams';


-- -----------------------------------------------------
-- Table `eol`.`Rooms`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Rooms` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Rooms` (
  `idRoom` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Room''s ID' ,
  `name` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'Room''s name' ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT 'Room''s description' ,
  `ipStart` INT(10) UNSIGNED NOT NULL COMMENT 'Room''s clients IP start' ,
  `ipEnd` INT(10) UNSIGNED NOT NULL COMMENT 'Room''s clients IP end' ,
  PRIMARY KEY (`idRoom`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Clients allowed to execute the exams';


-- -----------------------------------------------------
-- Table `eol`.`Exams_Rooms`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Exams_Rooms` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Exams_Rooms` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `fkExam` INT(10) UNSIGNED NOT NULL COMMENT 'Exam''s ID' ,
  `fkRoom` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Room''s ID' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Exams_Rooms_Room_idx` (`fkRoom` ASC) ,
  INDEX `fk_Exams_Rooms_Exam_idx` (`fkExam` ASC) ,
  CONSTRAINT `fk_Exams_Rooms_Exam`
    FOREIGN KEY (`fkExam` )
    REFERENCES `eol`.`Exams` (`idExam` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Exams_Rooms_Room`
    FOREIGN KEY (`fkRoom` )
    REFERENCES `eol`.`Rooms` (`idRoom` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Clients allowed to execute a specific exams';


-- -----------------------------------------------------
-- Table `eol`.`Questions_TestSettings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Questions_TestSettings` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Questions_TestSettings` (
  `fkQuestion` INT(10) UNSIGNED NOT NULL COMMENT 'Question''s ID' ,
  `fkTestSetting` INT(10) UNSIGNED NOT NULL COMMENT 'Test Setting''s ID',
  PRIMARY KEY (`fkQuestion`, `fkTestSetting`) ,
  INDEX `fk_Questions_TestSettings_TestSetting` (`fkTestSetting` ASC) ,
  CONSTRAINT `fk_Questions_TestSettings_Question`
    FOREIGN KEY (`fkQuestion` )
    REFERENCES `eol`.`Questions` (`idQuestion` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Questions_TestSettings_TestSetting`
    FOREIGN KEY (`fkTestSetting` )
    REFERENCES `eol`.`TestSettings` (`idTestSetting` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `eol`.`Sets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Sets` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Sets` (
  `idSet` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Set''s ID' ,
  `assigned` CHAR(1) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'n' COMMENT 'Check if set is assigned (y) or not (n)' ,
  `fkExam` INT(10) UNSIGNED NOT NULL COMMENT 'Exam''s ID ' ,
  PRIMARY KEY (`idSet`) ,
  INDEX `fk_Sets_Exam_idx` (`fkExam` ASC) ,
  CONSTRAINT `fk_Sets_Exam`
    FOREIGN KEY (`fkExam` )
    REFERENCES `eol`.`Exams` (`idExam` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Question sets';


-- -----------------------------------------------------
-- Table `eol`.`Sets_Questions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Sets_Questions` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Sets_Questions` (
  `fkSet` INT(10) UNSIGNED NOT NULL COMMENT 'Set''s ID' ,
  `fkQuestion` INT(10) UNSIGNED NOT NULL COMMENT 'Question''s ID' ,
  `answer` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT 'ID or text for answered question in this set' ,
  `fkIdLanguage` INT(10) UNSIGNED REFERENCES Languages(idLanguage),
  PRIMARY KEY (`fkSet`, `fkQuestion`) ,
  INDEX `fk_Sets_Questions_Set_idx` (`fkSet` ASC) ,
  INDEX `fk_Sets_Questions_Question_idx` (`fkQuestion` ASC) ,
  CONSTRAINT `fk_Sets_Questions_Question`
    FOREIGN KEY (`fkQuestion` )
    REFERENCES `eol`.`Questions` (`idQuestion` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Sets_Questions_Set`
    FOREIGN KEY (`fkSet` )
    REFERENCES `eol`.`Sets` (`idSet` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association between Questions and test''s sets';


-- -----------------------------------------------------
-- Table `eol`.`Users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Users` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Users` (
  `idUser` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'User''s ID' ,
  `name` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'User''s name' ,
  `surname` VARCHAR(35) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'User''s surname' ,
  `email` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'User''s email' ,
  `password` CHAR(40) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' COMMENT 'User''s SHA-1 password (40B length)' ,
  `role` VARCHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 's' COMMENT 'User''s role (a->admin, t->teacher, s->student, at->teacher and admin)' ,
  `fkLanguage` INT(10) UNSIGNED NULL DEFAULT '1' COMMENT 'User''s Language ID' ,
  PRIMARY KEY (`idUser`) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) ,
  INDEX `fk_Users_Languages_idx` (`fkLanguage` ASC) ,
  CONSTRAINT `fk_Users_Languages`
    FOREIGN KEY (`fkLanguage` )
    REFERENCES `eol`.`Languages` (`idLanguage` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Teachers and system admins';


-- -----------------------------------------------------
-- Table `eol`.`Tests`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Tests` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Tests` (
  `idTest` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Test''s ID',
  `timeStart` DATETIME DEFAULT NULL COMMENT 'Test''s start time',
  `timeEnd` DATETIME DEFAULT NULL COMMENT 'Test''s end time',
  `scoreTest` DOUBLE DEFAULT NULL COMMENT 'Test''s score',
  `bonus` DOUBLE DEFAULT '0' COMMENT 'Test''s assigned bonus',
  `scoreFinal` DOUBLE DEFAULT NULL COMMENT 'Final score',
  `status` CHAR(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'w' COMMENT 'Test''s status (w->waiting, s->started, e->ended, a->archived, b->blocked)',
  `fkExam` INT(10) UNSIGNED DEFAULT NULL COMMENT 'Test exam''s ID',
  `fkUser` INT(10) UNSIGNED DEFAULT NULL COMMENT 'Test student''s ID',
  `fkSet` INT(10) UNSIGNED DEFAULT NULL COMMENT 'Test''s set',
  PRIMARY KEY (`idTest`) KEY_BLOCK_SIZE=16,
  KEY `fk_Tests_Exam_idx` (`fkExam`) KEY_BLOCK_SIZE=16,
  KEY `fk_Tests_Set_idx` (`fkSet`) KEY_BLOCK_SIZE=16,
  KEY `fk_Tests_User_idx` (`fkUser`),
  CONSTRAINT `fk_Tests_Exam`
    FOREIGN KEY (`fkExam`)
    REFERENCES `Exams` (`idExam`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Tests_Set`
    FOREIGN KEY (`fkSet`)
    REFERENCES `Sets` (`idSet`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Tests_User`
    FOREIGN KEY (`fkUser`)
    REFERENCES `Users` (`idUser`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARSET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Test performed by a student in a exact exam';


-- -----------------------------------------------------
-- Table `eol`.`History`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`History` ;

CREATE  TABLE IF NOT EXISTS `eol`.`History` (
  `idHistory` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'History''s ID' ,
  `fkTest` INT(10) UNSIGNED DEFAULT NULL COMMENT 'Test''s ID' ,
  `fkQuestion` INT(10) UNSIGNED NOT NULL COMMENT 'Question''s ID' ,
  `answer` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT 'Text of open answer question' ,
  `score` VARCHAR(5) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT 'Value of submitted answer' ,
  PRIMARY KEY (`idHistory`)  KEY_BLOCK_SIZE=16,
  INDEX `fk_History_Test_idx` (`fkTest` ASC) ,
  INDEX `fk_History_Question_idx` (`fkQuestion` ASC) ,
  CONSTRAINT `fk_History_Test`
    FOREIGN KEY (`fkTest` )
    REFERENCES `eol`.`Tests` (`idTest` )
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_History_Question`
    FOREIGN KEY (`fkQuestion` )
    REFERENCES `eol`.`Questions` (`idQuestion` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'History of all questions and submitted answers for all tests';


-- -----------------------------------------------------
-- Table `eol`.`Tokens`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Tokens` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Tokens` (
  `email` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'User''s email' ,
  `action` CHAR(1) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'c' COMMENT 'Token''s action (c->create, p->password)' ,
  `value` CHAR(40) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'Token''s SHA-1 value (40B length)' ,
  PRIMARY KEY (`email`, `action`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Tokens for create account and password lost';


-- -----------------------------------------------------
-- Table `eol`.`Topics_TestSettings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Topics_TestSettings` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Topics_TestSettings` (
  `fkTestSetting` INT(10) UNSIGNED NOT NULL COMMENT 'Test Setting''s ID' ,
  `fkTopic` INT(10) UNSIGNED NOT NULL COMMENT 'Topic''s ID' ,
  `numEasy` INT(10) NOT NULL COMMENT 'Random easy questions' ,
  `numMedium` INT(10) NOT NULL COMMENT 'Random medium questions' ,
  `numHard` INT(10) NOT NULL COMMENT 'Random hard questions' ,
  `numQuestions` INT(10) NOT NULL COMMENT 'Number of topic questions' ,
  PRIMARY KEY (`fkTestSetting`, `fkTopic`) ,
  INDEX `fk_Topics_TestSettings_Topic` (`fkTopic` ASC) ,
  CONSTRAINT `fk_Topics_TestSettings_TestSetting`
    FOREIGN KEY (`fkTestSetting` )
    REFERENCES `eol`.`TestSettings` (`idTestSetting` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Topics_TestSettings_Topic`
    FOREIGN KEY (`fkTopic` )
    REFERENCES `eol`.`Topics` (`idTopic` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `eol`.`TranslationAnswers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`TranslationAnswers` ;

CREATE  TABLE IF NOT EXISTS `eol`.`TranslationAnswers` (
  `fkAnswer` INT(10) UNSIGNED NOT NULL COMMENT 'Answer''s ID' ,
  `fkLanguage` INT(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Language''s ID' ,
  `translation` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'Answer''s translation text' ,
  PRIMARY KEY (`fkAnswer`, `fkLanguage`) ,
  INDEX `fk_TranslationAnswers_Answer_idx` (`fkAnswer` ASC) ,
  INDEX `fk_TranslationAnswers_Language_idx` (`fkLanguage` ASC) ,
  CONSTRAINT `fk_TranslationAnswers_Answer`
    FOREIGN KEY (`fkAnswer` )
    REFERENCES `eol`.`Answers` (`idAnswer` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_TranslationAnswers_Language`
    FOREIGN KEY (`fkLanguage` )
    REFERENCES `eol`.`Languages` (`idLanguage` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Answers with they''re translations';


-- -----------------------------------------------------
-- Table `eol`.`TranslationQuestions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`TranslationQuestions` ;

CREATE  TABLE IF NOT EXISTS `eol`.`TranslationQuestions` (
  `fkQuestion` INT(10) UNSIGNED NOT NULL COMMENT 'Question''s ID' ,
  `fkLanguage` INT(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Language''s ID' ,
  `translation` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'Question''s translation text' ,
  PRIMARY KEY (`fkLanguage`, `fkQuestion`) ,
  INDEX `fk_TranslationQuestions_Question_idx` (`fkQuestion` ASC) ,
  INDEX `fk_TranslationQuestions_Language_idx` (`fkLanguage` ASC) ,
  CONSTRAINT `fk_TranslationQuestions_Language`
    FOREIGN KEY (`fkLanguage` )
    REFERENCES `eol`.`Languages` (`idLanguage` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_TranslationQuestions_Question`
    FOREIGN KEY (`fkQuestion` )
    REFERENCES `eol`.`Questions` (`idQuestion` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Questions with they''re translations';


-- -----------------------------------------------------
-- Table `eol`.`Users_Subjects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eol`.`Users_Subjects` ;

CREATE  TABLE IF NOT EXISTS `eol`.`Users_Subjects` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `fkUser` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'User''s ID' ,
  `fkSubject` INT(10) UNSIGNED NOT NULL COMMENT 'Subject''s ID',
  PRIMARY KEY (`id`) ,
  INDEX `fk_Teachers_Subjects_Subject_idx` (`fkSubject` ASC) ,
  INDEX `fk_Teachers_Subjects_Teacher_idx` (`fkUser` ASC) ,
  CONSTRAINT `fk_Users_Subjects_Subject`
    FOREIGN KEY (`fkSubject` )
    REFERENCES `eol`.`Subjects` (`idSubject` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Users_Subjects_User`
    FOREIGN KEY (`fkUser` )
    REFERENCES `eol`.`Users` (`idUser` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Relation between Teachers and Subjects';

USE `eol` ;

-- -----------------------------------------------------
-- Default language (English)
-- -----------------------------------------------------
INSERT INTO `Languages` (`idLanguage`, `alias`, `description`)
VALUES (1, 'en', 'English');

-- -----------------------------------------------------
-- Other languages
-- -----------------------------------------------------
INSERT INTO `Languages` (`idLanguage`, `alias`, `description`)
VALUES (2, 'it', 'Italiano');

-- -----------------------------------------------------
-- Insert syntax for test teacher account (password: testeol)
-- -----------------------------------------------------
INSERT INTO `Users` (`name`, `surname`, `email`, `password`, `role`, `fkLanguage`)
VALUES ('emanuele', 'gragnoli', 'emanuelegragnoli@live.it', '99053e95b3e682a0705474bb431f0041ece636b9', 'at', '1'),
('mario', 'rossi', 'mariorossi@live.it', 'bdcb71322f263c72d56717fb7e7238fc00473517', 's', '1'),
('echem', 'test', 'echem@test.org', '1b7b4b4203b28059f9eaf6f754b137d5e0a3ac8a', 'ae', '1');




ALTER TABLE TestSettings ADD negative BOOLEAN NULL DEFAULT 0 COMMENT 'Negative Scores';
ALTER TABLE TestSettings ADD editable BOOLEAN NULL DEFAULT 0 COMMENT 'Editable Scores';


ALTER TABLE Subjects ADD version REAL NULL DEFAULT -1 COMMENT 'Subjects version';
ALTER TABLE Topics ADD code VARCHAR(20) NULL DEFAULT NULL COMMENT 'Unique Code Topics Imported';

      ALTER TABLE Subjects
        ADD UNIQUE sbjC(name,fkLanguage,version);

        ALTER TABLE Topics
        ADD UNIQUE TopicC(code,fkSubject);

