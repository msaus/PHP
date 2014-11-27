<?php

interface connect{
  public function login();
}

abstract class connectionObj{
  
  // ログイン情報
  private $login_id = null;
  private $password = null;
  
  // 接続先URL
  private $url = null;  
  
  // 接続ID
  static $conn_id = null;
  

  
  /*************************
   * Setter
   *************************/
  /**
   * ログインID設定
   * 
   * @param $inLoginId ログインID
   * @return none
   */
  public function setLoginId( $inLoginId ){
    $this->login_id = $inLoginId;
  }

  /**
   * パスワード設定
   * 
   * @param $inPassword ログインID
   * @return none
   */
  public function setPassword( $inPassword ){
    $this->password = $inPassword;
  }
  
  /**
   * 接続先HOST設定
   * 
   * @param $inUrl URL
   * @return none 
   */
  public function setHost( $inUrl ){
    $this->url = $inUrl;
  }
  
  /**
   * コネクションID設定
   * 
   * @param $inUrl URL
   * @return connection ID 
   */
  abstract protected function setConnectId( $inUrl = null );
  
  /*************************
   * Getter 
   *************************/
  /**
   * ログインID取得
   * 
   * @param noen
   * @return ログインID
   */
  public function getLoginId(){
    return $this->login_id;
  }
  
  /**
   * パスワード取得
   * 
   * @param noen
   * @return パスワード
   */
  public function getPassword(){
    return $this->password;
  }

  /**
   * 接続先HOST取得
   * 
   * @param noen
   * @return 接続先URL
   */
  public function getHost(){
    return $this->url;
  }

  /**
   * 接続先ID取得
   * 
   * @param noen
   * @return 接続先ID
   */
  public function getConnectId(){
    return self::$conn_id;
  }
}
?>
