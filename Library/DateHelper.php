<?php

/**
 * Description of DateHelper
 *
 * Created by Masao Soseki
 */
class DateTimeHelper {
  //put your code here

  public static $day = array( "日", "月", "火", "水", "木", "金", "土" );
  public static $date = array("月", "日");

  public function today(){return date('Y/m/d H:i:s');}
  
  public function time(){return date('H:i:s');}

  public static function jpDateFormat( $arg ){
    $tm = strtotime($arg);

    $w = date("w", $tm);

    $wd = self::$day[ $w ];

    $ret = date('Y/m/d', $tm);
    $ret .= '(' . $wd . ') ';

    return $ret;
  }

  public static function commonDateFormat( $arg ){
    $dt = new DateTime($arg);
    if( $dt->format('Y-m-d') == date('Y-m-d') ){
      $ret = $dt->format( 'H:i');
    } else {
      $ret = $dt->format( 'y/m/d');
    }
    return $ret;
  }

  public static function commonTimeFormat( $arg ){
    $dt = strtotime($arg);
    return date( 'H:i', $dt);
  }
}
?>