<?php
/**
 * FPTクラス
 * 
 * Created by Masao Soseki
 */

require_once 'connectionObj.php';
require_once 'String.php';

class FTP extends connectionObj implements connect{

  private $login_result = null;
  
  public $command = null;
  
  // エラーフラグ
  const LOGIN_FAIL = 100;
  const UPLOAD_FAIL = 101;
  const COMMAND_FAIL = 102;
  const NOT_LOGIN_FAIL = 103;
  const FILE_EXIST = 104;
 
  // エラーメッセージ
  const ERROR_MESSAGE_LOGIN_FAIL ="ログインに失敗致しました。";
  const ERROR_MESSAGE_UPLOAD_FAIL ="ファイルアップロードに失敗致しました。";
  const ERROR_MESSAGE_COMMAND_FAIL = "コマンド実行に失敗致しました。";
  const ERROR_MESSAGE_NOT_LOGIN_FAIL = "未ログイン又はコネクションの切断されました。";
  const ERROR_MESSAGE_FILE_EXIST = "ファイルは既に存在します。";
  
  // 成功フラグ
  const LOGIN_SUCCESS = 200;
  const FILE_UPLOAD_SUCCESS = 201;
  
  // 成功メッセージ
  const SUCCESS_MESSAGE_LOGIN = "ログインに成功しました。";
  const SUCCESS_MESSAGE_FILE_UPLOAD = "ファイルのアップロードに成功致しました。";
  
  // FTPメッセージ
  private static $ftp_messages = array(self::LOGIN_FAIL => self::ERROR_MESSAGE_NOT_LOGIN_FAIL,
                                self::COMMAND_FAIL => self::ERROR_MESSAGE_COMMAND_FAIL,
                                self::UPLOAD_FAIL => self::ERROR_MESSAGE_UPLOAD_FAIL,
                                self::LOGIN_FAIL => self::ERROR_MESSAGE_LOGIN_FAIL,
                                self::FILE_EXIST => self::ERROR_MESSAGE_FILE_EXIST,
                                self::LOGIN_SUCCESS => self::SUCCESS_MESSAGE_LOGIN,
                                self::FILE_UPLOAD_SUCCESS => self::SUCCESS_MESSAGE_FILE_UPLOAD,
                                );
  
  /**
   * コンストラクター
   * 
   * @param $inUrl      接続先FTP URL
   *         $inLoginId  ログインID
   *         $inPassword ログインパスワード
   *         $inCommand  Linuxコマンド(任意)
   * @return none
   */
  public function __construct( $inUrl , $inLoginId, $inPassword, $inCommand = null ) {
    $this->setHost($inUrl);
    $this->setLoginId($inLoginId);
    $this->setPassword($inPassword);
    
    // FTPへ接続
    if( $this->getConnectId() === null ){
      $this->setConnectId($inUrl);
    }
    
    // アップロード時にコマンド実行フラグ
    if( $inCommand !== null ){
      $this->command = $inCommand;
    }
  }
  
  public function __destruct() {
    ftp_close( $this->getConnectId() );
  }
  
  /**
   * ログインtoFTPサーバ
   * 
   * @param  $inLoginId  ログインID
   *          $inPassword パスワード
   * @return Bool
   */
  public function login() {
    $this->login_result = ftp_login($this->getConnectId(), $this->getLoginId(), $this->getPassword()); 
    
    // 接続できたか確認する
    if ( $this->conn_id || !$this->login_result ){ 
      //echo "FTP connection has failed!";
      return false;
      //throw new Exception ("Attempted to connect to $this->url for user $inLoginId");
    } else {
      //echo "Connected to " . $this->getHost() .", for user " . $this->getLoginId();
      return true;
    }
  }
  
  /**
   * ファイルアップロード
   * 
   * @param  $inRemoteFilePath   ファイルの保存先のパスとファイル名
   *                              例) /public_html/sampelcsv.csv
   *          $inLocalFilePath    ローカルのファイルパスとファイル名
   *                              例) /home/soseki/sampelcsv.csv
   *          $inForceUploadFlag  強制アップロードフラグ
   *                              デフォルトは強制アップロード
   * 　　　　　　　　　　　　　　　 アップロード先ディレクトリーに同じファイルが存在しても
   *                              強制アップロードするか否か
   *          $inCmd              Linuxコマンド
   *                              FTPアップロード後に任意のコマンドを実行
   * 
   * @return true  ファイルアップロード及びコマンド実行成功
   *          101   ファイルのアップロードに失敗
   *          102   ファイルアップロードにコマンドの実行失敗
   *          103   未ログイン又はコネクションの切断
   *          104    ファイルは既に存在している
   */
  public function upload( $inRemoteFilePath, $inLocalFilePath, $inForceUploadFlag = true, $inCmd = null )
  {
    // ファイルが存在するかチェック
    if( $inForceUploadFlag === false )
    {
      $tmp = explode("/",$inRemoteFilePath);
      if( $this->isExistFile(str_replace($tmp[count($tmp)-1], "", $inRemoteFilePath), $tmp[count($tmp)-1] ) )
      {
        return 104;
      }
    }
    
    // アップロード
    if( $this->getConnectId() && $this->login_result )
    {
      // アップロードを開始する
      $ret = ftp_nb_put($this->getConnectId(),$inRemoteFilePath, $inLocalFilePath, FTP_BINARY);
      while ( $ret == FTP_MOREDATA ) {
        // 何かお好みの動作を
        echo ">>>>>>";
        // アップロードを継続する…
        $ret = ftp_nb_continue( $this->getConnectId() );
      }
      if ( $ret != FTP_FINISHED ) {
        return 101;
      }
      //$tmp = explode("/", $inLocalFilePath );
      ftp_chmod($this->getConnectId(),0644,$inRemoteFilePath);
      //echo "gzip " . $inRemoteFilePath . "<br>";
      if( $inCmd !== null ){
          return !$this->execCommand( $inCmd )  ? 102 : true;
      }
      return 201;
    }else{
      return 103;
    }
  }
  
  /**
   * FTPの接続先で任意コマンド実行
   * 
   * @param $inCmd Linuxコマンド(任意)
   * 
   * @return true コマンドの実行が成功(サーバコード200)
   *          102  コマンド実行エラー
   */
  public function execCommand( $inCmd = null ){
    if( $inCmd !== null ){
      $ret = ftp_exec($this->getConnectId(), $inCmd );
     }else if( $this->command  !== null ){
       $ret = ftp_exec($this->getConnectId(), $this->command );
     }else{
       $ret = false;
     }
     return $ret == false ? 102 :true;  
  }
  
  /**
   * ディレクトリーリスト一覧取得
   * 
   * @param $inDirPath: 一覧表示するディレクトリーパス
   *         $printFlag: 配列でディレクトリーリスト一覧表示フラグ
   * @return ディレクトリ内のファイル名の配列取得
   *          Error時は、false
   */
  public function getDirList( $inDirPath, $printFlag = false)
  {
    $tmp = ftp_nlist($this->getConnectId(), $inDirPath);
    if( $printFlag === true ){
      echo "<pre>";
      print_r($tmp);
      echo "</pre>"; 
    }
    return $tmp === false ? false : $tmp;
  }
  
  /**
   * 任意のディレクトリーに指定されたファイルが存在するか確認
   * 
   * @param $inDirPath: FPTのファイルpath
   *         $inFileName: ファイル名
   * @return True: ファイルが存在する
   *          Flse: ファイルが存在しない
   *          -1  :  ディレクトリー指定エラー
   */
  public function isExistFile( $inDirPath, $inFileName ){
    $tmp = $this->getDirList( $inDirPath );
    if( $tmp !== false ){
      $tmp = String::replace($inDirPath , "" , $tmp );
      foreach( $tmp as $dir ){
        if( $inFileName == $dir ){
          return true;
        }
      }
      return false;
    }else{
      return -1;
    }
  }
  
  /**
   * メッセージ取得
   * 
   * @param $inMessageId: メッセージID
   * @return メッセージ
   */
  public function getMessage( $inMessageId )
  {
    return self::$ftp_messages[$inMessageId];
  }
  
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
    parent::setLoginId($inLoginId);
  }
  
  /**
   * パスワード設定
   * 
   * @param $inPassword ログインID
   * @return none
   */
  public function setPassword( $inPassword ){
    parent::setPassword($inPassword);
  }
  
  /**
   * 接続先HOST設定
   * 
   * @param $inUrl URL
   * @return none 
   */
  public function setHost( $inUrl ){
    parent::setHost($inUrl);
  }
  
  /**
   * コネクションID設定
   * 
   * @param $inUrl URL
   * @return connection ID 
   */
  public function setConnectId( $inUrl = null ){
    if( $inUrl !== null ){
      $this->url = $inUrl;
      self::$conn_id = ftp_connect( $inUrl );
    }else{
      self::$conn_id = ftp_connect( $this->url );
    }
    return self::$conn_id;
  }
  
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
    return parent::getLoginId();
  }
  
  /**
   * パスワード取得
   * 
   * @param noen
   * @return パスワード
   */
  public function getPassword(){
    return parent::getPassword();
  }

  /**
   * 接続先HOST取得
   * 
   * @param noen
   * @return 接続先URL
   */
  public function getHost(){
    return parent::getHost();
  }

  /**
   * 接続先ID取得
   * 
   * @param noen
   * @return 接続先ID
   */
  public function getConnectId(){
    return parent::getConnectId();
  }
}
?>
