<?php
class ThisPage extends Page {
    function initialize() {
        $this->objClass = $this->loadClass("Board");
	}

	function checkParam() {
	}

	function makeJavaScript() {
	}

	function process() {
        $this->arrData['board_info'] = $this->objClass->info($this->reqData);
        // $this->arrData['data'] = $this->objClass->view($this->reqData);
	}

	function setDisplay() {
        return $this->arrData;
	}
}
?>