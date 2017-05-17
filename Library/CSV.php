<?php
/**
 * Description of csvFile
 *
 * @author soseki
 *
 *    How to use 
 *    $csv = new CSV("/home/soseki/デスクトップ/test_deliver_sample.csv");
 *    echo "<pre>";
 *    print_r($csv->getData());
 *    echo "</per>"; 
 * 
 * 
 */
require_once 'Files.php';

class CSV extends Files {
  public $data = null;

  public function getData() {
    $data = parent::getRawData();
    if( empty( $data ) ) {
      return false;
    }
    // Fix all break new line char into \n
    $data = trim(String::replace( array("\r\n","\r"), "\n", $data ) );
    $data = explode("\n",$data);  // Broken data by \n
    return ( $this->data = $data );
  }
}