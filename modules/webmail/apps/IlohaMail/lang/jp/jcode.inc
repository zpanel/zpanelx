<?php
////////////////////////////////
//	jcode.inc
//
//		Copied entirely from mini_jcode.phps by TOMO <groove@spencernetwork.org>
//
////////////////////////////////

function JcodeConvert(&$str, $from, $to)
{
	//0:AUTO DETECT
	//1:EUC-JP
	//2:Shift_JIS
	//3:ISO-2022-JP(JIS)

	if ($from == 0) $from = AutoDetect($str);
	if ($from == 1 && $to == 2) return EUCtoSJIS($str);
	if ($from == 1 && $to == 3) return EUCtoJIS($str);
	if ($from == 2 && $to == 1) return SJIStoEUC($str);
	if ($from == 2 && $to == 3) return SJIStoJIS($str);
	if ($from == 3 && $to == 1) return JIStoEUC($str);
	if ($from == 3 && $to == 2) return JIStoSJIS($str);

	return $str;
}

function AutoDetect(&$str)
{
	//0:US-ASCII
	//1:EUC-JP
	//2:Shift_JIS
	//3:ISO-2022-JP(JIS)
	//(4:Not available)
	//5:Unknown

	if (Jcode_is_JIS($str))   return 3; //JIS
	if (Jcode_is_ASCII($str)) return 0; //ASCII

	$b = unpack("C*", $str);

	if (Jcode_is_EUC($b))     return 1; //EUC
	if (Jcode_is_SJIS($b))    return 2; //SJIS

	return 5;
}

function HANtoZEN(&$str, $encode)
{
	//0:PASS
	//1:EUC-JP
	//2:Shift_JIS
	//3:ISO-2022-JP(JIS)

	if ($encode == 0) return $str;
	if ($encode == 1) return HANtoZEN_EUC($str);
	if ($encode == 2) return HANtoZEN_SJIS($str);
	if ($encode == 3) return HANtoZEN_JIS($str);

	return $str;
}

function ZENtoHAN(&$str, $encode, $kana=1, $alph=1)
{
	//0:PASS
	//1:EUC-JP
	//2:Shift_JIS
	//3:ISO-2022-JP(JIS)

	if ($encode == 0) return $str;
	if ($encode == 1) return ZENtoHAN_EUC($str,  $kana, $alph, $kana);
	if ($encode == 2) return ZENtoHAN_SJIS($str, $kana, $alph, $kana);
	if ($encode == 3) return ZENtoHAN_JIS($str, $kana, $alph, $kana);

	return $str;
}


function JIStoSJIS(&$str_JIS)
{
	$str_SJIS = '';
	$mode = 0;
	$b = unpack("C*", $str_JIS);
	$n = count($b);

	for ($i = 1; $i <= $n; $i++) {
		//Check escape sequence
		if ($b[$i] == 0x1B) {
			if (($b[$i+1] == 0x24 && $b[$i+2] == 0x42)
				|| ($b[$i+1] == 0x24 && $b[$i+2] == 0x40)) {
				$mode = 1;
			} elseif (($b[$i+1] == 0x28 && $b[$i+2] == 0x49)) {
				$mode = 2;
			} else {
				$mode = 0;
			}
			$i += 3;
		}

		if (!isset($b[$i])) break;

		//Do convert
		if ($mode == 1) {
			$b1 = $b[$i];
			$b2 = $b[$i+1];
			if ($b1 & 0x01) {
				$b1 >>= 1;
				if ($b1 < 0x2F) $b1 += 0x71; else $b1 -= 0x4F;
				if ($b2 > 0x5F) $b2 += 0x20; else $b2 += 0x1F;
			} else {
				$b1 >>= 1;
				if ($b1 <= 0x2F) $b1 += 0x70; else $b1 -= 0x50;
				$b2 += 0x7E;
			}
			$str_SJIS .= chr($b1).chr($b2);
			$i++;
		} elseif ($mode == 2) {
			$str_SJIS .= chr($b[$i] + 0x80);
		} else {
			$str_SJIS .= chr($b[$i]);
		}
	}

	return $str_SJIS;
}

function JIStoEUC(&$str_JIS)
{
	$str_EUC = '';
	$mode = 0;
	$b = unpack("C*", $str_JIS);
	$n = count($b);

	for ($i = 1; $i <= $n; $i++) {
		//Check escape sequence
		if ($b[$i] == 0x1B) {
			if (($b[$i+1] == 0x24 && $b[$i+2] == 0x42)
				|| ($b[$i+1] == 0x24 && $b[$i+2] == 0x40)) {
				$mode = 1;
			} elseif (($b[$i+1] == 0x28 && $b[$i+2] == 0x49)) {
				$mode = 2;
			} else {
				$mode = 0;
			}
			$i += 3;
		}

		if (!isset($b[$i])) break;

		//Do convert
		if ($mode == 1) {
			$str_EUC .= chr($b[$i] + 0x80).chr($b[$i+1] + 0x80);
			$i++;
		} elseif ($mode == 2) {
			$str_EUC .= chr(0x8E).chr($b[$i] + 0x80);
		} else {
			$str_EUC .= chr($b[$i]);
		}
	}

	return $str_EUC;
}

function SJIStoJIS(&$str_SJIS)
{
	$str_JIS = '';
	$mode = 0;
	$b = unpack("C*", $str_SJIS);
	$n = count($b);
	//Escape sequence
	$ESC = array(chr(0x1B).chr(0x28).chr(0x42),
	             chr(0x1B).chr(0x24).chr(0x42),
	             chr(0x1B).chr(0x28).chr(0x49));

	for ($i = 1; $i <= $n; $i++) {
		$b1 = $b[$i];
		if (0xA1 <= $b1 && $b1 <= 0xDF) {
			if ($mode != 2) {
				$mode = 2;
				$str_JIS .= $ESC[$mode];
			}
			$str_JIS .= chr($b1 - 0x80);
		} elseif ($b1 >= 0x80) {
			if ($mode != 1) {
				$mode = 1;
				$str_JIS .= $ESC[$mode];
			}
			$b2 = $b[$i+1];
			$b1 <<= 1;
			if ($b2 < 0x9F) {
				if ($b1 < 0x13F) $b1 -= 0xE1; else $b1 -= 0x61;
				if ($b2 > 0x7E)  $b2 -= 0x20; else $b2 -= 0x1F;
			} else {
				if ($b1 < 0x13F) $b1 -= 0xE0; else $b1 -= 0x60;
				$b2 -= 0x7E;
			}
			$str_JIS .= chr($b1).chr($b2);
			$i++;
		} else {
			if ($mode != 0) {
				$mode = 0;
				$str_JIS .= $ESC[$mode];
			}
			$str_JIS .= chr($b1);
		}
	}
	if ($mode != 0) $str_JIS .= $ESC[0];

	return $str_JIS;
}

function SJIStoEUC(&$str_SJIS)
{
	$b = unpack("C*", $str_SJIS);
	$n = count($b);
	$str_EUC = '';

	for ($i = 1; $i <= $n; $i++) {
		$b1 = $b[$i];
		if (0xA1 <= $b1 && $b1 <= 0xDF) {
			$str_EUC .= chr(0x8E).chr($b1);
		} elseif ($b1 >= 0x81) {
			$b2 = $b[$i+1];
			$b1 <<= 1;
			if ($b2 < 0x9F) {
				if ($b1 < 0x13F) $b1 -= 0x61; else $b1 -= 0xE1;
				if ($b2 > 0x7E)  $b2 += 0x60; else $b2 += 0x61;
			} else {
				if ($b1 < 0x13F) $b1 -= 0x60; else $b1 -= 0xE0;
				$b2 += 0x02;
			}
			$str_EUC .= chr($b1).chr($b2);
			$i++;
		} else {
			$str_EUC .= chr($b1);
		}
	}

	return $str_EUC;
}

function EUCtoJIS(&$str_EUC)
{
	$str_JIS = '';
	$mode = 0;
	$b = unpack("C*", $str_EUC);
	$n = count($b);
	//Escape sequence
	$ESC = array(chr(0x1B).chr(0x28).chr(0x42),
	             chr(0x1B).chr(0x24).chr(0x42),
	             chr(0x1B).chr(0x28).chr(0x49));

	for ($i = 1; $i <= $n; $i++) {
		$b1 = $b[$i];
		if ($b1 == 0x8E) {
			if ($mode != 2) {
				$mode = 2;
				$str_JIS .= $ESC[$mode];
			}
			$str_JIS .= chr($b[$i+1] - 0x80);
			$i++;
		} elseif ($b1 > 0x8E) {
			if ($mode != 1) {
				$mode = 1;
				$str_JIS .= $ESC[$mode];
			}
			$str_JIS .= chr($b1 - 0x80).chr($b[$i+1] - 0x80);
			$i++;
		} else {
			if ($mode != 0) {
				$mode = 0;
				$str_JIS .= $ESC[$mode];
			}
			$str_JIS .= chr($b1);
		}
	}
	if ($mode != 0) $str_JIS .= $ESC[0];

	return $str_JIS;
}

function EUCtoSJIS(&$str_EUC)
{
	$str_SJIS = '';
	$b = unpack("C*", $str_EUC);
	$n = count($b);

	for ($i = 1; $i <= $n; $i++) {
		$b1 = $b[$i];
		if ($b1 == 0x8E) {
			$str_SJIS .= chr($b[$i+1]);
			$i++;
		} elseif ($b1 >= 0x8E) {
			$b2 = $b[$i+1];
			if ($b1 & 0x01) {
				$b1 >>= 1;
				if ($b1 < 0x6F) $b1 += 0x31; else $b1 += 0x71;
				if ($b2 > 0xDF) $b2 -= 0x60; else $b2 -= 0x61;
			} else {
				$b1 >>= 1;
				if ($b1 <= 0x6F) $b1 += 0x30; else $b1 += 0x70;
				$b2 -= 0x02;
			}
			$str_SJIS .= chr($b1).chr($b2);
			$i++;
		} else {
			$str_SJIS .= chr($b1);
		}
	}

	return $str_SJIS;
}

function HANtoZEN_EUC(&$str_HAN)
{
	$table_han2zen_euc = array(0xA1A3,0xA1D6,0xA1D7,0xA1A2,0xA1A6,0xA5F2,
	0xA5A1,0xA5A3,0xA5A5,0xA5A7,0xA5A9,0xA5E3,0xA5E5,0xA5E7,0xA5C3,0xA1BC,
	0xA5A2,0xA5A4,0xA5A6,0xA5A8,0xA5AA,0xA5AB,0xA5AD,0xA5AF,0xA5B1,0xA5B3,
	0xA5B5,0xA5B7,0xA5B9,0xA5BB,0xA5BD,0xA5BF,0xA5C1,0xA5C4,0xA5C6,0xA5C8,
	0xA5CA,0xA5CB,0xA5CC,0xA5CD,0xA5CE,0xA5CF,0xA5D2,0xA5D5,0xA5D8,0xA5DB,
	0xA5DE,0xA5DF,0xA5E0,0xA5E1,0xA5E2,0xA5E4,0xA5E6,0xA5E8,0xA5E9,0xA5EA,
	0xA5EB,0xA5EC,0xA5ED,0xA5EF,0xA5F3,0xA1AB,0xA1AC);

	$str_ZEN = '';
	$b = unpack("C*", $str_HAN);
	$n = count($b);

	for ($i = 1; $i <= $n; $i++) {
		$b1 = $b[$i];
		if ($b1 == 0x8E) {
			$b2 = $b[$i+1] - 0xA1;
			$c1 = ($table_han2zen_euc[$b2] & 0xFF00) >> 8;
			$c2 =  $table_han2zen_euc[$b2] & 0x00FF;
			$str_ZEN .= chr($c1).chr($c2);
			$i++;
		} elseif ($b1 >= 0xA1) {
			$str_ZEN .= chr($b1).chr($b[$i+1]);
			$i++;
		} else {
			$str_ZEN .= chr($b1);
		}
	}

	return $str_ZEN;
}

function HANtoZEN_SJIS(&$str_HAN)
{
	$table_han2zen_sjis = array(0x8142,0x8175,0x8176,0x8141,0x8145,0x8392,
	0x8340,0x8342,0x8344,0x8346,0x8348,0x8383,0x8385,0x8387,0x8362,0x815B,
	0x8341,0x8343,0x8345,0x8347,0x8349,0x834A,0x834C,0x834E,0x8350,0x8352,
	0x8354,0x8356,0x8358,0x835A,0x835C,0x835E,0x8360,0x8363,0x8365,0x8367,
	0x8369,0x836A,0x836B,0x836C,0x836D,0x836E,0x8371,0x8374,0x8377,0x837A,
	0x837D,0x837E,0x8380,0x8381,0x8382,0x8384,0x8386,0x8388,0x8389,0x838A,
	0x838B,0x838C,0x838D,0x838F,0x8393,0x814A,0x814B);

	$str_ZEN = '';
	$b = unpack("C*", $str_HAN);
	$n = count($b);

	for ($i = 1; $i <= $n; $i++) {
		$b1 = $b[$i];
		if (0xA1 <= $b1 && $b1 <= 0xDF) {
			$b1 -= 0xA1;
			$c1 = ($table_han2zen_sjis[$b1] & 0xFF00) >> 8;
			$c2 =  $table_han2zen_sjis[$b1] & 0x00FF;
			$str_ZEN .= chr($c1).chr($c2);
		} elseif ($b1 >= 0x80) {
			$str_ZEN .= chr($b1).chr($b[$i+1]);
			$i++;
		} else {
			$str_ZEN .= chr($b1);
		}
	}

	return $str_ZEN;
}

function HANtoZEN_JIS(&$str_HAN)
{
	$table_han2zen_jis = array(0x2123,0x2156,0x2157,0x2122,0x2126,0x2572,
	0x2521,0x2523,0x2525,0x2527,0x2529,0x2563,0x2565,0x2567,0x2543,0x213C,
	0x2522,0x2524,0x2526,0x2528,0x252A,0x252B,0x252D,0x252F,0x2531,0x2533,
	0x2535,0x2537,0x2539,0x253B,0x253D,0x253F,0x2541,0x2544,0x2546,0x2548,
	0x254A,0x254B,0x254C,0x254D,0x254E,0x254F,0x2552,0x2555,0x2558,0x255B,
	0x255E,0x255F,0x2560,0x2561,0x2562,0x2564,0x2566,0x2568,0x2569,0x256A,
	0x256B,0x256C,0x256D,0x256F,0x2573,0x212B,0x212C);

	$str_ZEN = '';
	$b = unpack("C*", $str_HAN);
	$n = count($b);
	$mode = 0;
	$new_mode = 0;
	$esc = FALSE;
	$ESC = array(chr(0x1B).chr(0x28).chr(0x42),
	             chr(0x1B).chr(0x24).chr(0x42),
	             chr(0x1B).chr(0x28).chr(0x49));

	for ($i = 1; $i <= $n; $i++) {

		if ($b[$i] == 0x1B) {
			if (($b[$i+1] == 0x24 && $b[$i+2] == 0x42)
				|| ($b[$i+1] == 0x24 && $b[$i+2] == 0x40)) {
				$mode = 1; //Zenkaku
			} elseif ($b[$i+1] == 0x28 && $b[$i+2] == 0x49) {
				$mode = 2; //Hankaku
			} else {
				$mode = 0; //ASCII
			}
			$i += 3;
		}

		if (!isset($b[$i])) break;

		if ($mode == 2) {
			if ($new_mode != 1) $esc = TRUE;
			$new_mode = 1;
			$c  = $b[$i] - 0x21;
			$c1 = ($table_han2zen_jis[$c] & 0xFF00) >> 8;
			$c2 =  $table_han2zen_jis[$c] & 0x00FF;
			$str = chr($c1).chr($c2);
		} else {
			if ($new_mode != $mode) $esc = TRUE;
			$new_mode = $mode;
			$str = chr($b[$i]);
		}

		if ($esc) {  //add escape sequence
			$str_ZEN .= $ESC[$new_mode];
			$esc = FALSE;
		}
		$str_ZEN .= $str;
	}

	if ($new_mode != 0) $str_ZEN .= $ESC[0];

	return $str_ZEN;
}

function ZENtoHAN_EUC(&$str_ZEN, $kana = 1, $alph = 1, $sym = 1)
{
	$kana_euc = array(
	0x00A7,0x00B1,0x00A8,0x00B2,0x00A9,0x00B3,0x00AA,0x00B4,0x00AB,0x00B5,
	0x00B6,0xB6DE,0x00B7,0xB7DE,0x00B8,0xB8DE,0x00B9,0xB9DE,0x00BA,0xBADE,
	0x00BB,0xBBDE,0x00BC,0xBCDE,0x00BD,0xBDDE,0x00BE,0xBEDE,0x00BF,0xBFDE,
	0x00C0,0xC0DE,0x00C1,0xC1DE,0x00AF,0x00C2,0xC2DE,0x00C3,0xC3DE,0x00C4,
	0xC4DE,0x00C5,0x00C6,0x00C7,0x00C8,0x00C9,0x00CA,0xCADE,0xCADF,0x00CB,
	0xCBDE,0xCBDF,0x00CC,0xCCDE,0xCCDF,0x00CD,0xCDDE,0xCDDF,0x00CE,0xCEDE,
	0xCEDF,0x00CF,0x00D0,0x00D1,0x00D2,0x00D3,0x00AC,0x00D4,0x00AD,0x00D5,
	0x00AE,0x00D6,0x00D7,0x00D8,0x00D9,0x00DA,0x00DB,0x0000,0x00DC,0x0000,
	0x0000,0x00A6,0x00DD,0xB3DE,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000
	);

	$sym_euc = array(
	0x0020,0x8EA4,0x8EA1,0x0000,0x0000,0x8EA5,0x0000,0x0000,0x0000,0x0000,
	0x8EDE,0x8EDF,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x8EB0,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x8EA2,0x8EA3,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000
	);

	$str_HAN = '';
	$b = unpack("C*", $str_ZEN);
	$n = count($b);

	for ($i = 1; $i <= $n; $i++) {
		$b1 = $b[$i];
		if ($b1 >= 0x80) {
			if ($kana == 1 && $b1 == 0xA5) { // Katakana
				$c  = $b[$i+1] - 0xA1;
				$c1 = ($kana_euc[$c] & 0xFF00) >> 8;
				$c2 =  $kana_euc[$c] & 0x00FF;
				if ($c1 == 0x00) {
					if ($c2 == 0x00) {
						$str_HAN .= chr($b1).chr($b[$i+1]);
					} else {
						$str_HAN .= chr(0x8E).chr($c2);
					}
				} else {
					$str_HAN .= chr(0x8E).chr($c1).chr(0x8E).chr($c2);
				}
			} elseif ($sym == 1 && $b1 == 0xA1) { // Symbol
				$c = $b[$i+1] - 0xA1;
				$c1 = ($sym_euc[$c] & 0xFF00) >> 8;
				$c2 =  $sym_euc[$c] & 0x00FF;
				if ($c1 == 0x00) {
					if ($c2 == 0x00) {
						$str_HAN .= chr($b1).chr($b[$i+1]);
					} else {
						$str_HAN .= chr($c2);
					}
				} else {
					$str_HAN .= chr($c1).chr($c2);
				}
			} elseif ( $alph == 1 && $b1 == 0xA3 ) { // Alphabet & Number
				$str_HAN .= chr($b[$i+1] - 0x80);
			} else { // Rest of Zenkaku
				$str_HAN .= chr($b1).chr($b[$i+1]);
			}
			$i++;
		} else {  // ASCII
			$str_HAN .= chr($b1);
		}
	}

	return $str_HAN;
}

function ZENtoHAN_SJIS(&$str_ZEN, $kana = 1, $alph = 1, $sym = 1)
{
	$kana_sjis = array(
	0x00A7,0x00B1,0x00A8,0x00B2,0x00A9,0x00B3,0x00AA,0x00B4,0x00AB,0x00B5,
	0x00B6,0xB6DE,0x00B7,0xB7DE,0x00B8,0xB8DE,0x00B9,0xB9DE,0x00BA,0xBADE,
	0x00BB,0xBBDE,0x00BC,0xBCDE,0x00BD,0xBDDE,0x00BE,0xBEDE,0x00BF,0xBFDE,
	0x00C0,0xC0DE,0x00C1,0xC1DE,0x00AF,0x00C2,0xC2DE,0x00C3,0xC3DE,0x00C4,
	0xC4DE,0x00C5,0x00C6,0x00C7,0x00C8,0x00C9,0x00CA,0xCADE,0xCADF,0x00CB,
	0xCBDE,0xCBDF,0x00CC,0xCCDE,0xCCDF,0x00CD,0xCDDE,0xCDDF,0x00CE,0xCEDE,
	0xCEDF,0x00CF,0x00D0,0x0000,0x00D1,0x00D2,0x00D3,0x00AC,0x00D4,0x00AD,
	0x00D5,0x00AE,0x00D6,0x00D7,0x00D8,0x00D9,0x00DA,0x00DB,0x0000,0x00DC,
	0x0000,0x0000,0x00A6,0x00DD,0xB3DE,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000
	);

	$sym_sjis = array(
	0x20,0xA4,0xA1,0x00,0x00,0xA5,0x00,0x00,0x00,0x00,0xDE,0xDF,0x00,0x00,
	0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0xB0,
	0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,
	0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0xA2,0xA3,0x00,
	0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,
	0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,
	0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00
	);

	$str_HAN = '';
	$b = unpack("C*", $str_ZEN);
	$n = count($b);

	for ($i = 1; $i <= $n; $i++) {
		$b1 = $b[$i];
		if ($b1 >= 0x80) {
			$b2 = $b[$i+1];
			if ($kana == 1 && $b1 == 0x83  // Katakana
				&& (0x3F < $b2 && $b2 < 0x9F)) {
				$c = $b2 - 0x40;
				$c1 = ($kana_sjis[$c] & 0xFF00) >> 8;
				$c2 =  $kana_sjis[$c] & 0x00FF;
				if ($c1 == 0x00) {
					if ($c2 == 0x00) {
						$str_HAN .= chr($b1).chr($b2);
					} else {
						$str_HAN .= chr($c2);
					}
				} else {
					$str_HAN .= chr($c1).chr($c2);
				}
			} elseif ($sym == 1 && $b1 == 0x81  // Symbol
				&& (0x3F < $b2 && $b2 < 0x9F)) {
				$c1 = $sym_sjis[ $b2 - 0x40 ];
				if ($c1 == 0x00) {
					$str_HAN .= chr($b1).chr($b2);
				} else {
					$str_HAN .= chr($c1);
				}
			} elseif ($alph == 1 && $b1 == 0x82  // Alphabet & Number
				&& (0x3F < $b2 && $b2 < 0x9F)) {
				if ($b[$i+1] < 0x80) {
					$str_HAN .= chr($b2 - 0x1F);
				} else {
					$str_HAN .= chr($b2 - 0x20);
				}
			} else { // Rest of Zenkaku
				$str_HAN .= chr($b1).chr($b2);
			}
			$i++;
		} else {  // ASCII
			$str_HAN .= chr($b1);
		}
	}

	return $str_HAN;
}

function ZENtoHAN_JIS(&$str_ZEN, $kana = 1, $alph = 1, $sym = 1)
{
	$kana_jis = array(
	0x0027,0x0031,0x0028,0x0032,0x0029,0x0033,0x002A,0x0034,0x002B,0x0035,
	0x0036,0x365E,0x0037,0x375E,0x0038,0x385E,0x0039,0x395E,0x003A,0x3A5E,
	0x003B,0x3B5E,0x003C,0x3C5E,0x003D,0x3D5E,0x003E,0x3E5E,0x003F,0x3F5E,
	0x0040,0x405E,0x0041,0x415E,0x002F,0x0042,0x425E,0x0043,0x435E,0x0044,
	0x445E,0x0045,0x0046,0x0047,0x0048,0x0049,0x004A,0x4A5E,0x4A5F,0x004B,
	0x4B5E,0x4B5F,0x004C,0x4C5E,0x4C5F,0x004D,0x4D5E,0x4D5F,0x004E,0x4E5E,
	0x4E5F,0x004F,0x0050,0x0051,0x0052,0x0053,0x002C,0x0054,0x002D,0x0055,
	0x002E,0x0056,0x0057,0x0058,0x0059,0x005A,0x005B,0x0000,0x005C,0x0000,
	0x0000,0x0026,0x005D,0x335E,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000
	);

	$sym_jis = array(
	0x0020,0xFF24,0xFF21,0x0000,0x0000,0xFF25,0x0000,0x0000,0x0000,0x0000,
	0xFF5E,0xFF5F,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0xFF30,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0xFF22,0xFF23,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000
	);

	$str_HAN = '';
	$b = unpack("C*", $str_ZEN);
	$n = count($b);
	$mode = 0;
	$new_mode = 0;
	$esc = FALSE;
	$ESC = array(chr(0x1B).chr(0x28).chr(0x42),
	             chr(0x1B).chr(0x24).chr(0x42),
	             chr(0x1B).chr(0x28).chr(0x49));

	for ($i = 1; $i <= $n; $i++) {
		if ($b[$i] == 0x1B) {
			if (($b[$i+1] == 0x24 && $b[$i+2] == 0x42)
				|| ($b[$i+1] == 0x24 && $b[$i+2] == 0x40)) {
				$mode = 1;
			} elseif ($b[$i+1] == 0x28 && $b[$i+2] == 0x49) {
				$mode = 2;
			} else {
				$mode = 0;
			}
			$i += 3;
		}

		if (!isset($b[$i])) break;

		$b1 = $b[$i];
		if ($mode == 1) { //Zenkaku
			if ($alph == 1 && $b1 == 0x23) {  //Alphabet & Number
				if ($new_mode != 0) $esc = TRUE;
				$new_mode = 0;
				$str = chr($b[$i+1]);
			} elseif ($sym == 1 && $b1 == 0x21) {  //Symbol
				$c = $b[$i+1] - 0x21;
				$c1 = ($sym_jis[$c] & 0xFF00) >> 8;
				$c2 =  $sym_jis[$c] & 0x00FF;
				if ($c1 == 0x00) {
					if ($c2 == 0x00) {
						if ($new_mode != 1) $esc = TRUE;
						$new_mode = 1;
						$str = chr($b1).chr($b[$i+1]);
					} else {
						if ($new_mode != 0) $esc = true;
						$new_mode = 0;
						$str = chr($c2);
					}
				} else {
					if ($new_mode != 2) $esc = TRUE;
					$new_mode = 2;
					$str = chr($c2);
				}
			} elseif ($kana == 1 && $b1 == 0x25) {  //Katakana
				$c  = $b[$i+1] - 0x21;
				$c1 = ($kana_jis[$c] & 0xFF00) >> 8;
				$c2 =  $kana_jis[$c] & 0x00FF;
				if ($c1 == 0x00) {
					if ($c2 == 0x00) {
						if ($new_mode != 1) $esc = TRUE;
						$new_mode = 1;
						$str = chr($b1).chr($b[$i+1]);
					} else {
						if ($new_mode != 2) $esc = TRUE;
						$new_mode = 2;
						$str = chr($c2);
					}
				} else {
					if ($new_mode != 2) $esc = TRUE;
					$new_mode = 2;
					$str = chr($c1).chr($c2);
				}
			} else {
				if ($new_mode != 1) $esc = TRUE;
				$new_mode = 1;
				$str = chr($b1).chr($b[$i+1]);
			}
			$i++;
		} elseif ($mode == 2) {
			if ($new_mode != 2) $esc = TRUE;
			$new_mode = 2;
			$str = chr($b1);
		} else {
			if ($new_mode != 0) $esc = TRUE;
			$new_mode = 0;
			$str = chr($b1);
		}

		if ($esc) {  //add escape sequense
			$str_HAN .= $ESC[$new_mode];
			$esc = FALSE;
		}
		$str_HAN .= $str;
	}

	if ($new_mode != 0) $str_HAN .= $ESC[0];

	return $str_HAN;
}

/*************************************************************************/

function Jcode_is_EUC(&$b)
{
	$n = count($b);
	for ($i = 1; $i <= $n; $i++) {
		$b1 = $b[$i];
		if ($b1 == 0x8E) {
			if (!isset($b[$i+1])) {
				return FALSE;
			}
			$b2 = $b[$i+1] - 0x80;
			if ((0x20 > $b2) || ($b2 > 0x7F)) {
				return FALSE;
			}
			$i++;
		} elseif ($b1 > 0x8E) {
			$b1 -= 0x80;
			if ((0x20 > $b1) || ($b1 > 0x7F)) {
				return FALSE;
			}
			if (!isset($b[$i+1])) {
				return FALSE;
			}
			$b2 = $b[$i+1] - 0x80;
			if ((0x20 > $b2) || ($b2 > 0x7F)) {
				return FALSE;
			}
			$i++;
		} elseif ($b1 <= 0x7F){
			continue;
		} else {
			return FALSE;
		}
	}
	return TRUE;
}

function Jcode_is_SJIS(&$b)
{
	$n = count($b);
	for ($i = 1; $i <= $n; $i++) {
		$b1 = $b[$i];
		if (0xA1 <= $b1 && $b1 <= 0xDF) {
			$b1 -= 0x80;
			if ((0x20 > $b1) || ($b1 > 0x7F)) {
				return FALSE;
			}
		} elseif ($b1 >= 0x80) {
			$b2 = $b[$i+1];
			$b1 <<= 1;
			if ($b2 < 0x9F) {
				if ($b1 < 0x13F) $b1 -= 0xE1; else $b1 -= 0x61;
				if ($b2 > 0x7E)  $b2 -= 0x20; else $b2 -= 0x1F;
			} else {
				if ($b1 < 0x13F) $b1 -= 0xE0; else $b1 -= 0x60;
				$b2 -= 0x7E;
			}
			if (((0x20 > $b1) || ($b1 > 0x7F))
				|| ((0x20 > $b2) || ($b2 > 0x7F))) {
				return FALSE;
			}
			$i++;
		} elseif ($b1 <= 0x7F){
			continue;
		} else {
			return FALSE;
		}
	}

	return TRUE;
}

function Jcode_is_JIS(&$str)
{
	return ereg("\x1B", $str);
}

function Jcode_is_ASCII(&$str)
{
	return !ereg("[\x80-\xFF]", $str);
}
?>