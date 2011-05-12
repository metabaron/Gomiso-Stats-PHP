<?php
define('GOMISO_KEY', 'XXXXXXXXXXX');
define('GOMISO_SECRET', 'XXXXXXXXXXX');

define('request_token', 'http://www.gomiso.com/oauth/request_token');
define('access_token', 'http://www.gomiso.com/oauth/access_token');
define('authorize', 'http://www.gomiso.com/oauth/authorize');


//If we have a cookie, we are ok
//If not, redirect to root page
if(isset($_COOKIE['Gomiso-Metabaron'])){
  try{
    $oauth = new OAuth(GOMISO_KEY, GOMISO_SECRET, OAUTH_SIG_METHOD_HMACSHA1);
    $oauth->enableDebug();
    
    $oauth->setToken($_COOKIE['Gomiso-Metabaron']['oauth_token'], $_COOKIE['Gomiso-Metabaron']['oauth_token_secret']);
    $access_token_info = $oauth->getAccessToken(access_token);
    
    setcookie("Gomiso-Metabaron[access_token]", $access_token_info["oauth_token"]);
    setcookie("Gomiso-Metabaron[access_token_secret]", $access_token_info["oauth_token_secret"]);
    
    print "In order to make it easy to use, I'm not including database access<br>It makes the application slower and the code is harder to understand but that's a proof of concept that should be included in Gomiso, not something external<br>I coded this in one night and waiting for the Google IO presentation here in Singapore (midnight)...<br>";
    print "Everything is done in memory without database (much easy to test for all of you guys) and I call TVDB APIs for each episode so the next page can take about 20-25 secondes to appear on screen. THIS IS A NORMAL BEHAVIOR GIVING THE CURRENT ARCHITECTURE \"CONSTRAINTS\"";
    print "<a href=display.php>Continue</a>";
  } catch(OAuthException $E) {
    print_r($E);
  }
} else{
  header('Location: http://www.metabaron.net/gomiso/');
}
?>