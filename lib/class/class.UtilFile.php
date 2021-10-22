<?php
/**
 * UtilFile Class
 *
 * @author Lim sang jun <porchingman@naver.com>
 * @date 2015-10-18
 */
class UtilFile {
	var $msg;
	var $arrFile;
	var $fileExt;
	// 파일 type별 확장자 정리
	var $arrImgType		= array("jpg","gif","jpeg","png");
	var $arrFlashType	= array("swf");
	var $arrMovieType	= array("asf","wmv","mpg","vod");
	var $arrMmusicType	= array("wav","mid","mp3");
	var $arrLogoType	= array("jpg","gif","png","swf");
	var $arrCsvType		= array("csv");
	var $arrFileType	= array("*");

	var $arrDeniedFileExt = array("php","php3","php4","html","htm","inc","pl","xml","asp","jsp","exe","com","vbs","js");
	var $arrDeniedMineType = array("text/plain","text/html","text/xml");

	function UtilFile() {
		$this->setFiles();
	}

	function setFiles() {
		$this->arrFile = $_FILES;
	}

	function error($msg) {
		$val['status']	= "FAIL";
		$val['message']	= $msg;
		return $val;
	}

	// 업로드 불가 확장자 체크
	function checkDeniedExt($file_name) {
		if (preg_match('/(php[0-9]*|html|htm|inc|pl|xml|asp|jsp|exe|com|vbs|js)(\.|$)/i', $file_name)) {
			@unlink($file['tmp_name']);
			return $this->error("업로드가 제한된 파일 확장자입니다.");
		}
	}

	function checkFile($file_type="Img",$up_file) {	// 파일 체크
		$arrFileName = explode( ".",$this->arrFile[$up_file]['name']);
		$this->fileExt = strtolower(array_pop($arrFileName));
		if($this->fileExt == "") {
			$this->error("파일이 첨부되지 않았습니다.");
		}
		else {
			// 파일 확장자 체크
			if ($file_type != "File") {
				$tmp = "arr".$file_type."Type";
				if(!in_array($this->fileExt,$this->$tmp)) {
					return $this->error("파일 확장자는 ".implode(", ",$this->$tmp)." 만 지원합니다.");
				}
			}

			// 첨부금지 파일 확장자 체크
			if(in_array($this->fileExt,$this->arrDeniedFileExt)) {
				@unlink($this->arrFile[$up_file]['tmp_name']);
				return $this->error("업로드가 제한된 파일 확장자입니다.");
			}

			// mine type 체크
			if(in_array($this->arrFile[$up_file]['type'],$this->arrDeniedMineType)) {
				return $this->error("지원하지 않는 mine type 입니다.");
			}
		}
	}

    function checkFileBulk($file_type="Img",$file_index) {	// 파일 체크
		$arrFileName = explode( ".",$this->arrFile['file']['name'][$file_index]);
		$file_ext = strtolower(array_pop($arrFileName));
		if($file_ext == "") {
			$this->error("파일이 첨부되지 않았습니다.");
		}
		else {
			// 파일 확장자 체크
			if ($file_type != "File") {
				$tmp = "arr".$file_type."Type";
				if(!in_array($file_ext,$this->$tmp)) {
					return $this->error("파일 확장자는 ".implode(", ",$this->$tmp)." 만 지원합니다.");
				}
			}

			// 첨부금지 파일 확장자 체크
			if(in_array($file_ext,$this->arrDeniedFileExt)) {
				@unlink($this->arrFile['file']['tmp_name'][$file_index]);
				return $this->error("업로드가 제한된 파일 확장자입니다.");
			}

			// mine type 체크
			if(in_array($this->arrFile['file']['type'][$file_index],$this->arrDeniedMineType)) {
				return $this->error("지원하지 않는 mine type 입니다.");
			}
		}
	}

	function uploadFile($up_file,$dn_file) {	// 파일 업로드
		if ($this->arrFile[$up_file]['name']) {
			move_uploaded_file($this->arrFile[$up_file]['tmp_name'],$dn_file);

            // 세로 사진 업로드시 사진 방향 잡아주기
            $objExif = @exif_read_data($dn_file);
            if(!empty($objExif['Orientation'])) {
                $objImage = imagecreatefromstring(file_get_contents($dn_file));
                switch($objExif['Orientation']) {
                    case 8:
                        $objImage = imagerotate($objImage,90,0);
                        break;
                    case 3:
                        $objImage = imagerotate($objImage,180,0);
                        break;
                    case 6:
                        $objImage = imagerotate($objImage,-90,0);
                        break;
                }
                imagejpeg($objImage, $dn_file);
                imagedestroy($objImage);
            }
		}
		else {
			return $this->error("[$dn_file] 디렉토리에 파일 업로드를 실패하였습니다.");
		}
	}

    function getFilePath() {
        $folder = "/attachment/".date('Ym');
        if(!is_dir(_USER_DIR.$folder)){
            @mkdir(_USER_DIR.$folder, 0777);
        }
        return $folder;
    }

    function uploadFileBulk($file_index) {	// 파일 업로드
		if ($this->arrFile['file']['name'][$file_index]) {
            $arrFileName = explode(".", $this->arrFile['file']['name'][$file_index]);
		    $file_ext = strtolower(array_pop($arrFileName));
            $file_path = $this->getFilePath();
            $file_name = getMicrotime().".".$file_ext;
            $dn_file = _USER_DIR."/".$file_path."/".$file_name;
            move_uploaded_file($this->arrFile['file']['tmp_name'][$file_index],$dn_file);

            // 세로 사진 업로드시 사진 방향 잡아주기
            if ($file_ext == "jpg" or $file_ext == "jpeg") {
                $objExif = exif_read_data($dn_file);
                if(!empty($objExif['Orientation'])) {
                    $objImage = imagecreatefromstring(file_get_contents($dn_file));
                    switch($objExif['Orientation']) {
                        case 8:
                            $objImage = imagerotate($objImage,90,0);
                            break;
                        case 3:
                            $objImage = imagerotate($objImage,180,0);
                            break;
                        case 6:
                            $objImage = imagerotate($objImage,-90,0);
                            break;
                    }
                    imagejpeg($objImage, $dn_file);
                    imagedestroy($objImage);
                }
            }

            $arrSize = getimagesize($dn_file);
            $arrFileInfo['file_path'] = $file_path;
            $arrFileInfo['file_name'] = $file_name;
            $arrFileInfo['orig_name'] = $this->arrFile['file']['name'][$file_index];
            $arrFileInfo['file_size'] = round(filesize($dn_file)/1024);
            $arrFileInfo['file_width'] = $arrSize[0];
            $arrFileInfo['file_height'] = $arrSize[1];
            $arrFileInfo['file_ext'] = strtolower($file_ext);

            return $arrFileInfo;
		}
		else {
			return $this->error("[$dn_file] 디렉토리에 파일 업로드를 실패하였습니다.");
		}
	}

	function deleteFile($file_name) {			// 파일 삭제
		if(file_exists("$file_name")) {
			@unlink($file_name);
		}
		else {
			return $this->error("[".$file_name."] 경로와 파일명이 일치하지 않습니다.");
		}
	}

    // 첨부파일 가져오기
	function getFile($file_name, $file_real_name) {
        if(file_exists(_USER_DIR."/".$file_name)) {
			$img_name = getFileIcon($file_ext);
			$file_real_name = base64_encode($file_real_name);
			$download = "<a href=?tpf=common/download&file_real_name=".$file_real_name."&file_name=".$file_name." target=iframe_download style=\"text-decoration:none\">".$img_name;
		}
		return $download;
	}

	// 파일 다운로드 함수 추가
	function download($file_real_name,$file_name) {
		set_time_limit(0);
		$file_full_name = _USER_DIR."/".$file_name;
		$file_size = filesize($file_full_name);
        $file_real_name = iconv('utf-8', 'euc-kr', $file_real_name);

		if(eregi("MSIE 5.5", $_SERVER['HTTP_USER_AGENT'])) { // IE 5.5 버그로 인해 분리함.
			Header("Content-type: application/attachment");
			Header("Content-Disposition: filename=".trim($file_real_name));
		} else {
			Header("Content-type: application/octet-stream");
			Header("Content-Disposition: attachment; filename=".trim($file_real_name));
		}
		Header("Content-Length: ".$file_size);

		$fp=fopen($file_full_name, "r");
		print fread($fp, $file_size);
		fclose($fp);

		exit;
	}
}
?>