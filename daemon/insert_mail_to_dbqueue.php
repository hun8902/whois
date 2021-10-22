#!/usr/local/php/bin/php -q

<?
include("/home/www/home.ezin.kr/source/lib/config/config.php");
include _CLASS_DIR."/member/class.WhoisHttp.php";

$page = new Page;
$http = new WhoisHttp;
$arrReceiveEmailList = array();

$cfg_file = _CONFIG_DIR."/cfg.Mailer.php";
if (file_exists($cfg_file)) $arrMailerInfo = parse_ini_file($cfg_file,true);

$result = $page->objDBH->query("select * from mail_history where status='R' and res_date <= now() order by code");
while($arrMailHistory = $page->objDBH->fetch($result)) {
	$arrReceiveEmailList = unserialize($arrMailHistory['receive_email']);

	foreach($arrReceiveEmailList as $key => $val) {
		$http = new WhoisHttp();
		$http->host = $arrMailerInfo['host'];
		$http->path = $arrMailerInfo['send_path'];
		$http->variable['send_name']	= $arrMailHistory['send_name'];
		$http->variable['send_email']	= $arrMailHistory['send_email'];
		$http->variable['title']		= $arrMailHistory['title'];
		$http->variable['content']		= $arrMailHistory['content'];
		$http->variable['id']			= $arrMailerInfo['id'];
		$http->variable['password']		= $arrMailerInfo['password'];
		$http->variable['reseller_code']= "";
		$http->variable['user_id']		= $arrMailHistory['member_id'];
		$http->variable['category']		= $arrMailHistory['category'];
		$http->variable['res_date']		= $arrMailHistory['res_date'];
		$http->variable['receive_email']= trim($val);
		$buffer = $http->getBody("post");
		$process_result = $http->parserResult($buffer);
	}
	$page->objDBH->query("update mail_history set status='D' where code=".$arrMailHistory['code']);
}
die("ok");
?>
