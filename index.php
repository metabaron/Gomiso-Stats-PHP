<?php
define('GOMISO_KEY', 'XXXXXXXXXXX');
define('GOMISO_SECRET', 'XXXXXXXXXXX');

define('request_token', 'http://www.gomiso.com/oauth/request_token');
define('access_token', 'http://www.gomiso.com/oauth/access_token');
define('authorize', 'http://www.gomiso.com/oauth/authorize');


//If we have cookie, it's ok to display informations
//If no cookie, redirect user to the authentification system
if(isset($_COOKIE['Gomiso-Metabaron'])){
  header('Location: http://www.metabaron.net/gomiso/display.php');
} else{
  try{
    $oauth = new OAuth(GOMISO_KEY, GOMISO_SECRET, OAUTH_SIG_METHOD_HMACSHA1);
    //$oauth->enableDebug();
    $request_token_info = $oauth->getRequestToken(request_token, 'http://www.metabaron.net/gomiso/callback.php');
    setcookie("Gomiso-Metabaron[oauth_token]", $request_token_info['oauth_token']);
    setcookie("Gomiso-Metabaron[oauth_token_secret]", $request_token_info['oauth_token_secret']);
    
    header('Location: ' . authorize. '?oauth_token=' . urlencode($request_token_info['oauth_token']));
    exit();
  } catch(OAuthException $E) {
    print_r($E);
  }
}