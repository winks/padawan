<?php
$dbh = mysql_connect("localhost", "user", "password");
mysql_select_db("mydb");
$query = mysql_query("SELECT * FROM table");

?>
