<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MyMailer
 *
 * @author soseki
 */
class MyMailer {
 private $mailto = null;
 private $subject = null;
 private $body = null;
 private $mailfrom = null;
 private $fromname = null;
 private $encording = null;
 
 /**
  * @param $inSubject: 件名
  *        $inBody    : 本文
  *        $inMailfrom: 送信者アドレス
  *        $inFromname: 送信者名
  * 
  * @return none
  */
 
 public function __construct( $inSubject, $inBody, $inMailfrom, $inFromname) {
     $this->subject = $inSubject;
     $this->body = $inBody;
     $this->mailfrom = $inMailfrom;
     $this->fromname = $inFromname;
     $this->encording = "UTF-8";  // set default encoding
 }
 
 /**
  * @param $inMailto:   送信先アドレス 
  *         $inSubject:  件名
  *         $inBody    : 本文
  *         $inMailfrom: 送信者アドレス
  *         $inFromname: 送信者名
  * 
  * @return bool 成功: true, 失敗: false
  */
 public function send($inMailto ,$inSubject = null, $inBody = null, $inMailfrom = null, $inFromname = null ){
   if( $inSubject !== null ){
     $this->subject = $inSubject;
   }
   
   if( $inBody !== null ){
     $this->body = $inBody;
   }
   
   if( $inMailfrom !== null ){
     $this->mailfrom = $inMailfrom;
   }
   
   if( $inFromname !== null ){
     $this->fromname = $inFromname;
   }
    mb_language('uni');
    mb_internal_encoding($this->encording);
    $mailfrom=mb_encode_mimeheader($this->mailfrom);
    $mailfrom = "From: " .
    "".mb_encode_mimeheader (mb_convert_encoding($this->fromname) ) ."" .
    "<".$mailfrom."> \n";
    return mb_send_mail($inMailto, $this->subject,$this->body, $this->mailfrom); 
 }
 
 public function setEncording( $inEncording )
 {
   $this->encording = $inEncording;
 }
}

?>
