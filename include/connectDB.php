<?php
   $host        = "host = 127.0.0.1";
   $port        = "port = 5432";
   $dbname      = "dbname = postgres";
   $credentials = "user = mattwebley password=Supplier123";

   $db = pg_connect( "$host $port $dbname $credentials"  );
   global $db;
   if(!$db) {
      echo "Error : Unable to open database\n";
   }
?>