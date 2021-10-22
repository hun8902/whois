<?php
class ThisPage extends Page {
    function initialize() {
		$this->objClass = $this->loadClass("BizProduct");
	}

	function checkParam() {
	}

	function makeJavaScript() {
        $category = $this->reqData['category'];
        // 호출 함수 만들기
        $scriptCategory = '';
        $index = 1;
        for($i=1; $i<=strlen($category)/2; $i++) {
            $scriptCategory .= 'getCategory('.$index.',\''.substr($category,0,($index-1)*2).'\','.substr($category,0,$index*2).');';
            $index++;
        }
        $scriptCategory .= 'getCategory('.$index.','.$category.',\'\');';

        $this->addScript("
        var category = '".$category."';
        function getCategory(depth, category_code, category_code_default) {
            $('#category'+depth).empty();
            $.ajax({
                url:'"._API_URL."',
                type:'post',
                dataType:'json',
                data:{
                    method : 'BizProduct.listCategory',
                    category_code : category_code
                },
                success:function(data, textStatus, jqXHR) {
                    var json_data = data.data;
                    console.log(json_data);
                    $('#category'+depth).append('<option value=\'\'>'+depth+'차</option>');
                    if (json_data != null) {
                        var tag_name = 'category'+depth;
                        $.each(json_data, function(depth, value) {
                            $('#'+tag_name).append('<option value=\''+value['category_code']+'\'>'+value['title']+'</option>');
                        });
                        if (category_code_default != undefined) {
                            $('#'+tag_name).val(category_code_default);
                        }
                    }
                },
                error:function(jqXHR, textStatus, errorThrown){
                    console.log(textStatus);
                    // $('#content').val(errorThrown);
                }
            });
        }
        $scriptCategory
        // getCategory(1,'',11);
        // getCategory(2,11,1110);
        // getCategory(3,1110,'');
        ");
	}

	function process() {
	}

	function setDisplay() {
        // return $this->arrData;
	}
}
?>