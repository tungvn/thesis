<?php  
require_once('includes/Encoding.php'); 
use \ForceUTF8\Encoding;

$link = pg_connect("host='localhost' port='5432' user='postgres' password='123456' dbname='BacKan'") or die("Connect failed");
	
if (!$link) {
	echo ("Connection failed");
}
else {
	//pg_close($link);
}

/* Search Tool AjaxJquery - PHP - PostgreSQL */
//$data = $_POST['keyword'];
/*mb_internal_encoding('UTF-8');
$str = 'x· Nh¹n M«n';
$str1 = 'FÃ©dÃ©ration';
echo mb_detect_encoding(Encoding::fixUTF8($str));
echo Encoding::fixUTF8(Encoding::toUTF8($str));
echo Encoding::fixUTF8(Encoding::toUTF8($str1));*/
$sql  = "select fme_text_s, convert_to(fme_text_s, 'UTF8') as convert from textxa";
// where varname_2 like '%". $data ."%'"
$rows = pg_query($link, $sql);
if (!$rows)
{
	return 'Không có kết quả tương ứng!';
}
if(pg_num_rows($rows) > 0) {

	while ($row = pg_fetch_array($rows)) {
		print_r($row);
		echo '<li><a href="javascript:void(0);">' . ($row['fme_text_s']) . ' - ' . mb_detect_encoding($row['convert']) . '</a></li>';
	}
}

?>