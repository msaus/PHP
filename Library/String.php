<?php

/**
 * 文字列置換ライブラリー
 * 
 * Created by Masao Soseki
 */
class String
{
  
  /**
   * 文字列置換
   * 
   * @apram $inSearch:     検索文字列
   *                       Array又は文字列
   *        $inReplaceStr: 置換後文字列
   *        $inSubject:    置換対象文字列
   *                       Array
   * 
   * @return 置換された文字列
   * 
   * Usage
   * strReplace::replace("masao", "", array("sosekiiiiii","masaoooooooo"))
   * strReplace::replace(array("masao" , "soseki"), "", array("sosekiiiiii","masaoooooooo"))
   * strReplace::replace(array( "masao","soseki"), "", array("sosekiiiiii","masaoooooooo"))
   * 
   */  
  public static function replace( $inSearch, $inReplaceStr , $inSubject)
  { 
    
    if( !is_array( $inSubject ) )
    /// 置換文字列が複数の場合以下実行
    {
      if( !is_array($inSearch) ){    
          return str_replace($inSearch, $inReplaceStr, $inSubject);
      }else{
        foreach( $inSearch as $str )
        {
          $inSubject = str_replace($str, $inReplaceStr, $inSubject);
        }
        return $inSubject;
      }
    }
    
    $i = 0;
    $tmp = array();
    if( !is_array( $inSearch ) )
    // 置換文字列が1つの場合以下実行
    {
        foreach( $inSubject as $str )
        {
          $tmp[$i] = str_replace($inSearch, $inReplaceStr, $str);
          $i++;
        }
    }
    else
    // 置換文字列が複数の場合以下実行
    {
      foreach( $inSearch as $serch )
      {
        foreach( $inSubject as $str )
        {
          $tmp[$i] = str_replace($serch, $inReplaceStr, $str);
          $i++;
        }
        $i = 0;
        $inSubject = $tmp;
      }
    }
    return $tmp;
  }
  
  /**
   *  文字列先頭又は末尾又は両方の削除
   * 
   * @param $inStr 削除対象文字列
   *         $inWhere 削除対象指定
   *                    first : 先頭
   *                    last  : 末尾
   * @return string
   */
  public static function chop( $inStr = null , array $inWhere ){
    if( $inStr === null ){
      return "";
    }
    if( in_array("first", $inWhere ) )
    {
       $inStr = substr($inStr, 1 );
    }
    
    if( in_array("last", $inWhere ) )
    {
      $inStr = substr($inStr, 0, -1); 
    }
    return $inStr;
  }
  
  /**
   * 「*」でマスクされたアドレスの取得
   * @Params: $inAddress　emailアドレス
   * @Return: masked email address
   */
  public static function maskAddress( $inAddress )
  {
    if( !empty( $inAddress ) ){
      $tmp = explode("@",$inAddresss );
      return substr( $tmp[0], 0,1) . "*****" . substr( $tmp[0], strlen( $tmp[0] ) - 1,1) . "@" . $tmp[1];
    }
  }
  
  /**
   * マスクされた電話番号の取得
   * @Params:$inPhoneNo  電話番号
   * @Return: masked 電話番号
   */
  public static function maskPhoneNumber( $inPhoneNo)
  {
    if( !empty( $inPhoneNo) ){
      return substr( $inPhoneNo,0,3 ) . "******" . substr( $inPhoneNo,9,11 );
    }
  }

  /**
   * ファイルの文字コード変換
   * nkfコマンドは100%ではない為要注意
   *
   * @param : $inFilename: ファイル
   *          $inCode: 文字コード
   *                   utf8(default)
   *                   utf16
   *                   jis
   *                   euc
   * @return none
   */
  
  public static function convertCharCode($inFilename, $inCode = "utf8" ){
    switch($inCode){
      case "utf8":
        `nkf -w --overwrite $inFilename`;
        return;
      
      case "utf16":
        `nkf -w16 --overwrite $inFilename`;
        return;

      case "jis":
        `nkf -j --overwrite $inFilename`;
        return;

      case "euc":
        `nkf -e --overwrite $inFilename`;
        return;
      
    }
  }
  
  /**                                                                                                                                                                                       
   * Replaces duplicate break new lines and trims strings                                                                                                                                   
   * @param string                                                                                                                                                                          
   * @return                                                                                                                                                                                
   */                                                                                                                                                                                       
  public static function textTrimer(string $string)                                                                                                                                         
  {                                                                                                                                                                                         
    $string = preg_replace("/\r\n|\r|\n/", "\n", $string);                                                                              $string = array_filter(explode("\n",$string),                                                                                                                                           
      function($val)                                                                                                                
      {                                                                                                                             
        $val = trim($val);// Delete whilespaces before and after the character                                                      
        if(strlen($val) <= 1 || empty($val) || $val == false || $val == '' || is_null($val) || $val == ' ' || $val == '　')         
        {                                                                                                                           
          return false;                                                                                                             
        }                                                                                                                                   return true;                                                                                                                      }                                                                                                                             
    );                                                                                                                              
    $ret = '';                                                                                                                      
    foreach($string as $s)                                                                                                          
    {                                                                                                                               
      $ret .= trim($s)."\n";                                                                                                        
    }                                                                                                                               
    return trim($ret);                                                                                                              
  }
}
