<?php
/**
 * Description of Files
 *
 * @author soseki
 */
abstract class Files {
  public $name = null;      // ファイルPath
  public $raw_data = null;  // データ

  public function __construct( $inName = null ) {
    if( $inName !== null ){
      $this->setName($inName);
      $this->setRawData();
    }
  }

  public function setName( $inName ){
    $this->name = $inName;
  }
  
  public function setRawData(){
    if( $this->name !== null){
      $this->raw_data = file_get_contents($this->name);
    }
  }
  
  public function getName(){ return $this->name; }
  public function getRawData(){ return $this->raw_data; }
}

?>
