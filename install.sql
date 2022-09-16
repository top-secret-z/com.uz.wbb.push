DROP TABLE IF EXISTS wbb1_thread_push;
CREATE TABLE wbb1_thread_push (
	threadPushID	INT(10) AUTO_INCREMENT PRIMARY KEY,
	time			INT(10) NOT NULL DEFAULT 0,
	userID			INT(10),
	threadID		INT(10),
	
	UNIQUE KEY (userID, threadID)
);

ALTER TABLE wbb1_board ADD threadPushEnable TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE wbb1_thread_push ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE wbb1_thread_push ADD FOREIGN KEY (threadID) REFERENCES wbb1_thread (threadID) ON DELETE CASCADE;
