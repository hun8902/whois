#!/usr/local/php/bin/php -q

<?
include("/home/www/home.ezin.kr/source/lib/config/config.php");
include _CLASS_DIR."/class.WhoisSMS.php";

$page = new Page;
$sms = new WhoisSMS();

$index = 0;
$result = $page->objDBH->query("select code,member_id,sender_hp,receive_hp,content from sms where status='READY' and res_date <= now() order by code");
while($arrData = $page->objDBH->fetch($result)) {
	$sender_hp = eregi_replace("-","",$arrData['sender_hp']);
	$sms->setSender($sender_hp);			// 발신자 번호
	$sms->setUserId($arrData['member_id']);	// 고객Id 지정

	if ($arrData['receive_hp']) {
		$receive_hp = $arrData['receive_hp'];
		$receive_hp = eregi_replace("-","",$receive_hp);
		if ($receive_hp and is_numeric($receive_hp)) {
			$sms->arrReceiver = array();	// 수신자 번호 초기화
			$sms->addReceiver($receive_hp);	// 수신자 번호 : 복수로 추가 가능
			$sms->setMessage($arrData['content']);	// 전달 메시지
			$send_result  = $sms->send();
			$send_message = $sms->getMessage();

			if ($send_result == true) {	// 발송 성공일때
				$status = "DONE";
			}
			else {						// 발송 실패
				$status = "FAIL";
			}
			// sms status 발송 완료로 변경
			$page->objDBH->query("update sms set status='".$status."' where code='".$arrData['code']."'");
			$index++;
		}
	}
}
die($index." 건 발송완료");
?>
