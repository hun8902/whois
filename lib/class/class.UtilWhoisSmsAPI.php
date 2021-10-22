<?php
// Whois SMS API Client 클래스

if (class_exists("UtilWhoisSmsAPI")) return;
class UtilWhoisSmsAPI {
	// Server End Point
	var $cfgHost;
	var $cfgPath;
	var $cfgPort;
	//
	var $cfgAction;
	var $params;
	var $cfgJsonType;
	//
	var $input;
	var $output;
	var $request;
	var $response;
	var $result;
	var $debug;
	var $error;
	// 인증정보
	var $reseller_id;
	var $sms_id;
	var $sms_pw;

	// ================================================================= 생성자
	function UtilWhoisSmsAPI($pAction='') {
		$this->_initConfig();
		$this->setAction($pAction);
	}


	function setAction($pAction) {
		$this->cfgAction = 'customer_'. $pAction;
		$this->initParam();
	}

	function _initConfig() {
		$cfg = parse_ini_file(_CONFIG_DIR.'/config.WhoisSmsAPI.php');
		$this->reseller_id = $cfg['reseller_id'];
		$this->sms_id = $cfg['sms_id'];
		$this->sms_pw = $cfg['sms_pw'];
		$this->cfgHost = $cfg['host_name'];
		$this->cfgPath = $cfg['path'];
		$this->cfgPort = $cfg['port'];
	}
	// ================================================================= Execute
	function execute($pAction='', $pParam=array()) {
		if ($pAction != '') $this->setAction($pAction);
		if (!empty($pParam) && is_array($pParam)) $this->addParam($pParam);

		$this->_initResult();
		$this->_initRequestParam();
		$this->execHttpSocket($this->_getHttpQuery());
		return $this->_parseResponse();
	}

	function _initResult() {
		$this->input = array();;
		$this->output = array();
		$this->debug = array();;
		$this->error = '';
		$this->request = '';
		$this->response = '';
	}

	function _initRequestParam() {
		$base = array('reseller_id'=> $this->reseller_id, 'sms_id'=>$this->sms_id, 'sms_pw'=>$this->sms_pw);
		$paramInput = array('action'=>$this->cfgAction, 'param'=>$this->params, 'base'=>$base);
		$this->input = $paramInput;
		$this->request = $this->encodeRequest($paramInput);
	}
	function _getHttpQuery() {
		$parameter = "\r\nparam=". urlencode(trim($this->request));
		$queries = array();
		$queries[] = "POST ".$this->cfgPath." HTTP/1.0";
		$queries[] = "Host: ".$this->cfgHost;
		$queries[] = "User-agent: PHP/HTTP_CLASS";
		$queries[] = "Content-type: application/x-www-form-urlencoded";
		$queries[] = "Content-length: ".strlen($parameter);
		$queries[] = $parameter;
		$queries[] = "";
		return implode("\r\n", $queries);
	}

	function execHttpSocket($pQuery) {
		$fp = fsockopen($this->cfgHost, $this->cfgPort, $errno, $errstr, 10);
		if (!$fp) {
			$this->error = $this->cfgHost .' server connect fail';
			return false;
		}
		fputs($fp, $pQuery);

		$_header = ""; // 헤더의 내용을 초기화 한다.
		while (trim($buffer = fgets($fp,1024)) != "") $_header .= $buffer;
		while (!feof($fp)) $this->response .= fgets($fp,1024);

		fclose($fp);
	}

	function _parseResponse() {
		// decode response
		$result = $this->decodeResponse($this->response);
		// return result
		if (is_object($result)) {
			$result = (array)$result;
		}
		if (!is_array($result)) {
			$this->error = "Could not connect to HTTP server.";
			return false;
		}
		if (!isset($result['result'])) {
			$this->error = "Not found result code";
			return false;
		}
		$this->output = $result;
		if ($result['result'] != 'success') {
			$this->error = $result['error'];
			$this->debug = $result['debug'];
			return false;
		}
		$this->debug = $result['debug'];
		unset($result['result']);
		unset($result['error']);
		unset($result['debug']);
		$this->result = $result;
		return true;
	}
	// ================================================================= Parameter
	function initParam() {
		$this->params = array();
	}
	function setParam($pParam) {
		if (!is_array($pParam)) $pParam = array();
		$this->params = $pParam;
	}
	function addParam($pKey, $pVal) {
		$this->params[$pKey] = $this->encodeURL($pVal);
	}
	// =================================================================
	function getError() {
		return $this->error;
	}
	function getDebug() {
		return $this->debug;
	}
	function getResult($key='') {
		if ($key) {
			if (isset($this->result[$key])) return $this->result[$key];
			return '';
		} else {
			return $this->result;
		}
	}
	function getParam() {
		return $this->params;
	}
	function getInput() {
		return $this->input;
	}
	function getOutput() {
		return $this->output;
	}
	function getRequest() {
		return $this->request;
	}
	function getResponse() {
		return $this->response;
	}
	// ================================================================= JSON encoding / decoding
	function checkJson() {
		if (!function_exists("json_encode")) {
			if (!class_exists("Services_JSON")) include_once 'class.Services_JSON.php';
			$this->cfgJsonType = "";
		} else {
			$this->cfgJsonType = "PECL";
		}
	}

	function encodeRequest($param) {
		$this->checkJson();
		if($this->cfgJsonType == "PECL") return json_encode($param);
		$objJson = new Services_JSON();
		$return = $objJson->encode($param);
		return $return;
	}

	function decodeResponse($param) {
		$return = "";
		$this->checkJson();
		if ($this->cfgJsonType == "PECL") {
			$return = json_decode($param);
		} else {
			$objJson = new Services_JSON();
			$return = $objJson->decode($param);
		}
		$return = $this->convertObject2Array($return);
		return $return;
	}

	function convertObject2Array($param) {
		if (is_object($param) || is_array($param)) {
			if (is_object($param)) $param = (array)$param;
			foreach ($param as $key => $val) {
				if (is_object($val) || is_array($val)) {
					$param[$key] = $this->convertObject2Array($val);
				} else {
					if (array_key_exists($key, $param)) $param[$key] = $val;
				}
			}
			return $param;
		} else {
			return $param;
		}
	}
	// ================================================================= character set conversion
	function euckr2utf8($param) {
		if (is_array($param)) {
			foreach ($param as $key => $val) $param[$key] = $this->euckr2utf8($val);
			return $param;
		} else return iconv("EUC-KR", "UTF-8", $param);
	}

	function utf82euckr($param) {
		if (is_array($param)) {
			foreach ($param as $key => $val) $param[$key] = $this->utf82euckr($val);
			return $param;
		} else return iconv("UTF-8", "EUC-KR", $param);
	}

	function encodeURL($param) {
		if (is_array($param)) {
			while (list($key, $val) = each($param)) {
				if(!is_numeric($val)) {
					$param[$key] = urlencode($val);
				}
			}
			return $param;
		} else {
			return urlencode($param);
		}
	}
}

?>
