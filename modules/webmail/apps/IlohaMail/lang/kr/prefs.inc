<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/kr/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$prefHeaderStrings[0] = "환경설정";
$prefHeaderStrings[1] = "일반";
$prefHeaderStrings[2] = "색상";

											
$prefsStrings["0.0"] = "기본정보";
$prefsStrings["0.1"] = "이름: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["1.0"] = "다른 언어";
$prefsStrings["1.1"] = "언어 환경: ";
$prefsStrings["1.2"] = "글자 셋: ";
$prefsStrings["1.3"] = "시간대: ";
$prefsStrings["2.0"] = "편지함 보기 설정";
$prefsStrings["2.1"] = "한 페이지에 ";
$prefsStrings["2.2"] = " 씩 보기";
$prefsStrings["2.3"] = "크기 보기";
$prefsStrings["2.4"] = "정렬: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "정렬순서: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "편지함 목록보기";
$prefsStrings["3.0"] = "자동 저장 설정";
$prefsStrings["3.1"] = "보낸 편지 저장.";
$prefsStrings["3.2"] = "보낸 편지 저장: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "휴지통 자동으로 비우기";
$prefsStrings["3.5"] = "지운 편지 저장: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "기본 디렉토리: ";
$prefsStrings["3.8"] = "기타";
$prefsStrings["4.0"] = "보기 설정";
$prefsStrings["4.1"] = "프레임에서 보기";
$prefsStrings["4.2"] = "검색어 강조";
$prefsStrings["4.3"] = "이 설정은 시스템을 느리게 할수도 있습니다.";
$prefsStrings["4.4"] = "URL을 HTML로";
$prefsStrings["4.5"] = "HTML로 작성된 편지보기";
$prefsStrings["4.6"] = "그림 보기";
$prefsStrings["5.0"] = "서명";
$prefsStrings["5.1"] = "서명 항상 사용";
$prefsStrings["5.2"] = "";


$sort_fields=array("DATE"=>"날짜", "SUBJECT"=>"제목", "SIZE"=>"크기");
$sort_orders=array("ASC"=>"올림차순", "DESC"=>"내림차순");

$prefsButtonStrings = array( 0=> "고치기", 1=>"적용", 2=> "취소", 3=> "기본설정");

$prefs_new_user = "처음 로그인하셨군요, 반갑습니다!<br>";
$prefs_new_user .= "환경설정을 하시고 \"적용\"을 눌러주세요.";
?>