<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_items_list.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/shopping_cart.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("products_categories");

	$permissions = get_permissions();
	$products_settings = get_setting_value($permissions, "products_settings", 0);
	$product_types = get_setting_value($permissions, "product_types", 0);
	$manufacturers = get_setting_value($permissions, "manufacturers", 0);
	$products_reviews = get_setting_value($permissions, "products_reviews", 0);
	$shipping_methods = get_setting_value($permissions, "shipping_methods", 0);
	$shipping_times = get_setting_value($permissions, "shipping_times", 0);
	$shipping_rules = get_setting_value($permissions, "shipping_rules", 0);
	$downloadable_products = get_setting_value($permissions, "downloadable_products", 0);
	$coupons = get_setting_value($permissions, "coupons", 0);
	$advanced_search = get_setting_value($permissions, "advanced_search", 0);
	$products_report = get_setting_value($permissions, "products_report", 0);
	$product_prices = get_setting_value($permissions, "product_prices", 0);
	$product_images = get_setting_value($permissions, "product_images", 0);
	$product_properties = get_setting_value($permissions, "product_properties", 0);
	$product_features = get_setting_value($permissions, "product_features", 0);
	$product_related = get_setting_value($permissions, "product_related", 0);
	$product_categories = get_setting_value($permissions, "product_categories", 0);
	$product_accessories = get_setting_value($permissions, "product_accessories", 0);
	$product_releases = get_setting_value($permissions, "product_releases", 0);
	$products_order = get_setting_value($permissions, "products_order", 0);
	$products_export = get_setting_value($permissions, "products_export", 0);
	$products_import = get_setting_value($permissions, "products_import", 0);
	$products_export_google_base = get_setting_value($permissions, "products_export_google_base", 0);
	$features_groups = get_setting_value($permissions, "features_groups", 0);
	$tell_friend = get_setting_value($permissions, "tell_friend", 0);
	$categories_export = get_setting_value($permissions, "categories_export", 0);
	$categories_import = get_setting_value($permissions, "categories_import", 0);
	$categories_order = get_setting_value($permissions, "categories_order", 0);
	$view_categories = get_setting_value($permissions, "view_categories", 0);
	$view_products = get_setting_value($permissions, "view_products", 0);
	$add_categories = get_setting_value($permissions, "add_categories", 0);
	$update_categories = get_setting_value($permissions, "update_categories", 0);
	$remove_categories = get_setting_value($permissions, "remove_categories", 0);
	$add_products = get_setting_value($permissions, "add_products", 0);
	$update_products = get_setting_value($permissions, "update_products", 0);
	$remove_products = get_setting_value($permissions, "remove_products", 0);
	$approve_products = get_setting_value($permissions, "approve_products", 0);
	$view_only_products = !$update_products && $view_products;
	$read_only_products = !$update_products && !$view_products;
	$view_only_categories = !$update_categories && !$remove_categories && $view_categories;
	$read_only_categories = !$update_categories && !$remove_categories && !$view_categories;
	$remove_checkbox_column = !$update_products && !$remove_products && !$approve_products;
	$empty_select_block = !$add_products && !$update_products && !$products_order;
	$empty_export_block = !$products_export && !$products_import && !$products_export_google_base;
	$empty_export_approve_block = $empty_export_block && !$approve_products;
	$empty_first_category_block = !$add_categories && !$categories_order;
	$empty_second_category_block = !$categories_export && !$categories_import;

	$rp = new VA_URL("admin_items_list.php", false);
	$rp->add_parameter("category_id", REQUEST, "category_id");
	$rp->add_parameter("sc", GET, "sc");
	$rp->add_parameter("sl", GET, "sl");
	$rp->add_parameter("sa", GET, "sa");
	$rp->add_parameter("ss", GET, "ss");
	$rp->add_parameter("ap", GET, "ap");
	$rp->add_parameter("s", GET, "s");
	if ($sitelist) {
		$rp->add_parameter("param_site_id", GET, "param_site_id");		
	}	
	
	$operation = get_param("operation");
	$items_ids = get_param("items_ids");
//brian3t here delete all items that have FRAM
//$items_ids = '1450,1452,1453,10005,1454,1455,1456,1457,1458,1459,1460,1461,10001,1462,1463,1464,1465,1466,500,10002,10000,501,1467,1468,1469,1470,1471,1472,1473,1474,1475,1476,1477,1478,1479,1480,10003,1481,1482,1483,1484,1485,1486,10004,1487,1488,1489,1490,1491,10174,10352,982,10232,10353,10166,10233,10167,10168,10169,10170,317,988,10358,10329,10171,989,10359,10,215,11,216,217,218,219,318,307,220,306,314,221,12,10392,10393,10387,10354,10388,10355,10356,10396,320,10389,10328,222,223,13,224,14,327,15,10468,16,17,10357,18,19,10172,20,225,10173,10480,2022,10397,990,2021,10400,10391,10402,10320,10460,10478,10301,10302,10291,10292,10293,10294,10303,10304,10295,10305,10306,10307,10290,10296,10321,10324,10308,10309,10260,10297,10298,10299,10300,10310,10322,10286,10313,10311,10287,10312,10279,10280,10282,10281,10314,10323,10288,10289,10315,10316,10317,10318,10283,10284,10285,986,10319,10270,10271,10272,10273,10263,10261,10264,10278,10274,10275,10265,10266,10267,10268,10269,10276,10277,10262,9071,10246,9069,1357,1358,1359,1360,1361,1362,1363,1364,1365,1366,1367,10008,2030,1368,1369,1370,1371,1372,1373,1374,1375,1376,1377,1378,1379,1380,1381,1382,1383,1384,1385,1386,1387,10458,10457,10455,9075,2033,10009,9076,5960,9077,9078,9079,9080,9081,9082,9083,1388,1389,1390,1391,1392,1393,1394,1395,1396,1397,1398,1399,1400,1401,1402,1403,1404,1405,1406,1407,1408,1409,10006,1410,1411,1412,1413,1414,1415,1416,10007,9092,1417,1418,1419,991,992,993,994,10243,995,996,997,10244,998,999,1000,1001,1002,21,22,23,24,25,2013,10479,26,10475,226,227,27,228,301,229,323,230,28,339,29,231,30,31,331,232,32,233,234,235,236,33,34,237,35,321,238,36,239,240,241,242,342,37,38,243,39,305,40,310,41,42,43,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,44,10330,319,316,244,322,45,46,47,48,49,50,51,333,325,245,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,246,70,71,72,73,74,75,76,247,340,77,78,79,80,248,81,82,83,84,85,86,87,88,89,90,1003,10472,1004,10176,10177,10178,10234,10360,1005,10179,10180,1006,1007,10247,1008,1009,10473,1010,1448,9088,502,503,504,10147,10148,505,506,507,508,509,510,511,512,513,514,10149,10120,515,516,517,10121,518,519,520,521,522,523,524,525,526,527,528,10150,529,530,10122,10151,531,10123,10124,10152,532,10125,533,534,535,10126,10153,10127,10128,9089,2029,2032,10154,2027,10155,10129,10156,2026,5963,5958,9072,10130,10131,10132,10157,10158,2025,10159,10160,10133,10134,10135,10161,10476,10482,10481,11993,10024,536,10025,10026,537,538,539,540,541,542,10021,543,10051,10052,544,10053,10054,10055,10027,10056,10057,10058,10059,545,10060,5979,10061,10062,546,547,548,549,550,551,552,553,554,10028,555,10029,556,10030,10031,10032,10033,9085,641,10063,10064,10065,10066,10136,10067,10034,557,10035,10068,558,559,560,10036,561,562,563,564,565,10022,566,567,568,569,570,571,572,573,574,10037,575,576,577,578,579,580,10038,581,10069,10070,582,583,584,585,586,587,588,589,590,591,10039,592,593,594,595,596,597,598,10071,10137,599,600,601,602,603,604,605,606,607,608,609,610,611,612,613,614,615,616,617,618,619,620,621,622,623,10138,624,625,10040,626,10041,627,628,10072,2018,2019,629,631,632,633,10073,635,636,637,638,639,640,10470,642,643,644,10074,10075,645,646,647,648,649,650,651,652,653,655,656,657,658,659,660,661,662,663,664,665,666,667,668,669,670,671,672,673,674,675,676,677,678,679,680,681,682,683,684,685,686,687,688,689,690,691,692,10076,693,10077,694,695,696,10042,10078,697,698,10043,10044,10045,10046,699,700,701,702,703,10047,10048,704,705,706,10049,10050,707,708,709,710,711,712,713,714,10079,715,716,717,718,719,720,721,722,723,724,725,726,727,728,729,730,731,732,733,734,735,736,737,738,739,740,10139,741,10080,10081,742,743,744,745,746,747,748,749,750,751,752,753,754,755,756,9084,757,758,759,760,10082,761,10083,762,5978,763,764,10140,10141,10142,765,766,767,768,769,770,10084,10143,10085,771,772,773,774,775,776,777,778,779,10086,780,781,782,5971,10087,10088,783,784,785,786,10089,10090,787,788,789,790,791,792,793,794,795,796,797,798,799,800,801,802,803,804,805,806,807,808,809,10091,10092,10093,10094,10095,10096,10097,810,811,10023,812,813,814,815,816,817,818,819,820,821,822,823,824,825,826,10098,827,828,829,830,831,832,833,984,10099,834,835,836,837,838,839,840,841,842,843,844,845,846,847,848,849,850,852,853,854,855,856,857,858,10100,10101,859,860,861,862,863,864,865,10102,866,867,868,869,870,871,5961,872,873,874,10103,10104,10105,875,10106,2017,876,10107,877,878,10474,10108,879,880,881,882,883,884,10109,885,886,887,888,889,890,891,892,10110,893,894,895,896,897,898,899,900,901,902,903,904,905,906,907,908,909,910,911,912,913,914,915,916,917,918,919,920,10111,10112,10113,5974,921,10463,922,923,924,925,926,927,928,929,930,931,932,933,934,935,936,937,938,939,940,941,942,943,944,945,10114,946,947,948,949,950,10144,951,952,953,954,955,956,957,958,10115,959,10116,960,961,962,10145,963,964,965,966,2031,967,968,969,970,971,10117,972,973,974,975,976,10118,977,978,979,10119,980,10146,981,9093,9094,2034,985,983,10477,1635,1636,1637,1638,1639,1640,1641,1642,1643,1644,1645,1646,1647,1648,1649,1650,1651,1652,1653,1654,1655,1656,1657,1658,10434,1659,1660,1661,1662,1663,1664,1665,1666,1667,1668,1669,1670,1671,1672,1673,1674,1675,1676,1677,1678,1679,1680,1681,1682,10453,10454,1683,1684,1685,1686,1687,1688,1689,1690,1691,1692,1693,1694,1695,1696,1697,1698,1699,1700,1701,1702,1703,1704,1705,1706,1707,1708,1709,1710,1711,1712,1713,1714,1715,1716,1717,1718,1719,1720,1721,1722,1723,1724,1725,1726,1727,1728,1729,1730,1731,1732,1733,1734,1735,1736,1737,1738,1739,1740,1741,1742,1743,1744,1745,1746,1747,1748,1749,1750,1751,1752,1753,1754,1755,1756,1757,1758,1759,1760,1761,1762,1763,1764,1765,1766,1767,1768,1769,1770,1771,1772,1773,1774,1775,1776,1777,1778,1779,1780,1781,1782,1783,1784,1785,1786,1787,1788,1789,1790,1791,1792,1793,1794,1795,1796,1797,1798,1799,1800,1801,1802,1803,1804,1805,1806,1807,1808,1809,1810,1811,1812,1813,1814,1815,1816,1817,1818,1819,10435,10436,10437,10438,10439,10440,10441,10442,10443,10444,10445,2014,10446,10449,10447,10448,1492,1493,1494,1495,1496,1497,10416,1498,1499,1500,1501,10417,1502,1503,10418,1504,1505,10419,1506,1507,1508,1509,1510,1511,1512,1513,10420,10421,1514,1515,1516,10422,1517,1518,1519,1520,1521,1522,1523,1524,1525,1526,1527,1528,1529,1530,1531,1532,1533,1534,1535,1536,1537,1538,1539,1540,1541,10423,1542,1543,1544,1545,1546,1547,1548,1549,10424,1550,1551,1552,1553,1554,1555,1556,1557,1558,10425,1559,1560,1561,1562,10426,1563,1564,1565,1566,10427,1567,1568,10428,1569,1570,1571,1572,10429,1573,1574,1575,1576,1577,1578,1579,10430,1580,10431,1581,1582,1583,1584,1585,1586,1587,1588,1589,1590,1591,1592,1593,1594,1595,1596,1597,1598,1599,1600,1601,1602,1603,1604,1605,1606,1607,1608,1609,1610,1611,1612,1613,1614,1615,1616,1617,1618,1619,1620,1621,1622,1623,1624,1625,1626,1627,1628,1629,1630,1631,1632,1633,1634,1011,10181,1012,1013,1014,1015,1016,1017,1018,1019,1020,1021,1022,1023,1024,1025,1026,1027,1028,1029,1030,1031,1032,1033,1034,1035,1036,1037,1038,1039,1040,1041,1042,1043,1044,1045,1046,1047,1048,1049,1050,1051,1052,1053,1054,1055,1056,1057,1058,1059,1060,1061,1062,1063,1064,1065,1066,1067,1068,1069,1070,1071,1072,1073,1074,1075,1076,1077,1078,1079,1080,1081,1082,1083,1084,1085,1086,1087,1088,1089,1090,1091,1092,1093,1094,1095,1096,1097,1098,1099,1100,1101,1102,1103,1104,1105,1106,1107,1108,1109,1110,1111,1112,1113,1114,1115,1116,1117,1118,1119,1120,1121,1122,1123,1124,1125,1126,1127,1128,1129,1130,1131,1132,1133,1134,1135,1136,1137,1138,1139,1140,1141,1142,1143,1144,1145,1146,1147,1148,1149,1150,1151,1152,1153,1154,1155,1156,1157,1158,1159,1160,1161,1162,1163,1164,1165,1166,1167,1168,1169,1170,1171,1172,1173,10182,1174,1175,1176,1177,1178,1179,1180,1181,1182,1183,1184,1185,1186,1187,1188,1189,1190,1191,1192,1193,1194,1195,1196,1197,1198,10258,1199,1200,1201,1202,1203,1204,1205,1206,1207,1208,1209,1210,1211,1212,1213,1214,1215,1216,1217,1218,1219,1220,1221,1222,1223,1224,1225,1226,1227,1228,1229,1230,1231,1232,10183,1233,1234,1235,1236,1237,1238,1239,1240,1241,1242,1243,10256,10257,1244,1245,1246,1247,1248,1249,1250,1251,1252,1253,1254,1255,1256,1257,1258,1259,1292,1293,1294,1295,1296,1297,1298,1299,1300,1301,1302,1303,1304,1305,2036,1306,1307,1308,1309,249,10349,10350,10346,250,326,10331,10347,10332,10333,10334,10351,251,10235,10184,1447,10465,10185,10236,10186,10237,10238,10239,10240,10241,1260,10187,10188,5973,10361,9090,10483,10189,9087,5975,5970,5972,10394,336,10362,252,10190,10403,10404,10405,10406,10191,10410,10192,10335,10193,10363,10194,1261,5980,5981,1262,10195,10407,1263,10196,1264,1265,10411,10197,1266,1267,10412,10162,10198,10199,91,1268,10200,10201,10364,1269,1270,10202,10408,1272,1273,1274,10413,10203,2015,253,10204,10466,10205,10409,10206,2016,10208,10365,10366,10367,10414,10395,10163,10368,10209,10210,10211,2037,10369,10212,10370,10336,10213,10371,10415,1275,10216,10217,10218,2023,2020,10372,10373,10432,10375,10450,10469,10451,10337,10376,10433,10338,254,10219,334,1276,255,10220,10339,92,1277,10377,10245,93,5959,10020,10461,94,214,97,256,257,98,99,100,101,102,103,104,105,106,107,108,311,258,109,110,111,259,313,299,112,113,114,115,260,116,300,261,117,10340,118,119,262,120,263,264,10341,121,122,123,124,265,125,126,127,128,129,266,267,268,269,270,271,130,272,273,131,10342,132,10452,133,134,135,274,136,137,138,139,275,140,141,276,142,143,277,144,302,278,145,279,146,280,312,281,147,148,204,205,206,207,208,209,210,211,212,213,282,149,150,151,152,153,283,154,335,284,303,155,10459,308,156,157,285,286,287,332,288,158,159,160,289,330,329,10343,315,290,161,291,292,162,163,309,324,304,293,10325,294,164,328,10326,10344,295,165,166,167,10345,168,10327,169,337,296,170,171,172,173,297,174,175,298,10011,10012,10013,10015,10016,5977,10014,10017,10018,10010,10019,1278,10221,10242,1279,10222,1280,1281,1282,1283,1284,1285,1286,9086,10259,1449,1287,10253,10254,1288,10223,10224,10378,10225,10252,1289,10226,10379,10380,5976,10384,10386,10227,10248,10164,10255,1290,10228,10249,10229,1291,10250,10230,10251,10231,1310,1311,1312,1313,1314,1315,1316,1317,1318,1319,1320,1321,1322,1323,1324,1325,1326,1327,1328,1329,1330,1331,1332,1333,1334,1335,1336,1420,1421,1422,1423,1424,1425,1426,1427,1428,1429,1430,1431,1432,1433,1434,1435,1436,1438,1439,1440,1441,1442,1443,1444,1445,1446,5969,1337,1338,1339,1340,1341,1342,1343,1344,1345,1346,1347,1348,1349,1350,1351,2028,1352,1353,1354,1355,1356,9068';

$categories_ids = get_param("categories_ids");
	$approved_status = get_param("approved_status");
	if ($operation == "delete_items") {
		if ($remove_products && strlen($items_ids)) {
			delete_products($items_ids);
		}
	} else if ($operation == "delete_categories") {
		if ($remove_categories && strlen($categories_ids)) {
			delete_categories($categories_ids);
		}
	} else if ($operation == "update_status") {
		if ($update_products && strlen($items_ids)) {
			$sql  = " UPDATE " . $table_prefix . "items SET is_approved=" . $db->tosql($approved_status, INTEGER); 
			$sql .= " WHERE item_id IN (" . $db->tosql($items_ids, TEXT, false) . ")";
			$db->query($sql);
		}
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_items_list.html");

	//BEGIN product privileges changes
	$set_delimiter = false;
	if ($product_prices) {
		$set_delimiter = true;
	}
	if ($product_images && $set_delimiter) {
		$t->set_var("product_images_delimiter", " | ");
	} elseif ($product_images) {
		$set_delimiter = true;
	}
	if ($product_properties && $set_delimiter) {
		$t->set_var("product_properties_delimiter", " | ");
	} elseif ($product_properties) {
		$set_delimiter = true;
	}
	if ($product_features && $set_delimiter) {
		$t->set_var("product_features_delimiter", " | ");
	} elseif ($product_features) {
		$set_delimiter = true;
	}
	if ($product_related && $set_delimiter) {
		$t->set_var("product_related_delimiter", " | ");
	} elseif ($product_related) {
		$set_delimiter = true;
	}
	if ($product_categories && $set_delimiter) {
		$t->set_var("product_categories_delimiter", " | ");
	} elseif ($product_categories) {
		$set_delimiter = true;
	}
	if ($product_accessories && $set_delimiter) {
		$t->set_var("product_accessories_delimiter", " | ");
	} elseif ($product_accessories) {
		$set_delimiter = true;
	}
	if ($product_releases && $set_delimiter) {
		$t->set_var("product_releases_delimiter", " | ");
	}
	//END product privileges changes

	// set files names
	$t->set_var("admin_items_list_href",       "admin_items_list.php");
	$t->set_var("admin_layout_page_href",      "admin_layout_page.php");
	$t->set_var("admin_reviews_href",          "admin_reviews.php");
	$t->set_var("admin_category_edit_href",    "admin_category_edit.php");
	$t->set_var("admin_product_href",          "admin_product.php");
	$t->set_var("admin_properties_href",       "admin_properties.php");
	$t->set_var("admin_releases_href",         "admin_releases.php");
	$t->set_var("admin_item_related_href",     "admin_item_related.php");
	$t->set_var("admin_item_categories_href",  "admin_item_categories.php");
	$t->set_var("admin_category_items_href",  "admin_category_items.php");
	$t->set_var("admin_categories_order_href", "admin_categories_order.php");
	$t->set_var("admin_products_order_href",   "admin_products_order.php");
	$t->set_var("admin_item_types_href",       "admin_item_types.php");
	$t->set_var("admin_features_groups_href",  "admin_features_groups.php");
	$t->set_var("admin_item_prices_href",      "admin_item_prices.php");
	$t->set_var("admin_item_features_href",    "admin_item_features.php");
	$t->set_var("admin_item_images_href",      "admin_item_images.php");
	$t->set_var("admin_item_accessories_href", "admin_item_accessories.php");
	$t->set_var("admin_export_google_base_href", "admin_export_google_base.php");
	$t->set_var("admin_search_href",           "admin_search.php");
	$t->set_var("admin_tell_friend_href",      "admin_tell_friend.php");
	$t->set_var("admin_products_edit_href",  "admin_products_edit.php");
	$t->set_var("rp_url", urlencode($rp->get_url()));



	$t->set_var("admin_import_href", "admin_import.php");
	$t->set_var("admin_export_href", "admin_export.php");

	$t->set_var("approved_status", $approved_status);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$category_id = get_param("category_id");
	if (!strlen($category_id))  { $category_id = "0"; }
	// get search parameters
	$s = trim(get_param("s"));
	$sc = get_param("sc");
	$sl = get_param("sl");
	$ss = get_param("ss");
	$ap = get_param("ap");
	$param_site_id = get_param("param_site_id");
	$search = (strlen($s) || strlen($sl) || strlen($ss) || strlen($ap) || strlen($param_site_id)) ? true : false;
	if ($sc) { $category_id = $sc; }
	$sa = "";

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($category_id);

	$sql  = " SELECT full_description FROM " . $table_prefix . "categories WHERE category_id = " . $db->tosql($category_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$t->set_var("full_description", $db->f("full_description"));
	} else {
		$t->set_var("full_description", "");
	}

	$t->set_var("parent_category_id", $category_id);
	$sql  = " SELECT category_id,category_name ";
	$sql .= " FROM " . $table_prefix . "categories WHERE parent_category_id = " . $db->tosql($category_id, INTEGER);
	$sql .= " ORDER BY category_order ";
	$db->query($sql);

	// BEGIN product privileges changes
	$set_delimiter = false;
	if ($add_categories) {
		$t->parse("add_categories_priv", false);
		$set_delimiter = true;
	}
	//END product_privileges changes

	if ($db->next_record())
	{
		// BEGIN product privileges changes
		if ($categories_order) {
			if ($set_delimiter) {
				$t->set_var("categories_order_delimiter", "|");
			}
			$t->parse("categories_order_link", false);
		}
		if (!$empty_first_category_block) {
			$t->parse("categories_first_block", false);
		}
		//END product_privileges changes

		$t->set_var("no_categories", "");
		$category_index = 0;
		do {
			$category_index++;
			$row_category_id = $db->f("category_id");
			$row_category_name = $db->f("category_name");
			$row_category_name = get_translation($row_category_name, $language_code);
//delete_categories($category_id);

			$t->set_var("category_index", $category_index);
			$t->set_var("category_id", $row_category_id);
			$t->set_var("category_name", htmlspecialchars($row_category_name));
			if (!$read_only_categories) {
				if ($view_only_categories) {
					$t->set_var("category_edit_msg", VIEW_MSG);
				} else {
					$t->set_var("category_edit_msg", EDIT_MSG);
				}
				$t->parse("categories_edit_link", false);
			}
			
			if ($product_categories) {
				$t->parse("category_products_priv", false);
			} else {
				$t->set_var("category_products_priv", "");
			}

			$row_style = ($category_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);
			if ($remove_categories) {
				$t->parse("category_checkbox", false);
			} else {
				$t->set_var("category_checkbox", "");
			}

			$t->parse("categories");
		} while ($db->next_record());
		if ($remove_categories) {
			$t->parse("categories_all_checkbox", false);
			if ($add_categories || $update_categories) {
				$t->set_var("delete_categories_delimiter", "|");	
			}
			$t->parse("delete_categories_link", false);
			$t->set_var("categories_colspan", "2");
		} else {
			$t->set_var("categories_colspan", "1");
		}

		$t->set_var("categories_number", $category_index);
		$t->parse("categories_header", false);
	}
	else
	{
		$t->set_var("categories", "");
		$t->set_var("categories_order_link", "");
		$t->parse("no_categories");
	}

	// BEGIN product privileges changes
	if (!$empty_first_category_block) {
		$t->parse("categories_first_block", false);
	}
	//END product_privileges changes
	
	$group_by = "";
	
	$sorter = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_items_list.php");
	$sorter->set_parameters(false, true, true, false);
	$sorter->set_default_sorting(10, "asc");
	$sorter->set_sorter(PROD_TITLE_COLUMN, "sorter_item_name", 1, "i.item_name");
	$sorter->set_sorter(PROD_PRICE_COLUMN, "sorter_price", 2, "i.price");
	$sorter->set_sorter(PROD_QTY_COLUMN, "sorter_qty", 3, "i.stock_level");
	if ($search) {
		$sorter->set_sorter(ADMIN_ORDER_MSG, "sorter_order", 10, "i.item_order, i.item_id", "i.item_order, i.item_id", "i.item_order DESC, i.item_id");
		$group_by .= ", i.item_order";
	} else {
		$sorter->set_sorter(ADMIN_ORDER_MSG, "sorter_order", 10, "ic.item_order", "ic.item_order, i.item_order, i.item_id", "ic.item_order DESC, i.item_order, i.item_id");
		$group_by .= ", ic.item_order, i.item_order";
	}

	$where = "";
	$join  = "";
	$brackets = "";
	if ($search && $category_id != 0) {
		$brackets .= "((";
		$join  .= " LEFT JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id) ";
		$join  .= " LEFT JOIN " . $table_prefix . "categories c ON c.category_id = ic.category_id) ";
		
		$where .= " AND (ic.category_id = " . $db->tosql($category_id, INTEGER);
		$where .= " OR c.category_path LIKE '" . $db->tosql($tree->get_path($category_id), TEXT, false) . "%')";
	} elseif (!$search) {
		$brackets .= "(";
		$join  .= " LEFT JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id) ";
		$where .= " AND ic.category_id = " . $db->tosql($category_id, INTEGER);
	}
	if ($s) {
		$sa = split(" ", $s);
		for($si = 0; $si < sizeof($sa); $si++) {
			$sa[$si] = str_replace("%","\%",$sa[$si]);
			$where .= " AND (i.item_name LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
			$where .= " OR i.item_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%' ";
			if (sizeof($sa) == 1 && preg_match("/^\d+$/", $sa[0])) {
				$where .= " OR i.item_id =" . $db->tosql($sa[0], INTEGER);
			}
			$where .= " OR i.manufacturer_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%')";
		}
	}
	if (strlen($sl)) {
		if ($sl == 1) {
			$where .= " AND (i.stock_level>0 OR i.stock_level IS NULL) ";
		} else {
			$where .= " AND i.stock_level<1 ";
		}
	}
	if (strlen($ss)) {
		if ($ss == 1) {
			$where .= " AND i.is_showing=1 ";
		} else {
			$where .= " AND i.is_showing=0 ";
		}
		$group_by .= ", i.is_showing";
	}
	if (strlen($ap)) {
		if ($ap == 1) {
			$where .= " AND i.is_approved=1 ";
		} else {
			$where .= " AND i.is_approved=0 ";
		}
		$group_by .= ", i.is_approved";
	}
	if (strlen($param_site_id)) {
		if ($param_site_id == "all") {
			$where .= " AND i.sites_all=1 ";
		} else {
			$brackets .= "(";
			$join  .= " LEFT JOIN " . $table_prefix . "items_sites s ON (s.item_id = i.item_id AND i.sites_all = 0 )) ";
			$where .= " AND (s.site_id=" . $db->tosql($param_site_id, INTEGER) . " OR i.sites_all=1) ";
		}
		$group_by .= ", i.sites_all";
	}

	
	$total_records = 0;
	if (strtolower($db_type) == "mysql" || !strlen($join)) {
		$sql  = " SELECT COUNT(DISTINCT i.item_id) ";
	} else {
		$sql  = " SELECT COUNT(*) ";
	}
	$sql .= " FROM " . $brackets . $table_prefix . "items i " . $join;
	$sql .= " WHERE 1=1 ";
	$sql .= $where;
	$total_records = 0;
	if (strtolower($db_type) == "mysql" || !strlen($join)) {
		$db->query($sql);
		$db->next_record();
		$total_records = $db->f(0);
	} else {
		$sql .= " GROUP BY i.item_id";
		$db->query($sql);
		while ($db->next_record()) {
			$total_records++;
		}
	}

	// set up variables for navigator
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_items_list.php");
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", MOVING, $pages_number, $records_per_page, $total_records, false);

	
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;              

	$item_index = 0;

	// 'Add new product' link
	$set_delimiter = false;
	if ($add_products) {
		$t->parse("add_products_priv", false);
		$set_delimiter = true;
	}

	if ($total_records > 0) {
		$sql  = " SELECT i.item_id, i.item_code, i.manufacturer_code, i.item_name, i.price, i.sales_price, i.is_sales, i.stock_level ";
		$sql .= " FROM " . $brackets . $table_prefix . "items i " . $join;
		$sql .= " WHERE 1=1 ";
		$sql .= $where;
		$sql .= " GROUP BY i.item_id, i.item_code, i.manufacturer_code, i.item_name, i.price, i.sales_price, i.is_sales, i.stock_level ";
		$sql .= $group_by;
	
		$sql .= $sorter->order_by;
		$db->query($sql);
		if ($db->next_record())
		{
			//BEGIN product privileges changes
			if ($update_products) {
				if ($set_delimiter) {
					$t->set_var("edit_items_delimiter", " | ");
				}
				$t->parse("edit_items_link", false);
				$set_delimiter = true;
			}
			if ($remove_products) {
				if ($set_delimiter) {
					$t->set_var("delete_items_delimiter", " | ");
				}
				$t->parse("delete_items_link", false);
				$set_delimiter = true;
			}
			if ($products_order) {
				if ($set_delimiter) {
					$t->set_var("products_order_delimiter", " | ");
				}
				$t->parse("products_order_link", false);
			}
			//END product privileges changes
			$t->set_var("category_id", $category_id);
			$t->set_var("no_items", "");
			do {
				$item_index++;
				$item_id = $db->f("item_id");
				$product_category_id = $db->f("category_id");
				$item_code = $db->f("item_code");
				$manufacturer_code = $db->f("manufacturer_code");
				$item_name = get_translation($db->f("item_name"));
				$price = $db->f("price");
				$is_sales = $db->f("is_sales");
				$sales_price = $db->f("sales_price");
				$stock_level = $db->f("stock_level");
				$item_codes = "";
				if ($item_code && $manufacturer_code) {
					$item_codes = "(" . $item_code . ", " . $manufacturer_code . ")";
				} elseif ($item_code) {
					$item_codes = "(" . $item_code . ")";
				} elseif ($manufacturer_code) {
					$item_codes = "(" . $manufacturer_code . ")";
				}

				$price = calculate_price($price, $is_sales, $sales_price);

				$t->set_var("item_id", $item_id);
				$t->set_var("item_index", $item_index);
				$t->set_var("product_category_id", $product_category_id);
				$t->set_var("item_code", htmlspecialchars($item_code));
				$t->set_var("manufacturer_code", htmlspecialchars($manufacturer_code));
				$t->set_var("item_codes", htmlspecialchars($item_codes));

				$item_name = htmlspecialchars($item_name);
				if (is_array($sa)) {
					for ($si = 0; $si < sizeof($sa); $si++) {
						$regexp = "";
						for ($si = 0; $si < sizeof($sa); $si++) {
							if (strlen($regexp)) $regexp .= "|";
							$regexp .= htmlspecialchars(str_replace(
								array( "/", "|",  "$", "^", "?", ".", "{", "}", "[", "]", "(", ")", "*"),
								array("\/","\|","\\$","\^","\?","\.","\{","\}","\[","\]","\(","\)","\*"),$sa[$si]));
						}
						if (strlen($regexp))
						{
							$item_name = preg_replace ("/(" . $regexp . ")/i", "<font color=\"blue\">\\1</font>", $item_name);
						}
					}
				}
				$t->set_var("item_name", $item_name);
				$t->set_var("price", currency_format($price));
				if ($stock_level < 0) {
					$stock_level = "<font color=red>" . $stock_level . "</font>";
				}
				$t->set_var("stock_level", $stock_level);

				// BEGIN product privileges changes
				if ($product_prices) {
					$t->parse("product_prices_priv", false);
				} else {
					$t->set_var("product_prices_priv", "");
				}
				if ($product_images) {
					$t->parse("product_images_priv", false);
				} else {
					$t->set_var("product_images_priv", "");
				}
				if ($product_properties) {
					$t->parse("product_properties_priv", false);
				} else {
					$t->set_var("product_properties_priv", "");
				}
				if ($product_features) {
					$t->parse("product_features_priv", false);
				} else {
					$t->set_var("product_features_priv", "");
				}
				if ($product_related) {
					$t->parse("product_related_priv", false);
				} else {
					$t->set_var("product_related_priv", "");
				}
				if ($product_categories) {
					$t->parse("product_categories_priv", false);
				} else {
					$t->set_var("product_categories_priv", "");
				}
				if ($product_accessories) {
					$t->parse("product_accessories_priv", false);
				} else {
					$t->set_var("product_accessories_priv", "");
				}
				if ($product_releases) {
					$t->parse("product_releases_priv", false);
				} else {
					$t->set_var("product_releases_priv", "");
				}
				if ($read_only_products) {
					$t->parse("read_only_products_priv", false);
					$t->set_var("update_products_priv", "");
				} elseif ($view_only_products) {
					$t->set_var("product_edit_msg", VIEW_MSG);
					$t->parse("update_products_priv", false);
					$t->set_var("read_only_products_priv", "");
				} else {
					$t->set_var("product_edit_msg", EDIT_MSG);
					$t->parse("update_products_priv", false);
					$t->set_var("read_only_products_priv", "");
				}
				if (!$remove_checkbox_column) {
					$t->parse("checkbox_list_priv", false);
				}
				
				$row_style = ($item_index % 2 == 0) ? "row1" : "row2";
				$t->set_var("row_style", $row_style);
				// END product privileges changes
				$t->parse("items_list");
			} while ($db->next_record());
			if (!$remove_checkbox_column) {
				$t->parse("checkbox_header_priv", false);
			}
			$t->parse("items_header", false);
		}
	}

	if ($item_index < 1) {
		$t->set_var("delete_items_link", "");
		$t->set_var("products_order_link", "");
		$t->set_var("items_list", "");
		$t->parse("no_items");
	}

	if ($total_records > 0) {
		$admin_google_base_filtered_url = new VA_URL("admin_export_google_base.php", false);
		if ($search) {
			$admin_google_base_filtered_url->add_parameter("sc", GET, "sc");
		} else {
			$admin_google_base_filtered_url->add_parameter("sc", CONSTANT, $category_id);
		}
		$admin_google_base_filtered_url->add_parameter("sl", GET, "sl");
		$admin_google_base_filtered_url->add_parameter("sa", GET, "sa");
		$admin_google_base_filtered_url->add_parameter("ss", GET, "ss");
		$admin_google_base_filtered_url->add_parameter("ap", GET, "ap");
		$admin_google_base_filtered_url->add_parameter("s", GET, "s");
		$admin_google_base_filtered_url->add_parameter("param_site_id", GET, "param_site_id");		

		$t->set_var("admin_google_base_filtered_url", $admin_google_base_filtered_url->get_url());
		$t->set_var("total_filtered", $total_records);
		$t->parse("google_base_filtered", false);
		
		$admin_export_filtered_url = new VA_URL("admin_export.php", true);
		$admin_export_filtered_url->add_parameter("table", CONSTANT, "items");
		if (!strlen(get_param("category_id")))
			$admin_export_filtered_url->add_parameter("category_id", CONSTANT, $category_id);

		$t->set_var("admin_export_filtered_url", $admin_export_filtered_url->get_url());
		$t->set_var("total_filtered", $total_records);
		$t->parse("export_filtered", false);	
  
		if ($approve_products) {
			if (!$empty_export_block) {
				$t->set_var("update_status_br", "<br><br>");
			}
			$approved_options = array(array("", ""), array("1", IS_APPROVED_MSG), array("0", NOT_APPROVED_MSG));
			for ($i = 0; $i < sizeof($approved_options); $i++) {
				if ($approved_options[$i][0] == $approved_status) {
					$t->set_var("status_id_selected", "selected");
				} else {
					$t->set_var("status_id_selected", "");
				}
				$t->set_var("status_id_value", $approved_options[$i][0]);
				$t->set_var("status_id_description", $approved_options[$i][1]);
				$t->parse("status_id", true);
			}
			$t->parse("update_status", false);
		}
	}

	// BEGIN product privileges changes
	$set_delimiter = false;
	if ($products_export) {
		$t->parse("products_export_priv", false);
		$set_delimiter = true;
	}
	if ($products_import) {
		if ($set_delimiter) {
			$t->set_var("products_import_delimiter", " | ");
		}
		$t->parse("products_import_priv", false);
	}
	if ($products_export_google_base) {
		if ($set_delimiter) {
			$t->set_var("products_export_google_base_delimiter", " | ");
		}
		$t->parse("products_export_google_base_priv", false);
	}
	// END product privileges changes


	// set up search form parameters
	$stock_levels =
		array(
			array("", ""), array(0, OUTOFSTOCK_PRODUCTS_MSG), array(1, INSTOCK_PRODUCTS_MSG)
		);
	$sales =
		array(
			array("", ""), array(0, NOT_FOR_SALES_MSG), array(1, FOR_SALES_MSG)
		);
	$aproved_values =
		array(
			array("", ""), array(0, NO_MSG), array(1, YES_MSG)
		);

	set_options($stock_levels, $sl, "sl");
	set_options($sales, $ss, "ss");
	set_options($aproved_values, $ap, "ap");
	$values_before[] = array("", SEARCH_IN_ALL_MSG);
	if ($category_id != 0) {
		$values_before[] = array($category_id, SEARCH_IN_CURRENT_MSG);
	}
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ", 
			array(array("", ""), array("all",  SITES_ALL_MSG) ));
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist");
	}

	$sql  = " SELECT category_id,category_name ";
	$sql .= " FROM " . $table_prefix . "categories WHERE parent_category_id = " . $db->tosql($category_id, INTEGER);
	$sql .= " ORDER BY category_order ";
	$sc_values = get_db_values($sql, $values_before);
	set_options($sc_values, $sc, "sc");
	$t->set_var("s", $s);
	if ($search) {
		$t->parse("s_d", false);
	}

	$hidden_params["s"] = get_param("s");
	$hidden_params["sl"] = get_param("sl");
	$hidden_params["sc"] = get_param("sc");
	$hidden_params["sort_ord"] = get_param("sort_ord");
	$hidden_params["sort_dir"] = get_param("sort_dir");
	get_query_string($hidden_params, "", "", true);

	if (!$empty_select_block) {
		$t->parse("products_select_block_priv", false);
	}
	if (!$empty_export_approve_block) {
		$t->parse("products_export_block_priv", false);
	}

	$set_delimiter = false;
	if ($categories_export) {
		$t->parse("categories_export_priv", false);
		$set_delimiter = true;
	}
	if ($categories_import) {
		if ($set_delimiter) {
			$t->set_var("categories_import_delimiter", " | ");
		}
		$t->parse("categories_import_priv", false);
	}
	if (!$empty_second_category_block) {
		$t->parse("categories_second_block", false);
	}

	$t->set_var("items_number", $item_index);
	$t->parse("items_block", false);

	$t->pparse("main");

?>