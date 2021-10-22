<?php
/**
 * Account Board
 *
 * @author Lim sang jun <porchingman@naver.com>
 * @date 2015-10-18
 */
include_once _CLASS_DIR."/class.UtilFile.php";
class Board extends UtilFile {
	private $objDBH;
	private $table = 'board_data';
    private $strFileName = array();

	function __construct($obj) {
		$this->objDBH = $obj;
	}

    /************************* 게시판 *************************/
    // 게시판 정보 가져오기
    function info($reqData) {
        checkParam($reqData['board_code'], "board_code");

        $arrReturn = $this->objDBH->getRow("select * from board where code='".$reqData['board_code']."'");
        return $arrReturn;
    }

    // 게시판 리스트 가져오기
    function listBoard($reqData) {
        $arrReturn = $this->objDBH->getRows("select * from board order by code");
        return $arrReturn;
    }

    // 게시판 등록된 글수 가져오기
    function getCount() {
        $arrReturn = $this->objDBH->getRows("select board_code,count(*) as count from board_data group by board_code");
        if (!empty($arrReturn['list'])) {
            foreach($arrReturn['list'] as $key => $val) {
                $arrTmp[$val['board_code']] = $val['count'];
            }
        }
        return $arrTmp;
    }

    // 등록
	function insertBoard($reqData) {
        $arrParam = array (
            'type'          => $reqData['type'],
            'title'         => $reqData['title'],
            'category'      => $reqData['category'],
            'show_list'     => $reqData['show_list'],
            'show_memo'     => $reqData['show_memo'],
            'show_secret'   => $reqData['show_secret'],
            'limit_title'   => @$reqData['limit_title'],
            'auth_list'     => $reqData['auth_list'],
            'auth_view'     => $reqData['auth_view'],
            'auth_write'    => $reqData['auth_write'],
            'auth_reply'    => $reqData['auth_reply'],
            'auth_update'   => $reqData['auth_update'],
            'auth_memo'     => $reqData['auth_memo'],
            'auth_delete'   => $reqData['auth_delete'],
            'auth_notice'   => $reqData['auth_notice'],
            'is_mass'       => $reqData['is_mass'],
            'is_order'      => $reqData['is_order']
        );
        $this->objDBH->insert("board", $arrParam);
        $code = $this->objDBH->getLastId();

        return $code;
	}

	// 수정
	function updateBoard($reqData) {
        $arrParam = array (
            'type'          => $reqData['type'],
            'title'         => $reqData['title'],
            'category'      => $reqData['category'],
            'show_list'     => $reqData['show_list'],
            'show_memo'     => $reqData['show_memo'],
            'show_secret'   => $reqData['show_secret'],
            'limit_title'   => @$reqData['limit_title'],
            'auth_list'     => $reqData['auth_list'],
            'auth_view'     => $reqData['auth_view'],
            'auth_write'    => $reqData['auth_write'],
            'auth_reply'    => $reqData['auth_reply'],
            'auth_update'   => $reqData['auth_update'],
            'auth_memo'     => $reqData['auth_memo'],
            'auth_delete'   => $reqData['auth_delete'],
            'auth_notice'   => $reqData['auth_notice'],
            'is_mass'       => $reqData['is_mass'],
            'is_order'      => $reqData['is_order']
        );
        $arrWhere = array(
            'code' => $reqData['board_code']
        );
        $this->objDBH->update("board", $arrParam, $arrWhere);
	}

	// 삭제
	function deleteBoard($reqData) {
        $query = "delete from board where code in (".$reqData['code'].")";
		$this->objDBH->query($query);
	}

    /************************* 게시물 *************************/
    // 정보 가져오기
    function view($reqData) {
        checkParam($reqData['board_code'], "board_code");
        checkParam($reqData['code'], "code");

        // hitting + 1
        $this->objDBH->query("update ".$this->table." set hitting=hitting+1 where code='".$reqData['code']."' and board_code='".$reqData['board_code']."'");

        $arrReturn = $this->objDBH->getRow("select * from ".$this->table." where code='".$reqData['code']."' and board_code='".$reqData['board_code']."'");

        // 파일 표출
        $arrFile = $this->objDBH->getRows("select code,file_path,file_name,orig_name,file_size,file_width,file_height,file_ext,concat('"._USER_URL."',file_path,'/',file_name,'?dummy=',".getDummy().") as url,date_format(reg_date,'%Y-%m-%d %H:%i') as reg_date from attachment where table_name='board_data' and table_code='".$reqData['code']."' order by code");
        if (!empty($arrFile['list'])) {
            $arrReturn['files'] = $arrFile['list'];
        }
        else {
            $arrReturn['files'] = null;
        }
        return $arrReturn;
    }

    // 이전/다음글 가져오기
    function getPrevNext($reqData) {
        checkParam($reqData['board_code'], "board_code");
        checkParam($reqData['code'], "code");

        $arrData = $this->objDBH->getRow("select num from ".$this->table." where code='".$reqData['code']."'");

        // 이전글
        $arrPrev = $this->objDBH->getRow("select code,board_code,title from ".$this->table." where board_code='".$reqData['board_code']."' and num < '".$arrData['num']."' and depth='A' order by num desc limit 1");
        if(file_exists(_USER_DIR."/board/".$arrPrev['code'])) {
            $arrPrev['file'] = $arrPrev['code'];
        }

        // 다음글
        $arrNext = $this->objDBH->getRow("select code,board_code,title from ".$this->table." where board_code='".$reqData['board_code']."' and num > '".$arrData['num']."' and depth='A' order by num limit 1");
        if(file_exists(_USER_DIR."/board/".$arrNext['code'])) {
            $arrNext['file'] = $arrNext['code'];
        }
        $arrReturn['prev'] = $arrPrev;
        $arrReturn['next'] = $arrNext;

        return $arrReturn;
    }

    // 리스트 가져오기 : list_query 값으로 넘기면 parent 단에서 displayDataList() 실행
    function lists($reqData) {
        checkParam($reqData['board_code'], "board_code");

        $add_where = !empty($reqData['category']) ? " and b.category = '".$reqData['category']."'" : "";
        $add_where .= !empty($reqData['keyword']) ? " and ".$reqData['field']." like '%".$reqData['keyword']."%'" : "";

        $arrReturn['list_query'] = "select b.code,b.num,length(b.depth) as length_depth,b.title,b.name,b.content,b.category,b.hitting,to_days(current_date)-to_days(b.reg_date) as diff_date,date_format(b.reg_date,'%Y-%m-%d %H:%i') as reg_date,date_format(b.reg_date,'%Y-%m-%d') as reg_date_short,concat('"._USER_URL."',a.file_path,'/',a.file_name,'?dummy=',".getDummy().") as image_url from ".$this->table." b left join attachment a on (a.table_name='board_data' and b.code=a.table_code) where b.board_code='".$reqData['board_code']."'".$add_where." group by b.code  order by b.num, b.depth asc";

		return $arrReturn;

    }

    function getMinNum($board_code) {
        $arrBoard = $this->objDBH->getRow("select min(num) as min from ".$this->table." where board_code='".$board_code."'");
		if (!$arrBoard['min']) $arrBoard['min'] = -1;
		else $arrBoard['min']--;
		return $arrBoard['min'];
	}

    // 파일체크
	function _checkFile() {
        $this->setFiles();
        if (!empty($_FILES['file']['name'])) {
            foreach($_FILES['file']['name'] as $key => $val) {
                if (!empty($val)) {
                    $arrResultFile = $this->checkFileBulk("File",$key);
                    if($arrResultFile['status'] == "FAIL") {
                        putJSMessage("[".$arrResultFile['message']."]","back");
                        exit;
                    }
                    else {
                        $this->strFileName[$key] = strtolower($_FILES['file']['name'][$key]);
                    }
                }
            }
        }
	}

	// 파일첨부
	function _uploadFile($member_code, $file_code) {
        if (count($this->strFileName) > 0) {
            foreach($this->strFileName as $key => $val) {
                $arrFileInfo = $this->uploadFileBulk($key);

                // attachment 정보 저장 하기
                $arrParam = array (
                    'member_code' => $member_code,
                    'table_name' => 'board_data',
                    'table_code' => $file_code,
                    'file_path' => $arrFileInfo['file_path'],
                    'file_name' => $arrFileInfo['file_name'],
                    'orig_name' => $arrFileInfo['orig_name'],
                    'file_size' => $arrFileInfo['file_size'],
                    'file_width' => $arrFileInfo['file_width'],
                    'file_height' => $arrFileInfo['file_height'],
                    'file_ext' => $arrFileInfo['file_ext'],
                    'reg_date' => 'now()'
                );
                $this->objDBH->insert("attachment", $arrParam);

                usleep(1);
			}
		}
	}

    // 대용량 파일 (파일첨부)
	function _uploadFilePL($member_code, $file_code) {
        if (count(@$_COOKIE['attachment']) > 0) {
            foreach($_COOKIE['attachment'] as $key => $val) {
                // attachment 정보 저장 하기
                $arrParam = array (
                    'member_code' => $member_code,
                    'table_name' => 'board_data',
                    'table_code' => $file_code,
                    'file_path' => $val['file_path'],
                    'file_name' => $val['file_name'],
                    'orig_name' => $val['orig_name'],
                    'file_size' => $val['file_size'],
                    'file_ext' => $val['file_ext'],
                    'reg_date' => 'now()'
                );
                $this->objDBH->insert("attachment", $arrParam);
            }
            $this->resetAttachment();
        }
	}

    // 대용량 파일 (첨부 파일 초기화)
    function resetAttachment() {
        if (count(@$_COOKIE['attachment']) > 0) {
            foreach($_COOKIE['attachment'] as $key => $val) {
                @setcookie("attachment[".$key."][file_path]","", 0,"/",_LOGIN_URL);
                @setcookie("attachment[".$key."][file_name]","", 0,"/",_LOGIN_URL);
                @setcookie("attachment[".$key."][orig_name]","", 0,"/",_LOGIN_URL);
                @setcookie("attachment[".$key."][file_size]","", 0,"/",_LOGIN_URL);
                @setcookie("attachment[".$key."][file_ext]","", 0,"/",_LOGIN_URL);
            }
        }
    }

	// 등록
	function insert($reqData) {
        $this->_checkFile();

        if ($reqData['mode'] == "reply") {  // 답변
            $arrBoardData = $this->objDBH->getRow("select num,depth from ".$this->table." where board_code='".$reqData['board_code']."' and code='".$reqData['board_data_code']."'");
            $num = $arrBoardData['num'];

            $arrDepth = $this->objDBH->getRow("select depth,right(depth,1) as right_depth from ".$this->table." where board_code='".$reqData['board_code']."' and num='".$num."' and length(depth) = length('".$arrBoardData['depth']."')+1 and locate('".$arrBoardData['depth']."',depth) = 1 order by depth desc limit 1");
            if($arrDepth['depth']) {	// 이미 해당 답글이 있을때
				$depth_head = substr($arrDepth['depth'],0,-1);
				$depth_foot = ++$arrDepth['right_depth'];
				$depth = $depth_head.$depth_foot;
			}
			else {						// 처음 답글일때
				$depth = $arrBoardData['depth']."A";
			}
		}
		else {                              // 글쓰기
            $num = $this->getMinNum($reqData['board_code']);
			$depth = "A";
		}

        $arrParam = array (
            'board_code'    => $reqData['board_code'],
            'num'           => $num,
            'depth'         => $depth,
            'member_code'   => $reqData['member_code'],
            'name'          => $reqData['name'],
            'title'         => $reqData['title'],
            'category'      => @$reqData['category'],
            'content'       => $reqData['content'],
            'ip'            => $_SERVER['REMOTE_ADDR'],
            'reg_date'      => "now()"
        );
        $this->objDBH->insert($this->table, $arrParam);
        $code = $this->objDBH->getLastId();

        $this->_uploadFile($reqData['member_code'], $code);
        $this->_uploadFilePL($reqData['member_code'], $code); // 대용량 파일 업로드
		return $code;
	}

	// 수정
	function update($reqData) {
        // 파일 삭제모듈
        if (!empty($reqData['delete_file'])) {
            $query = "delete from attachment where code in (".$reqData['delete_file'].") and member_code='".$reqData['member_code']."'";
            $this->objDBH->query($query);
        }

        $this->_checkFile();
        $this->_uploadFile($reqData['member_code'], $reqData['board_data_code']);
        $this->_uploadFilePL($reqData['member_code'], $reqData['board_data_code']);

        $arrParam = array (
            'name'      => $reqData['name'],
            'title'     => $reqData['title'],
            'category'  => @$reqData['category'],
            'content'   => $reqData['content']
        );
        $arrWhere = array(
            'code' => $reqData['board_data_code'],
            'board_code' => $reqData['board_code']
        );
        $this->objDBH->update($this->table, $arrParam, $arrWhere);
	}

	// 삭제
	function delete($reqData) {
        $query = "delete from ".$this->table." where code in (".$reqData['code'].") and board_code='".$reqData['board_code']."'";
		$this->objDBH->query($query);
	}
}
?>