<?php
error_reporting(E_ALL);
date_default_timezone_set('Asia/Seoul');

define ("_DOCUMENT_ROOT_DIR", "/whois");
define ("_TEMPLATE_ROOT", _DOCUMENT_ROOT_DIR ."/html");
define ("_HOME_URL", "http://".$_SERVER['HTTP_HOST']."");
define ("_LOGIN_URL", $_SERVER['HTTP_HOST']);

define ("_APPL_VARIABLE", "tpf");
define ("_APPL_DIRECTORY_DIVISION", "/");
define ("_APPL_EXEC_EXTENSION", "php");
define ("_APPL_TEMPLATE_EXTENSION", "html");

/*
// PG사 셋팅
define ("_PG", "kcp");				// PG사
define ("_PAY_DUE_DAY_", 5);		// 무통장 입금 만료기간(today+_PAY_DUE_DAY_);
// define ("_PG_URL", "paygw.kcp.co.kr"); // PG URL (real)
define ("_PG_URL", "testpaygw.kcp.co.kr"); // PG URL (test)
// define ("_PG_WSDL", "real_KCPPaymentService.wsdl"); // 스마트폰 SOAP 통신 설정 (real)
define ("_PG_WSDL", "KCPPaymentService.wsdl"); // 스마트폰 SOAP 통신 설정 (test)
define ("_SHOP_ID", "T0000");   	// SHOP ID
define ("_PG_SITE_KEY", "3grptw1.zW0GSo4PQdaGvsF__"); // PG URL (test)
// buy_process.php 46 line ! 빼기
*/

define ("_LIBRARY_DIR", _DOCUMENT_ROOT_DIR."/lib");
define ("_CLASS_DIR", _LIBRARY_DIR."/class");
define ("_FUNC_DIR"	, _LIBRARY_DIR."/function");
define ("_CONFIG_DIR", _LIBRARY_DIR."/config");
define ("_USER_DIR", _DOCUMENT_ROOT_DIR."/user");
define ("_USER_URL", _HOME_URL."/user");
define ("_CATEGORY_LENGTH", 2);		// category length 2자리

// 사용자 정의
define ("_DISPLAY_DEBUG", true);    // error 발생시 표출 여부
// define ("_DB_ERROR_NOTI", "porchingman@naver.com");     // db error 발생시 수신 메일주소
define ("_HASH_SALT", "mart app");  // hash salt key
define ("_DISPLAY_DATA_COUNT", 8); // 한페이지 표출되는 data 개수
define ("_DISPLAY_DATA_COUNT_MOBILE", 10); // 한페이지 표출되는 data 개수
define ("_DISPLAY_PAGE_COUNT", 5);  // 표출되는 page 개수

// API 응답 코드 정의
define ("_API_SUCCESS", "00");      // 성공
define ("_API_CERTIFY_FAIL", "11"); // 인증 실패
define ("_API_FAIL", "99");         // 실패
define ("_API_URL", _HOME_URL."/api/process.php");  // 서버단 API END_POINT

define ("_SHOP_NAME","framework");
define ("_SITE_NAME","coolsoft");
define ("_DB_NAME",	"coolsoft");
define ("_DB_USER",	"root");
define ("_DB_PASSWORD",	"Kk596589!");
define ("_DB_IP",	"127.0.0.1");
define ("_CUSTOMER_TEL", "16614259");   // 고객센터 전화번호

// include _CLASS_DIR."/template/class.BearTemplate.php";
include _CLASS_DIR."/class.Page.php";
include _CLASS_DIR."/class.DBMysql.php";

include _FUNC_DIR."/func.common.php";	// 공통 함수
include _FUNC_DIR."/func.user.php";		// 사용자 정의
?>