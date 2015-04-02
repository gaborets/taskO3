<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 31.03.15
 * Time: 1:50
 */

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if(isset($_POST['num_comment']) && intval($_POST['num_comment']) > 0)
    {
      set_time_limit(60);
      for($i = 0; intval($_POST['num_comment'])> $i; $i++)
      {
          $sSql = "INSERT INTO `comments`(`m_id`, `c_id`, `text`, `c_date`) VALUES (".mt_rand(1,DB_MAX_USER).",".getCommentId().",'".md5(microtime())."',NOW())";
          Db::execute($sSql);
      }
    }
    header('Location:./index.php',false,301);
}

$sSQL = "SELECT `c`.`id`, `m`.`name`, `c`.`m_id`, `c`.`c_id`, `c`.`text`, `c`.`c_date` FROM `comments` AS `c` JOIN `member` AS `m` ON (`m`.`id` = `c`.`m_id`) WHERE 1";
$aData = Db::getAll($sSQL);


$aPrimary   = array();
$aFallow    = array();
$content    = '';

if(!empty($aData))
{
    foreach($aData as $key => $val)
    {
      if(intval($val['c_id']) == 0)
      {
          $aPrimary[] = array(
              'name'      => $val['name'],
              'comment'   => $val['text'],
              'c_date'    => date('Y-m-d', strtotime($val['c_date'])),
              'm_id'      => $val['m_id'],
              'id'        => $val['id'],
             );
      }
      else
      {
          $aFallow[intval($val['c_id'])][intval($val['id'])] = array(
              'name'      => $val['name'],
              'comment'   => $val['text'],
              'c_date'    => date('Y-m-d', strtotime($val['c_date'])),
              'm_id'      => $val['m_id'],
              'id'        => $val['id'],
            );
      }
    }

    if(!empty($aPrimary))
    {
        foreach($aPrimary as $key => $val)
            $content .= buildComments($val,$aFallow);
    }
}

function getCommentId()
{
    if(mt_rand(1,DB_MAX_USER) % 3 == 0)
    {
        $sSQl = 'SELECT `id` FROM `comments` ORDER BY RAND() LIMIT 1';
        return intval(Db::getOne($sSQl));
    }

    return 0;
}

function buildComments($aNeed = array(), $aBase = array(), $aParent = array())
{
   $sRet = '<ul class="f"><li><div class="n"><span>' . $aNeed['name'] .'</span>';

    if(is_array($aParent) && !empty($aParent))
      $sRet .=  ' told <span>' . $aParent['name'] .'</span>:</div>';
    else
      $sRet .=  ' said: </div>';

    $sRet .= '<div class="t">' . $aNeed['comment'] . '</div>';

    if(!empty($aNeed['id']) && array_key_exists(intval($aNeed['id']),$aBase))
    {
        $sRet .= '<ul class="s">';
        foreach($aBase[intval($aNeed['id'])] as $k => $item)
        {
            $sRet .= '<li>';
            if(array_key_exists(intval($item['id']),$aBase))
            {
                $sRet .=  buildComments($item,$aBase,$aNeed);
                continue;
            }
            $sRet .=  '<div class="n"><span>' . $item['name'] . '</span> told <span>' . $aNeed['name'] .'</span>:</div>';
            $sRet .=  '<div class="t">' . $item['comment'] . '</div></li>';
        }
        $sRet .= '</ul>';
    }

    return $sRet .= '</li></ul>';
}

$content = str_replace('#content#', $content, (string)file_get_contents('./template/index.html'));

