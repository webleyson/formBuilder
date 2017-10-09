<?php

/*
 * (c) Wyn Connections Limited
 *
 * PostgreSQL Library
 */
define('DATE_SQL', 'Y-m-d H:i:s');
define('DATE_SQL_SHORT', 'Y-m-d');


class DB {

    /**
     *
     * @return boolean|\PDO
     */
    public static function init() {
        if (!defined('SHOW_SQL_ERROR_QUERIES')) {
            define('SHOW_SQL_ERROR_QUERIES', false);
        }

        try {
            $db = new PDO('pgsql:host='.DB_HOST.' port=5432 dbname='.DB_NAME.' user='.DB_USER.' password='.DB_PASS);
        } catch (PDOException $e) {
            printf("<h1>DB Error</h1><p>Connect failed : There was a problem connecting to the %s database</p><p>%s</p>\n", DB_NAME, $e->getMessage());
            return false;
        }

        return $db;
    }

    public static function appendSiteCheck($sql){
      if(substr($sql, -1) == ";"){
        $sql = substr($sql, 0, -1);
      }
      //$site_id = ApiToken::getTokenSiteId();
      $site_id = 1;
      $extra = " AND \"site_id\" = {$site_id} ";
      if($strpos = stripos($sql, 'ORDER BY')){
        $str = substr($sql, 0, $strpos) . $extra . substr($sql, $strpos);
      }else{
        $sql = $sql . $extra;
      }
      return $sql;
    }

    /**
     *
     * @param string $sql
     * @return boolean|\PDOStatement a pg_query result or false on error
     */
    public static function query($sql, $checkSite = true) {

        $db = self::init();
        if ($db === false) {
            return false;
        }

        if($checkSite === true){
          $sql = DB::appendSiteCheck($sql);
        }
        $qry = $db->query($sql);
        if ($qry === false) {
            printf("<div style=\"text-align: left;\"><h1>DB Error</h1><p>Query Error :</p><p>%s</p><pre>Query: %s</pre></div>\n", print_r( $sql));
        }
        return $qry;
    }



    /**
     *
     * @param string $table
     * @param array $data
     * @param string $where
     * @return false|int
     */


    public static function update($table, $data, $where = false, $checkSite = false) {

        $sql = "UPDATE \"{$table}\" SET ";
        foreach ($data as $key => $val) {
            if (strtolower($val) == 'null' || $val === null) {
                $sql.= "\"{$key}\" = NULL, ";
            } elseif (strtolower($val) == 'now()') {
                $sql.= "\"{$key}\" = NOW(), ";
            } else {
                $sql.= "\"{$key}\"=" . self::escapestring($val) . ", ";
            }
        }
        $sql = rtrim($sql, ', ');
        if ($where) {
            $sql.= ' WHERE ' . $where . ';';
        }
        if($checkSite){
          $sql = DB::appendSiteCheck($sql);
        }


        $qry = self::query($sql, false);
        if ($qry !== false) {
            return $qry->rowCount();
        }
        return false;
    }

    /**
     *
     * @param string $table
     * @param array $data
     * @return boolean|int
     */
    public static function insert($table, $data, $return = true) {
        $sql = "INSERT INTO \"{$table}\" ";
        $values = '';
        $fields = '';

        foreach ($data as $key => $val) {
            $fields.="\"{$key}\", ";
            if (strtolower($val) == 'null' || $val === null) {
                $values.="NULL, ";
            } elseif (strtolower($val) == 'now()') {
                $values.="NOW(), ";
            } else {
                $values.= self::escapestring($val) . ", ";
            }
        }

        $sql .= "(" . rtrim($fields, ', ') . ") VALUES (" . rtrim($values, ', ') . ")";
        //die( "<pre>".print_r($sql,true)."</pre>");
        //echo "<pre>".print_r($sql,true)."</pre>";
        if($return){
            $sql .= ' RETURNING id;';
        }

        $qry = self::query($sql, false);
        if ($qry !== false) {
            $row = $qry->fetch();
            return $row[0];
        }
    }

    public static function value($sql, $checkSite = true) {
        $result = null;

        if($checkSite){
          $sql = DB::appendSiteCheck($sql);
        }

        $qry = self::query($sql, $checkSite);
        if ($qry->rowCount() === 1) {
            $row = $qry->fetch();
            $result = $row[0];
        }

        return $result;
    }

    public static function escapestring($inStr) {
        $db = self::init();
        $outStr = $db->quote($inStr);
        return $outStr;
    }

    public static function sql_date($date = null) {
        if ($date == null || !is_numeric($date)) {
            $date = time();
        }

        return date(SQL_DATE, $date);
    }
}
