<?PHP
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	
	echo count($_FILES);
	foreach($_FILES as $k => $v) {
		echo "$k => $v";
	}
	
	phpinfo();
	
	//$fileName = time() . $_FILES["upload"]["name"][0];
	//move_uploaded_file($_FILES["upload"]["tmp_name"],  "./Uploads/" . $fname);
	
	//echo "$fileName";
?>