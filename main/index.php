<?php
class ThisPage extends Page {
    private $arrData;

	function initialize() {
        $this->arrData = array(
            'title' => '테스트 페이지'
        );
	}

	function checkParam() {
	}

	function makeJavaScript() {
        $this->addScript("
        function register() {
            if(form_register.name.value == undefined) { alert('이름이 입력되지 않았습니다.'); return false;}
            form_register.submit();
        }");
	}

	function process() {
        $arrBoard = $this->objDBH->getRows("select * from board_data where board_code=1 order by code desc");
        $this->arrData = array_merge($arrBoard, $this->arrData);
	}

	function setDisplay() {
        return $this->arrData;
	}
}
?>