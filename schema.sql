DROP DATABASE IF EXISTS NewlyWedGame;
CREATE DATABASE IF NOT EXISTS NewlyWedGame;
USE NewlyWedGame;

CREATE TABLE IF NOT EXISTS system (
	activeQuestionID VARCHAR(5) DEFAULT NULL,
	activePlayerID INT DEFAULT NULL,
	FOREIGN KEY (activeQuestionID) REFERENCES questions(questionID),
	FOREIGN KEY (activePlayerID) REFERENCES players(playerID)
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS players (
	playerID INT PRIMARY KEY AUTO_INCREMENT,
	lastName VARCHAR(128),
	firstName VARCHAR(128),
	gender ENUM('female', 'male')
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS teams (
	player1ID INT NOT NULL,
	player2ID INT NOT NULL,
	ordinal INT NOT NULL,
	score INT NOT NULL,
	FOREIGN KEY (player1ID) REFERENCES players(playerID),
	FOREIGN KEY (player2ID) REFERENCES players(playerID)
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

DROP USER 'NewlyWedDBUser'@'localhost';
CREATE USER 'NewlyWedDBUser'@'localhost' IDENTIFIED BY 'dBX4H5x7gHLVwMMN';
GRANT ALL PRIVILEGES ON NewlyWedGame.* TO NewlyWedDBUser;

INSERT INTO system () VALUES ();

INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1m1', 'What household chore are you most likely to do?', 1, 'male', 1, 'multiple-choice');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1m2', 'What is your wife&rsquo;s/significant other&rsquo;s guilty pleasure', 1, 'male', 2, 'multiple-choice');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1m3', 'How many dates did you go on with your wife/significant other before sharing your first kiss?', 1, 'male', 3, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1m4', 'What is your wife&rsquo;s/significant other&rsquo;s best feature?', 1, 'male', 4, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1f1', 'If your husband/significant other had a night out &quot;with the boys&quot; where would they go?', 1, 'female', 1, 'multiple-choice');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1f2', 'How will your husband/significant other describe you when you wake up in the morning? Will he say you are:', 1, 'female', 2, 'multiple-choice');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1f3', 'What is your term of endearment for him?', 1, 'female', 3, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('1f4', 'What is your husband&rsquo;s most prized possession?', 1, 'female', 4, 'short-answer');

INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2m1', 'What is your favorite activity you like doing together?', 2, 'male', 1, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2m2', 'What about you annoys her the most?', 2, 'male', 2, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2m3', 'What can your wife/significant other not get enough of?', 2, 'male', 3, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2m4', 'What color panties is your wife/significant other wearing tonight?', 2, 'male', 4, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2f1', 'What does he do that always makes you feel better?', 2, 'female', 1, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2f2', 'If you asked your husband/significant other to cook dinner, what dish would he prepare?', 2, 'female', 2, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2f3', 'Where is your husband&rsquo;s/significant other&rsquo;s favorite place?', 2, 'female', 3, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('2f4', 'What can your husband/significant other not get enough of?', 2, 'female', 4, 'short-answer');

INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('3m1', 'What can the two of you just not agree on?', 3, 'male', 1, 'short-answer');
INSERT INTO questions (questionID, question, round, gender, ordinal, questionType) VALUES ('3f1', 'What has kept you coming back for more?', 3, 'female', 1, 'short-answer');

INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m1', 'the Laundry', 1);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m1', 'the Dishes', 2);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m1', 'Cleaning the bathroom', 3);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m2', 'Chocolate', 1);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m2', 'Wine', 2);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1m2', 'some form of Pampering', 3);

INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f1', 'a Sporting Event', 1);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f1', 'a Bar', 2);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f1', 'a Strip Club', 3);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f2', 'Grouchy', 1);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f2', 'Dazed', 2);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f2', 'Frisky', 3);
INSERT INTO answerOptions (questionID, answer, ordinal) VALUES ('1f2', 'a Ball of Energy', 4);

