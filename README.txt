*Updated 24/07/2019* Detailed installation instructions from nil
-----------------------------------------------------------------------------
sudo apt update
sudo apt-get upgrade
sudo apt install apache2
sudo apt install postgresql
sudo apt install php libapache2-mod-php
-> nav to /var/www/
sudo chmod -R 777 .
-> fileZilla delete html/index.html
-> fileZilla paste src/www/htdocs contents into html folder
-> fileZilla paste src/www/htdocs_private into www folder
sudo apt-get install php-ldap
sudo apt-get install php-pgsql
sudo apt-get install php-memcached
sudo apt-get install memcached
sudo /etc/init.d/apache2 restart
sudo vim /etc/php/7.0/apache2/php.ini
-> add extension=php_ldap.dll and extension=php_pdo_pgsql.dll to extensions and save+exit
sudo -u postgres psql
create database mydb;
create user myuser with encrypted password 'redacted';
exit
psql -h localhost -U myuser -d mydb
-> separately, access file /setup_postgresql
Add: insert into USERS (USERID, FIRSTNAME, LASTNAME) values ('your_username', 'your_firstname', 'your_lastname');
Add: insert into USERGROUPS (CLASSID, USERID, GROUPID) values (1, 'your_username', 4);
paste entire contents of the sql file into the terminal window and run
exit
Update /var/www/htdocs_private/data/DBConnection.php to contain correct database details.
Set all file permissions to 744 and folder permissions to 755.
-----------------------------------------------------------------------------

General structure notes:
1. The main technical work is located in the src/www directory location.
2. Within the folder described, htdocs contains the main website content and htdocs_private contains sensitive connection files.
3. Test files are stored in src/tests with sub-directories as appropriate.
4. At root, the the file example_students.csv can be used during class creation.
5. At root, the the file example_grade_document.csv can be used to test GC document amending. (The class must have the same worksheet structure.)
6. At root, the setup_postgresql.sql file contains the database commands used to initialise the database for use.


Server installation instructions:
1. Install default configurations of Apache, PHP (7+) and PostgreSQL (9+) on a Aberystwyth University networked Debian container (ideally SSL certified). 
2. sudo apt-get install php-ldap
3. sudo apt-get install php-pgsql
4. sudo apt-get install php-memcached
5. sudo apt-get install memcached
6. sudo /etc/init.d/memcached start
7. sudo vim /etc/php/7.0/apache2/php.ini
8. uncomment extension=php_ldap.dll and extension=php_pdo_pgsql.dll, then save and quit.
9. sudo /etc/init.d/apache2 restart
10. Change src/www/htdocs_private/data DBConnection and MemcachedConnection to use relevant details.
11. Move src/www/htdocs contents to Apache's html folder.
12. Move src/www/htdocs_private folder to the directory containing the html folder.
13. Set all file permissions to 744 and folder permissions to 755. More strict PHP permissions such as 700 could be used if PHP is configured to run as the user. This has not been tested.


PostgreSQL instructions:
1. Connect to the database server using a terminal.
2. Enter the database (psql -h hostname -U username -d database_name) and supply your password.
3. Amend the contents of /setup_postgresql to contain the following details.
4. Add insert into USERS (USERID, FIRSTNAME, LASTNAME) values ('your_username', 'your_firstname', 'your_lastname');
5. Add insert into USERGROUPS (CLASSID, USERID, GROUPID) values (1, 'your_username', 4);
6. Paste the entire contents of the sql file into the terminal window and run the commands. 
7. You may now log into the system as an admin.


JavaScript (Jest) testing: 
1. Install node.js
2. Recommended you run 'npm install -g jest' command in terminal.
3. In src run 'npm run test' to run the test suite.


Selenium testing (edits required):
0. Requires database used to reference placeholder-module structure as defined in provided starter SQL commands file.
1. Edit /src/www/scripts/User.php so that checkLogin() function performs '$this->isAuthenticated = true' and comment out the use of checkldap.
1. Install Selenium IDE for a browser such as Google Chrome.
2. 'Open an existing project', select the file located in src/tests/Selenium.
3. Adjust playback base URL if current URL is not relevant or if using a development environment (which is recommended.) 
4. Select 'Run all tests' option to run interaction tests.
5. Revert changes to User.php when complete.


PHPUnit testing:
1. OS specific, run from terminal in src folder:
.\vendor\bin\phpunit (Windows)
./vendor/bin/phpunit (Unix, untested.)