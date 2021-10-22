<?php
class ThisPage extends Page {
    function initialize() {
        $this->objClass = $this->loadClass("Board");
        $arrBoard = $this->objClass->info($this->reqData);

        // 레이아웃 셋팅
        $this->setFile($arrBoard['type']);

        // 네비메뉴 셋팅
        if ($arrBoard['category']) {
            $this->arrData['current_url'] = $this->getCurrentUrl('category');
            $this->arrData['arrCategory'] = preg_split('/,/',$arrBoard['category']);
        }
        else {
            $this->arrData['display_navi'] = ' hidden';
        }

        $this->arrData['arrSearch'] = array(
			"b.title"	=> "제목",
			"b.name"	=> "작성자"
		);
		if (!empty($this->reqData['keyword'])) {    // 검색키 있을때
            $this->arrData['field'] = $this->reqData['field'];
            $this->arrData['keyword'] = $this->reqData['keyword'];
        }
    }

    function checkParam() {
    }

    function makeJavaScript() {
        $this->addScript('
        $(function(){
            // 답변 글 보이기
            $(".qna-board-list").find("dt").click(function(e){
            e.preventDefault();
                $(".qna-board-list").find("dt").not(this).removeClass("on").next().slideUp(500);
                $(this).addClass("on").next().slideToggle(500,function(){
                    if($(this).css("display") == "none" ){
                        $(this).prev().removeClass("on");
                    }
                });
            });
        });');
    }

    function process() {
        $arrReturn = $this->objClass->lists($this->reqData);
        $this->arrData['board_info'] = $this->objClass->info($this->reqData);
        $this->arrData['data'] = $this->displayDataList($arrReturn['list_query'],'y');
        if (!empty($this->arrData['data']['list'])) {
            foreach($this->arrData['data']['list'] as $key => $val) {
                $reply_icon = '';
                if($val['length_depth'] > 1)	{
                    for($j=2; $j<$val['length_depth']; $j++) { $reply_icon .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';}
                    $reply_icon .= '<img src="img/icon_re.gif" alt="답글" class="board-list-re-icon" />&nbsp;';
                }
                $this->arrData['data']['list'][$key]['title'] = $reply_icon.$this->arrData['data']['list'][$key]['title'];
            }
        }
        $this->arrData['board_code'] = $this->reqData['board_code'];
		if(!empty($this->reqData['category'])) $this->arrData['category'] = $this->reqData['category'];
    }

    function setDisplay() {
        return $this->arrData;
    }
}
?>