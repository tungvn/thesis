<?php  
/*
All scripts of users.
*/
include_once('databases.php');

// function to escape data and strip tags
function safestrip($string){
	$string = strip_tags($string);
	$string = pg_escape_string($string);
	return $string;
}

//function to show any messages
function messages() {
	$message = '';
	if($_SESSION['success'] != '') {
		$message = '<span class="success" id="message">'.$_SESSION['success'].'</span>';
		$_SESSION['success'] = '';
	}
	if($_SESSION['error'] != '') {
		$message = '<span class="error" id="message">'.$_SESSION['error'].'</span>';
		$_SESSION['error'] = '';
	}
	return $message;
}

// log user in function
function login($username, $password, $redirect_to) {

	$link = connectDB('localhost', 'fimo', 'postgres', '123456');

	//call safestrip function
	$user = safestrip($username);
	$pass = safestrip($password);

	//convert password to md5
	$pass = md5('vnt' . $pass);

	// redirect link
	$redirect = urldecode($redirect_to);

	// check if the user id and password combination exist in database
	$rows = pg_query($link, "SELECT * FROM users WHERE username = '$user' AND password = '$pass'") or die(pg_result_error());

	//if match is equal to 1 there is a match
	if (pg_num_rows($rows) == 1) {
		$row = pg_fetch_array($rows);
		//set session
		$_SESSION['authorized'] = true;
		// is_role user
		switch (trim($row['role'])) {
			case 'administrator': $_SESSION['is_admin'] = true; break;
			case 'moderator': $_SESSION['is_moder'] = true; break;
			case 'editor': $_SESSION['is_editor'] = true; break;
			case 'publisher': $_SESSION['is_publisher'] = true; break;
			case 'subscriber': $_SESSION['is_subscriber'] = true; break;
		}

		// current user
		$_SESSION["user_id"] = 'user-' . $row['id'];

		// reload the page
		header('Location: ' . $redirect);
		exit;
	} 
	else {
		// login failed save error to a session
		$_SESSION['login_error'] = 'Sorry, wrong username or password!';
		header('Location: ' . $redirect);
		exit;
	}
	closeDB($link);
}

function logout() {
	session_start();
	session_destroy();
	header('Location: index.php');
	exit;
}

/* Register user */
function register($username, $password, $email) {
	//default is subscriber
	$role = 'subscriber';

	//call safestrip function
	$user = safestrip($username);
	$pass = safestrip($password);
	$email = safestrip($email);

	//convert password to md5
	$pass = md5('vnt' . $pass);

	// insert user to database
	$args = array(
		'username' => $user,
		'email' => $email,
		'password' => $pass
	);
	$result = insertRecords('fimo', 'users', $args);

	return $result;
}

/* Update users */
function update($new_info) {
	
}


?>