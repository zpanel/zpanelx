<?
include("../lang/eng/prefs.inc");
/********************************************************
	lang/vn/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/
/*** Vietnamese support added by SnAKes (snx@ifrance.com) ***/

$prefHeaderStrings = array(
	0=> "Lựa chọn",
	1=>"Chung",
	2=>"Màu mè",
	3=>"Hộp thư"
);
											
$prefsStrings = array(
	"0.0" => "Chúng minh",
	"0.1" => "Tên: ",
	"0.2" => "Email: ",
	"0.3" => "thay đổi ở trang \"%s\"",
	"0.4" => "không có",
	"1.0" => "Quốc tế",
	"1.1" => "Ngữ: ",
	"1.2" => "Bộ chữ: ",
	"1.3" => "Múi giờ: ",
	"1.4" => "Hiện thị giờ: ",
	"1.5" => array(12=>"12AM/PM", 24=>"24h"),
	"2.0" => "Hộp thư",
	"2.1" => "Hiện thị ",
	"2.2" => " thư",
	"2.3" => "Hiện thị kích thước",
	"2.4" => "Sắp xêp theo: ",
	"2.5" => "",
	"2.6" => "Hướng sắp xếp",
	"2.7" => "",
	"2.8" => "Hiện thị hồ sơ",
	"2.9" => "Chiều rộng list hồ sơ",
        "2.10" => "Kiểm tra thư mới mỗi %n dây",
        "2.11" => "Hiện thị tool bar ở phía %m",
        "2.12" => array("b"=>"dưới", "t"=>"trên", "bt"=>"trên và dưới"),
        "2.13" => "Sắp xếp lại cột: ",
        "2.14" => "bấm lên đây",
	"3.0" => "Thu tự động",
	"3.1" => "Thu lại những thư gửi rồi.",
	"3.2"=> "Thu lại những thư được gửi vào hồ sơ: ",
	"3.3" => "",
	"3.4" => "Tự động đổ hộp rác",
	"3.5" => "Chuyển thư được xóa qua hồ sơ: ",
	"3.6" => "",
	"3.7" => "Hồ sơ chính: ",
	"3.8" => "Hồ sơ khác",
	"3.9" => "Giấu lại những hồ sơ chưa được đăng ký",
	"3.10" => "Hiện thị kích thước hộp thư %m",
	"3.11" => array(""=>"đừng hiện thị", "m"=>"trong list thư từ", "f"=>"trong list hồ sơ"),
	"4.0" => "Đọc thư",
	"4.1" => "Đọc trong khung, đừng hiện thị cửa sổ mới",
	"4.2" => "Đoạn trích dẫn bằng maù",
	"4.3" => "Chọn cái này có thể làm máy chạy chậm.",
        "4.4" => "Chuyểng URL thành đường nối",
	"4.5" => "Tôi muốn đọc thư bằng HTML",
	"4.6" => "Tối muốn coi hình trong thư",
	"4.7" => "Hiện thị đường nối",
	"4.8" => "Đừng đánh dấu thư khi đợc sơ",
	"5.0" => "Chữ ký",
	"5.1" => "Ghi chữ ký khi viết thư",
	"5.2" => "",
	"6.0" => "Viết thư",
	"6.1" => "Hiện thị cuốn sổ địa chỉ",
	"6.2" => "Hiện thị CC/BCC",
	"6.3" => "Đóng sau khi gửi được",
	"6.4" => "Khi viêt thư mới, đừng mở cửa sổ mới",
	"7.0.0" => "Ổ cứng &amp; hồ sơ",
	"7.0.1" => "Kích thước",
	"7.1" => "Hiện thị bao nhiêu thư chưa đọc",
	"7.2" => "Kiểm tra lại list hồ sơ mỗi %n dây"
);

$sort_fields=array("DATE"=>"Ngày", "SUBJECT"=>"Chủ đề", "SIZE"=>"Kích thước");
$sort_orders=array("ASC"=>"lên", "DESC"=>"suống");

$prefsButtonStrings = array (0=> "Cập nhật", 1=>"Lưu", 2=> "Bỏ", 3=> "Defaults");

$prefs_new_user = "Đây là lần đầu tiên bạn vào trương mục của minh.<br>";
$prefs_new_user .= "Hãy ghi vào chi tiết và click lên \"Thu\"";
?>
