<!Doctype html>
<html>
<head>

<title> Facebook App </title>

<!--materialize.css-->

	<link type="text/css" rel="stylesheet" href="css/materialize.css"  media="screen,projection"/>

<style type="text/css">
			html, body {
				height:100%;
				margin:0;
			}
			body {
				background-image: url("b12.jpg");
				background-repeat: repeat-x;
			}
			
</style>
	
<body>

<div class="container"  style="padding:30px 0 450px 0">

<?php
echo"<h3>ABOUT YOU</h3>";
session_start();
require_once __DIR__ . '/src/Facebook/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '',// Your app id
  'app_secret' => '', //your app secret
  'default_graph_version' => 'v2.9',
  ]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email','user_birthday','user_friends']; 

	
try {
	if (isset($_SESSION['facebook_access_token'])) {
		$accessToken = $_SESSION['facebook_access_token'];
	} else {
  		$accessToken = $helper->getAccessToken();
	}
} catch(Facebook\Exceptions\FacebookResponseException $e) {
 	// When Graph returns an error
 	echo 'Graph returned an error: ' . $e->getMessage();

  	exit;
	
} catch(Facebook\Exceptions\FacebookSDKException $e) {
 	// When validation fails or other local issues
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
  	exit;
 }

if (isset($accessToken)) {
	if (isset($_SESSION['facebook_access_token'])) {
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	} else {
		// getting short-lived access token
		$_SESSION['facebook_access_token'] = (string) $accessToken;

	  	// OAuth 2.0 client handler
		$oAuth2Client = $fb->getOAuth2Client();

		// Exchanges a short-lived access token for a long-lived one
		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);

		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;

		// setting default access token to be used in script
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}

	// redirect the user back to the same page if it has "code" GET variable
	if (isset($_GET['code'])) {
		header('Location: ./');
	}

	// getting basic info about user
	try {
		$profile_request = $fb->get('/me?fields=name,first_name,last_name,email,gender,birthday');		
		$profile = $profile_request->getGraphNode()->asArray();		
		
		$me = $profile_request->getGraphUser();
		
				
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		session_destroy();
		// redirecting user back to app login page
		header("Location: ./");
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	
	// getting profile picture of the user
	try {
		$requestPicture = $fb->get('/me/picture?redirect=false&height=300'); //getting user picture
		$requestProfile = $fb->get('/me'); // getting basic info
		$picture = $requestPicture->getGraphUser();
		$profile = $requestProfile->getGraphUser();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	
	$name = $me['name'];
	$email = $me['email'];
	$gender = $me['gender'];
	
	echo "<img src='".$picture['url']."'/>";
	echo "<br>";
	echo "<br>";
	echo 'Name : ' . $name ;
	echo "<br>";
	echo 'Email : ' . $email;
	echo "<br>";
	echo 'Gender : ' . $gender;
	echo "<br>";
	
	
	  	// Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
} 
	
	else {
	// replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
	
	$loginUrl = $helper->getLoginUrl('http://localhost/apps/Fblogin/index.php', $permissions);
	echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
	} 
	echo "<br>";
	echo'<a href="logout.php">Logout</a>';
?>
</div>
</body>	
</head>
</html>


