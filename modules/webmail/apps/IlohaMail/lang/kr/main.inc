<?php
include("../lang/eng/main.inc"); // <-- do not delete
/********************************************************
	lang/kr/main.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

function formatStatusString($num, $folder, $type){
	$str="";
	if ($type=="delete"){
		$str = "$num 개의 편지 지움".($num>1?"s":"");
	}else if ($type == "move"){
		$str = "$num 개의 편지 옮김".($num>1?"s":"")." to $folder";
	}
	
	return $str;
}

$mainStrings[0] = "%p of %n 표시";
$mainStrings[1] = " 편지 찾음";
$mainStrings[2] = "이전";
$mainStrings[3] = " 편지";
$mainStrings[4] = "다음";
$mainStrings[5] = " 편지";
$mainStrings[6] = "제목";
$mainStrings[7] = "To";
$mainStrings[8] = "From";
$mainStrings[9] = "날짜";
$mainStrings[10] = "삭제";
$mainStrings[11] = "휴지통 비우기";
$mainStrings[12] = "파일";
$mainStrings[13] = "검색";
$mainStrings[14] = "크기";
$mainStrings[15] = "제목없음";
$mainStrings[16] = "보기";
$mainStrings[17] = "새로 고침 ";
$mainStrings[18] = "모두 삭제";
$mainStrings[19] = "취소";


$mainErrors[0] = "편지함이 비어 있습니다.";
$mainErrors[1] = "편지함이 비어 있습니다.";
$mainErrors[2] = "휴지통으로 옮길수 없습니다..";
$mainErrors[3] = "편지를 지운것으로 표시할수 없습니다.";
$mainErrors[4] = "편지를 옮길수 없습니다.";
$mainErrors[5] = "휴지통이 지정되지 않았습니다.";
$mainErrors[6] = "휴지통을 비울수 없습니다.";
$mainErrors[7] = "%f 에 있는 모든 편지를 삭제하시겠습니까?";



?>