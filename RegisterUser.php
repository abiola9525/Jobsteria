<?PHP
	require_once "QueryUtility.php";
	require_once "User.php";
	
	$name = $_POST["canonicalName"];
	$username = $_POST["userName"];
	$password1 = $_POST["password1"];
	$password2 = $_POST["password2"];
	$email = $_POST["email"];
	$zip = $_POST["zip"];
	$phone = $_POST["phone"];
	$workPhone = $_POST["workPhone"];
	$birthdate = $_POST["birthdate"];
	$country = $_POST["country"];
	$stateProv = $_POST["state_prov"];
	$city = $_POST["city"];
	$skype = $_POST["skype"];
	$aim = $_POST["aim"];
	$address = $_POST["address"];
	$photoName = null;
	$resumeName = null;
	
	if($name == null || $username == null || $password1 == null || $password2 == null ||
		$email == null || $zip == null || $phone == null || $birthdate == null || $country == null ||
		$stateProv == null || $city == null) {
		header("http://localhost/MyFreeLancer/Register.php");
	}
	
	if(strcmp($password1, $password2) != 0) {
		header("http://localhost/MyFreeLancer/Register.php");
	}
	
	if( array_key_exists("photo", $_FILES)) {
		$photoName = time() . $_FILES["photo"]["name"];
		move_uploaded_file($_FILES["photo"]["tmp_name"],  "./imgs/photos/" . $photoName);
	}
	
	if( array_key_exists("resume", $_FILES)) {
		$resumeName = time() . $_FILES["resume"]["name"];
		move_uploaded_file($_FILES["resume"]["tmp_name"], "./uploads/resumes/" . $resumeName);
	}
	
	
	$user = new User(null, $name, $username, md5($password1), null, null,$address,
									$birthdate, $zip, null, $email, $stateProv, $city, $country, 
									$photoName, $phone,$workPhone,$skype, $aim, $resumeName);
									
	QueryUtility::insertNewUser($user);
	header("http://localhost/MyFreeLancer/index.php");
?>