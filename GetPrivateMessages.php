<?PHP
	require_once 'QueryUtility.php';
	require_once 'User.php';
	
	session_start();
	
	
	if(!isset($_SESSION["uid"])) {
		die("User Not Logged In");
	}
	
	$uid = $_SESSION["uid"];
	
	
	$out = "";
	
	$pms = QueryUtility::getInBoxPrivateMessagesByUid($uid);
	$len = count($pms);
	
	$out .= "<table>\n";
	$out .= "<tr><th>Sender</th><th>Subject</th><th>Date</th></tr>\n";
	
	$count = 0;
	
	for($i = 0; $i < $len; $i++) {
		$pm = $pms[$i];
		$sender = QueryUtility::getUserByUid($pm->getFromUid());
		$out .= "<tr><td>" . $sender->getUsername() . "</td><td><b><a href=\"javascript:readMessage($count)\">" . $pm->getSubject() . "<a/></b></td><td>" .  $pm->getDate() .
		"</td><td><input id=\"$count\"type=\"checkbox\"><input id=\"message$count\" type=\"hidden\" value=\"" . $pm->getMessage() . "\"></td></tr>";
		$count++;
	}
	
	$out .= "</table>";
	
	echo $out;
?>