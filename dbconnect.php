<?php
//I only use this for the log-in stuff.
 // this will avoid mysql_connect() deprecation error,
 error_reporting( ~E_ALL & ~E_DEPRECATED &  ~E_NOTICE );

 define('DBHOST', 'oniddb.cws.oregonstate.edu');
 define('DBUSER', 'minorc-db');
 define('DBPASS', 'kLdt7swbvLhRmjzu');
 define('DBNAME', 'minorc-db');

 $conn = mysql_connect(DBHOST,DBUSER,DBPASS);
 $dbcon = mysql_select_db(DBNAME);

 if ( !$conn ) {
  die("Connection failed : " . mysql_error());
 }

 if ( !$dbcon ) {
  die("Database Connection failed : " . mysql_error());
 }
