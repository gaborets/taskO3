<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 31.03.15
 * Time: 0:09
 */
include_once('./config/config.php');
include_once('./class/Db.php');
class System {

 public $aUrl = array();

 public function __construct()
 {
     return null;
 }


 public function load()
 {
   Db::connect();
 }


 public function getContentPage()
 {
   $this->aUrl = explode('/',$_SERVER['REQUEST_URI']);
   $this->aUrl = array_values(array_filter($this->aUrl));

   if(end($this->aUrl) !== false && strpos(end($this->aUrl),'.') !== false)
     $this->aUrl[array_search(end($this->aUrl),$this->aUrl)] = substr(end($this->aUrl),0,strpos(end($this->aUrl),'.'));

   echo $this->_buildPage();
 }

 private function _buildPage()
 {
   if(empty($this->aUrl) || !is_array($this->aUrl))
     return '';

   if(isset($this->aUrl[1]))
   {
     if($this->aUrl[1] == 'index')
     {
        include_once('./module/comments.php');
        if(isset($content) && is_string($content))
          return $content;
        else
          return '';
     }
   }
   return '';
  }

}