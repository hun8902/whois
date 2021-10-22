<?php
/**
 * Stat 통계
 *
 * @author Lim sang jun <porchingman@naver.com>
 * @date 2015-10-18
 */
class Stat {
	var $arrMaster;
	var $objDBH;
	var $arrQuery = array();

	function __construct($obj) {
		$this->objDBH = $obj;
	}

}
?>