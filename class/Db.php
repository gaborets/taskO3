<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 31.03.15
 * Time: 0:03
 */

class Db {

    static private $_db = null;

    public function __construct()
    {
        return null;
    }

    static public function connect()
    {
        self::$_db = new Mysqli(DB_SERVER,DB_USER_ROOT,DB_PASS,DB_NAME);

        if(self::$_db->connect_errno == 1049)
            self::_createDB();
    }

    static private function _createDB()
    {
        self::$_db = new mysqli(DB_SERVER, DB_USER_ROOT, DB_PASS);

        if (self::$_db->connect_error)
        {
           die('Не удалось создать БД');
        }

        $sSql = "CREATE DATABASE " . DB_NAME  . ' CHARACTER SET utf8 COLLATE utf8_general_ci';
        if(self::$_db->query($sSql) === TRUE)
        {
            self::_createTables();
        }
        else
            die('Не удалось создать БД '.__LINE__);

        return null;
    }

    static private function _createTables()
    {
        self::$_db = new mysqli(DB_SERVER, DB_USER_ROOT, DB_PASS, DB_NAME);
        if (self::$_db->connect_error)
            die('Не удалось создать БД '.__LINE__);


        $sSql = "CREATE TABLE  `comments` (`id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY ,
                    `m_id` INT(6) UNSIGNED NOT NULL ,
                    `c_id` INT(6) UNSIGNED NOT NULL ,
                    `text` TEXT NOT NULL ,
                    `c_date` TIMESTAMP)";

        if (self::$_db->query($sSql) === TRUE)
        {
            $sSql = "CREATE TABLE  `member` (`id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY ,
                    `name` VARCHAR(30) NOT NULL ,
                    `m_date` TIMESTAMP)";
            if(!self::$_db->query($sSql))
                die('Не удалось создать БД '.__LINE__);
            else
            {
              set_time_limit(60);

              for($i = 0; DB_MAX_USER > $i; $i++)
              {

                 $sSql = "INSERT INTO `member` (`name`, `m_date`) VALUES ('member_#" . ($i + 1) . "', NOW())";
                 if(!self::execute($sSql))
                   die('Не удалось создать БД '.__LINE__);
              }
            }
        }
        else
          die('Не удалось создать БД '.__LINE__);
    }

    static public function getAll($sSQL = '')
    {
        if(!is_string($sSQL) || empty($sSQL))
            return array();

        $rResult = self::$_db->query($sSQL);
        if($rResult->num_rows <= 0)
            return array();

        $aRet = array();
        while($aRow = $rResult->fetch_assoc())
        {
            foreach($aRow as $key => $val)
                $aTemp[$key] = $val;

            $aRet[] = (array)$aTemp;
        }

        return $aRet;
    }

    static public function getOne($sSQL = '')
    {
       if(!is_string($sSQL) || empty($sSQL))
          return '';

       $rResult = self::$_db->query($sSQL);
       if($rResult->num_rows <= 0)
         return '';

       while($aRow = $rResult->fetch_assoc())
       {
          foreach($aRow as $key => $val)
              return $val;
       }

       return '';
    }

    static public function execute($sSQL = '',$bRetID = false)
    {
        if(!is_string($sSQL) || empty($sSQL))
            return false;

        if(self::$_db->query($sSQL))
        {
            if($bRetID)
                return mysqli_insert_id(self::$_db);

            return true;
        }

        return false;
    }
} 