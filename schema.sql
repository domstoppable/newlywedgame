DROP DATABASE NewlyWedGame;
CREATE DATABASE IF NOT EXISTS NewlyWedGame;
USE NewlyWedGame;

CREATE TABLE IF NOT EXISTS players (
	playerID INT PRIMARY KEY AUTO_INCREMENT,
	lastName VARCHAR(128),
	firstName VARCHAR(128),
	gender ENUM('female', 'male')
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS teams (
	person1ID INT NOT NULL,
	person2ID INT NOT NULL,
	ordinal INT NOT NULL,
	score INT NOT NULL,
	FOREIGN KEY (person1ID) REFERENCES players(playerID),
	FOREIGN KEY (person2ID) REFERENCES players(playerID)
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS questions (
	questionID VARCHAR(5) PRIMARY KEY,
	question VARCHAR(512),
	round INT NOT NULL,
	gender ENUM('female', 'male'),
	ordinal INT NOT NULL,
	questionType ENUM('multiple-choice', 'short-answer')
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS answerOptions (
	optionID INT PRIMARY KEY AUTO_INCREMENT,
	questionID VARCHAR(5) NOT NULL,
	answer VARCHAR(128),
	ordinal INT NOT NULL,
	FOREIGN KEY (questionID) REFERENCES questions(questionID)
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS answerChoices (
	questionID VARCHAR(5) NOT NULL,
	playerID INT NOT NULL,
	optionID INT NOT NULL,
	FOREIGN KEY (questionID) REFERENCES questions(questionID),
	FOREIGN KEY (playerID) REFERENCES players(playerID),
	FOREIGN KEY (optionID) REFERENCES answerOptions(optionID)
) ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS shortAnswers (
	questionID VARCHAR(5) NOT NULL,
	playerID INT NOT NULL,
	answer VARCHAR(1024),
	FOREIGN KEY (questionID) REFERENCES questions(questionID),
	FOREIGN KEY (playerID) REFERENCES players(playerID)
) ENGINE=INNODB;

CREATE USER 'NewlyWedDBUser'@'localhost' IDENTIFIED BY 'dBX4H5x7gHLVwMMN';
GRANT SELECT, INSERT ON NewlyWedGame.* TO NewlyWedDBUser;


INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1m1', '1m1 Test Question', 1, 'male', 1, 'multiple-choice');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1m2', '1m2 Test Question', 1, 'male', 2, 'multiple-choice');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1m3', '1m3 Test Question', 1, 'male', 3, 'multiple-choice');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1m4', '1m4 Test Question', 1, 'male', 4, 'multiple-choice');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1f1', '1f1 Test Question', 1, 'female', 1, 'multiple-choice');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1f2', '1f2 Test Question', 1, 'female', 2, 'multiple-choice');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1f3', '1f3 Test Question', 1, 'female', 3, 'multiple-choice');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1f4', '1f4 Test Question', 1, 'female', 4, 'multiple-choice');

INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2m1', '2m1 Test Question', 2, 'male', 1, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2m2', '2m2 Test Question', 2, 'male', 2, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2m3', '2m3 Test Question', 2, 'male', 3, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2m4', '2m4 Test Question', 2, 'male', 4, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2f1', '2f1 Test Question', 2, 'female', 1, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2f2', '2f2 Test Question', 2, 'female', 2, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2f3', '2f3 Test Question', 2, 'female', 3, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2f4', '2f4 Test Question', 2, 'female', 4, 'short-answer');

INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('3m1', '3m1 Test Question', 3, 'male', 1, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('3f1', '3f1 Test Question', 3, 'female', 1, 'short-answer');

INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m1', '1m1 Choice 1', 1);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m1', '1m1 Choice 2', 2);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m1', '1m1 Choice 3', 3);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m2', '1m2 Choice 1', 1);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m2', '1m2 Choice 2', 2);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m2', '1m2 Choice 3', 3);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m3', '1m3 Choice 1', 1);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m3', '1m3 Choice 2', 2);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m3', '1m3 Choice 3', 3);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m4', '1m4 Choice 1', 1);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m4', '1m4 Choice 2', 2);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m4', '1m4 Choice 3', 3);

INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f1', '1f1 Choice 1', 1);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f1', '1f1 Choice 2', 2);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f1', '1f1 Choice 3', 3);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f2', '1f2 Choice 1', 1);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f2', '1f2 Choice 2', 2);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f2', '1f2 Choice 3', 3);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f3', '1f3 Choice 1', 1);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f3', '1f3 Choice 2', 2);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f3', '1f3 Choice 3', 3);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f4', '1f4 Choice 1', 1);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f4', '1f4 Choice 2', 2);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f4', '1f4 Choice 3', 3);

