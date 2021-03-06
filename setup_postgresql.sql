drop table classes, users, groups, usergroups, worksheets, questions, parts, signoffs;



CREATE TABLE CLASSES(
   CLASSID SERIAL NOT NULL,
   MODULECODE VARCHAR NOT NULL,
   STARTYEAR INT NOT NULL,

   PRIMARY KEY (CLASSID),         
   UNIQUE (MODULECODE, STARTYEAR)
);


CREATE TABLE USERS(
   USERID VARCHAR NOT NULL,
   FIRSTNAME VARCHAR NOT NULL,
   LASTNAME VARCHAR NOT NULL,

   PRIMARY KEY (USERID)
);


CREATE TABLE GROUPS(
   GROUPID SERIAL NOT NULL,
   GROUPNAME VARCHAR NOT NULL,

   PRIMARY KEY (GROUPID),
   UNIQUE (GROUPNAME)
);


CREATE TABLE USERGROUPS(
   CLASSID INT NOT NULL,
   USERID VARCHAR NOT NULL,
   GROUPID INT,

   PRIMARY KEY (CLASSID, USERID),
   FOREIGN KEY (CLASSID) references CLASSES(CLASSID) ON DELETE CASCADE,
   FOREIGN KEY (USERID) references USERS(USERID) ON DELETE CASCADE,
   FOREIGN KEY (GROUPID) references GROUPS(GROUPID) ON DELETE CASCADE
);


CREATE TABLE WORKSHEETS(
   CLASSID INT NOT NULL,
   WORKSHEETNO INT NOT NULL,
   LOCKDATE DATE,

   PRIMARY KEY (CLASSID, WORKSHEETNO),
   FOREIGN KEY (CLASSID) references CLASSES(CLASSID) ON DELETE CASCADE
);


CREATE TABLE QUESTIONS(
   CLASSID INT NOT NULL,
   WORKSHEETNO INT NOT NULL,
   QUESTIONNO INT NOT NULL,

   PRIMARY KEY (CLASSID, WORKSHEETNO, QUESTIONNO),
   FOREIGN KEY (CLASSID) references CLASSES(CLASSID) ON DELETE CASCADE,
   FOREIGN KEY (CLASSID, WORKSHEETNO) references WORKSHEETS(CLASSID , WORKSHEETNO) ON DELETE CASCADE
);


CREATE TABLE PARTS(
   CLASSID INT NOT NULL,
   WORKSHEETNO INT NOT NULL,
   QUESTIONNO INT NOT NULL,
   QUESTIONPART INT NOT NULL,
   ANSWERWEIGHT INT NOT NULL,

   PRIMARY KEY (CLASSID, WORKSHEETNO, QUESTIONNO, QUESTIONPART),
   FOREIGN KEY (CLASSID) references CLASSES(CLASSID) ON DELETE CASCADE,
   FOREIGN KEY (CLASSID, WORKSHEETNO) references WORKSHEETS(CLASSID, WORKSHEETNO) ON DELETE CASCADE,
   FOREIGN KEY (CLASSID, WORKSHEETNO, QUESTIONNO) references QUESTIONS(CLASSID, WORKSHEETNO, QUESTIONNO) ON DELETE CASCADE
);


CREATE TABLE SIGNOFFS(
   USERID VARCHAR NOT NULL,
   CLASSID INT NOT NULL,
   WORKSHEETNO INT NOT NULL,
   QUESTIONNO INT NOT NULL,
   QUESTIONPART INT NOT NULL,
   PERCENTAGEDONE INT NOT NULL,

   PRIMARY KEY (USERID, CLASSID, WORKSHEETNO, QUESTIONNO, QUESTIONPART),
   FOREIGN KEY (USERID) references USERS(USERID) ON DELETE CASCADE,
   FOREIGN KEY (CLASSID) references CLASSES(CLASSID) ON DELETE CASCADE,
   FOREIGN KEY (CLASSID, WORKSHEETNO) references WORKSHEETS(CLASSID, WORKSHEETNO) ON DELETE CASCADE,
   FOREIGN KEY (CLASSID, WORKSHEETNO, QUESTIONNO) references QUESTIONS(CLASSID, WORKSHEETNO, QUESTIONNO) ON DELETE CASCADE,
   FOREIGN KEY (CLASSID, WORKSHEETNO, QUESTIONNO, QUESTIONPART) references PARTS(CLASSID, WORKSHEETNO, QUESTIONNO, QUESTIONPART) ON DELETE CASCADE
);


insert into CLASSES (MODULECODE, STARTYEAR) values ('class-placeholder', 2018);


insert into USERS (USERID, FIRSTNAME, LASTNAME) values ('admin', 'admin', 'admin');
insert into USERS (USERID, FIRSTNAME, LASTNAME) values ('demonstrator', 'demonstrator', 'demonstrator');
insert into USERS (USERID, FIRSTNAME, LASTNAME) values ('lecturer', 'lecturer', 'lecturer');
insert into USERS (USERID, FIRSTNAME, LASTNAME) values ('student', 'student', 'student');



insert into GROUPS (GROUPNAME) values ('Student');
insert into GROUPS (GROUPNAME) values ('Demonstrator');
insert into GROUPS (GROUPNAME) values ('Lecturer');
insert into GROUPS (GROUPNAME) values ('Admin');


insert into USERGROUPS (CLASSID, USERID, GROUPID) values (1, 'admin', 4);
insert into USERGROUPS (CLASSID, USERID, GROUPID) values (1, 'lecturer', 3);
insert into USERGROUPS (CLASSID, USERID, GROUPID) values (1, 'demonstrator', 2);
insert into USERGROUPS (CLASSID, USERID, GROUPID) values (1, 'student', 1);




insert into WORKSHEETS (CLASSID, WORKSHEETNO, LOCKDATE) values (1, 1, '2999-11-11');
insert into WORKSHEETS (CLASSID, WORKSHEETNO, LOCKDATE) values (1, 2, '2999-11-11');
insert into WORKSHEETS (CLASSID, WORKSHEETNO, LOCKDATE) values (1, 3, '2999-11-11');
insert into QUESTIONS (CLASSID, WORKSHEETNO, QUESTIONNO) values (1, 1, 1);
insert into QUESTIONS (CLASSID, WORKSHEETNO, QUESTIONNO) values (1, 2, 1);
insert into QUESTIONS (CLASSID, WORKSHEETNO, QUESTIONNO) values (1, 2, 2);
insert into QUESTIONS (CLASSID, WORKSHEETNO, QUESTIONNO) values (1, 3, 1);

insert into PARTS (CLASSID, WORKSHEETNO, QUESTIONNO, QUESTIONPART, ANSWERWEIGHT) values (1, 1, 1, 1, 10);
insert into PARTS (CLASSID, WORKSHEETNO, QUESTIONNO, QUESTIONPART, ANSWERWEIGHT) values (1, 2, 1, 1, 10);
insert into PARTS (CLASSID, WORKSHEETNO, QUESTIONNO, QUESTIONPART, ANSWERWEIGHT) values (1, 2, 2, 1, 10);
insert into PARTS (CLASSID, WORKSHEETNO, QUESTIONNO, QUESTIONPART, ANSWERWEIGHT) values (1, 3, 1, 1, 10);
insert into PARTS (CLASSID, WORKSHEETNO, QUESTIONNO, QUESTIONPART, ANSWERWEIGHT) values (1, 3, 1, 2, 10);