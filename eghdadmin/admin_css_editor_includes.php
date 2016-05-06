<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_css_editor_includes.php                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


$css_array=array();
		
		$css_array[0][0] = "position";
		$css_array[0][1] = "relative,absolute,static,fixed";
		
		$css_array[1][0] = "align";
		$css_array[1][1] = "center,right,left,justify";
		
		$css_array[2][0] = "repeat";
		$css_array[2][1] = "repeat,repeat-x,repeat-y,no-repeat";
		
		$css_array[3][0] = "border_style";
		$css_array[3][1] = "none,dotted,dashed,solid,double,groove,ridge,inset,outset,hidden";
		
		$css_array[4][0] = "vertical-align";
		$css_array[4][1] = "baseline,sub,super,top,text-top,middle,bottom,text-bottom";
		
		$css_array[5][0] = "font-weight";
		$css_array[5][1] = "normal,bolder,bold,lighter";
		
		$css_array[6][0] = "display";
		$css_array[6][1] = "inline,block,list-item,run-in,compact,marker,inline-table,table-row-group,table-header-group,table-footer-group,table-row,table-column-group,table-column,table-cell,table-caption,table,none";
		
		$css_array[7][0] = "text-decoration";
		$css_array[7][1] = "none,underline,overlibe,line-through,blink";
		
		$css_array[8][0] = "text-transform";
		$css_array[8][1] = "none,capitalize,uppercase,lowcase";
		
		$css_array[9][0] = "font-size_value";
		$css_array[9][1] = "px,pt,%,em";
		
		$css_array[10][0] = "font-style";
		$css_array[10][1] = "normal,italic,oblique";

		$css_array[11][0] = "font-variant";
		$css_array[11][1] = "normal,small-caps";	
		
	$colors = array();
	$colors[0] = "F0F8FF";
	$colors[1] = "FAEBD7";
	$colors[2] = "00FFFF";
	$colors[3] = "7FFFD4";
	$colors[4] = "F0FFFF";
	$colors[5] = "F5F5DC";
	$colors[6] = "FFE4C4";
	$colors[7] = "000000";
	$colors[8] = "FFEBCD";
	$colors[9] = "0000FF";
	$colors[10] = "8A2BE2";
	$colors[11] = "A52A2A";
	$colors[12] = "DEB887";
	$colors[13] = "5F9EA0";
	$colors[14] = "7FFF00";
	$colors[15] = "D2691E";
	$colors[16] = "FF7F50";
	$colors[17] = "6495ED";
	$colors[18] = "FFF8DC";
	$colors[19] = "DC143C";
	$colors[20] = "00FFFF";
	$colors[21] = "00008B";
	$colors[22] = "008B8B";
	$colors[23] = "B8860B";
	$colors[24] = "A9A9A9";
	$colors[25] = "006400";
	$colors[26] = "BDB76B";
	$colors[27] = "8B008B";
	$colors[28] = "556B2F";
	$colors[29] = "FF8C00";
	$colors[30] = "9932CC";
	$colors[31] = "8B0000";
	$colors[32] = "E9967A";
	$colors[33] = "8FBC8F";
	$colors[34] = "483D8B";
	$colors[35] = "2F4F4F";
	$colors[36] = "00CED1";
	$colors[37] = "9400D3";
	$colors[38] = "FF1493";
	$colors[39] = "00BFFF";
	$colors[40] = "696969";
	$colors[41] = "1E90FF";
	$colors[42] = "B22222";
	$colors[43] = "FFFAF0";
	$colors[44] = "228B22";
	$colors[45] = "FF00FF";
	$colors[46] = "DCDCDC";
	$colors[47] = "F8F8FF";
	$colors[48] = "FFD700";
	$colors[49] = "DAA520";
	$colors[50] = "808080";
	$colors[51] = "008000";
	$colors[52] = "ADFF2F";
	$colors[53] = "F0FFF0";
	$colors[54] = "FF69B4";
	$colors[55] = "CD5C5C";
	$colors[56] = "4B0082";
	$colors[57] = "FFFFF0";
	$colors[58] = "F0E68C";
	$colors[59] = "E6E6FA";
	$colors[60] = "FFF0F5";
	$colors[61] = "7CFC00";
	$colors[62] = "FFFACD";
	$colors[63] = "ADD8E6";
	$colors[64] = "F08080";
	$colors[65] = "E0FFFF";
	$colors[66] = "FAFAD2";
	$colors[67] = "90EE90";
	$colors[68] = "D3D3D3";
	$colors[69] = "FFB6C1";
	$colors[70] = "FFA07A";
	$colors[71] = "20B2AA";
	$colors[72] = "87CEFA";
	$colors[73] = "778899";
	$colors[74] = "B0C4DE";
	$colors[75] = "FFFFE0";
	$colors[76] = "00FF00";
	$colors[77] = "32CD32";
	$colors[78] = "FAF0E6";
	$colors[79] = "FF00FF";
	$colors[80] = "800000";
	$colors[81] = "66CDAA";
	$colors[82] = "0000CD";
	$colors[83] = "BA55D3";
	$colors[84] = "9370D8";
	$colors[85] = "3CB371";
	$colors[86] = "7B68EE";
	$colors[87] = "00FA9A";
	$colors[88] = "48D1CC";
	$colors[89] = "C71585";
	$colors[90] = "191970";
	$colors[91] = "F5FFFA";
	$colors[92] = "FFE4E1";
	$colors[93] = "FFE4B5";
	$colors[94] = "FFDEAD";
	$colors[95] = "000080";
	$colors[96] = "FDF5E6";
	$colors[97] = "808000";
	$colors[98] = "688E23";
	$colors[99] = "FFA500";
	$colors[100] = "FF4500";
	$colors[101] = "DA70D6";
	$colors[102] = "EEE8AA";
	$colors[103] = "98FB98";
	$colors[104] = "AFEEEE";
	$colors[105] = "D87093";
	$colors[106] = "FFEFD5";
	$colors[107] = "FFDAB9";
	$colors[108] = "CD853F";
	$colors[109] = "FFC0CB";
	$colors[110] = "DDA0DD";
	$colors[111] = "B0E0E6";
	$colors[112] = "800080";
	$colors[113] = "FF0000";
	$colors[114] = "BC8F8F";
	$colors[115] = "4169E1";
	$colors[116] = "8B4513";
	$colors[117] = "FA8072";
	$colors[118] = "F4A460";
	$colors[119] = "2E8B57";
	$colors[120] = "FFF5EE";
	$colors[121] = "A0522D";
	$colors[122] = "C0C0C0";
	$colors[123] = "87CEEB";
	$colors[124] = "6A5ACD";
	$colors[125] = "708090";
	$colors[126] = "FFFAFA";
	$colors[127] = "00FF7F";
	$colors[128] = "4682B4";
	$colors[129] = "D2B48C";
	$colors[130] = "008080";
	$colors[131] = "D8BFD8";
	$colors[132] = "FF6347";
	$colors[133] = "40E0D0";
	$colors[134] = "EE82EE";
	$colors[135] = "F5DEB3";
	$colors[136] = "FFFFFF";
	$colors[137] = "F5F5F5";
	$colors[138] = "FFFF00";
	$colors[139] = "9ACD32";
	
	
	$fonts[] = array();
	$fonts[] = "";
?>