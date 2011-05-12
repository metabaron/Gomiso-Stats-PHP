<?php
define('GOMISO_KEY', 'XXXXXXXXXXX');
define('GOMISO_SECRET', 'XXXXXXXXXXX');
define('TVDB_KEY', 'XXXXXXXXXXX');

//If we have a cookie, we are ok
//If not, redirect to root page
if(isset($_COOKIE['Gomiso-Metabaron'])){
  //I'm using arrays because I don't want to use a database but would be much much better for memory usage and for sorting later on
  $movies = array();
  $series = array();
  $genres = array();
  
  try{
    $oauth = new OAuth(GOMISO_KEY, GOMISO_SECRET, OAUTH_SIG_METHOD_HMACSHA1);
    
    $access_token_info["oauth_token"] = $_COOKIE['Gomiso-Metabaron']['access_token'];
    $access_token_info["oauth_token_secret"] = $_COOKIE['Gomiso-Metabaron']['access_token_secret'];
    
    $oauth->setToken($access_token_info["oauth_token"], $access_token_info["oauth_token_secret"]);
    $oauth->fetch('http://gomiso.com/api/oauth/v1/users/show.json', '', OAUTH_HTTP_METHOD_GET);
    $json_user = json_decode($oauth->getLastResponse());
    
    $user = (array)$json_user->{'user'};
    $name = $user['full_name'];
    $id = $user['id'];
    print "Hello to you: " . $name . "<br>";
    
    $min_id = 0;
    //Let's build the movie and series lists
    $parameters['user_id'] = $id;
    $parameters['count'] = 50;
    $oauth->fetch('http://gomiso.com/api/oauth/v1/feeds/home.json', $parameters, OAUTH_HTTP_METHOD_GET);
    $json_feeds = json_decode($oauth->getLastResponse());
    
    foreach($json_feeds as $json_feed){
      $feed = (array)$json_feed;
      $entry = (array)$feed['feed_item'];
      
      $entry_id =$entry['id'];
      //First run
      if($min_id == 0){
	$min_id = $entry_id;
      }
      //Find the lowest ID, as we cannot be sure the json order is from higest to lowest id
      if($min_id > $entry_id){
	$min_id = $entry_id;
      }
      if($entry['type'] == "checkin"){
	$user_entry = (array)$entry['user'];
	if($user_entry['id'] == $id){
	  $topics = (array)$entry['topics'];
	  $media = (array)$topics['media'];
	  if($media['kind'] == "Movie"){
	    $push_item = array('title' => $media['title'], 'poster' => $media['poster_image_url'], 'id' => $media['id']);
	    //Let's add it in a array so that in the futur you can play with the array for nice display
	    if(array_search($push_item, $movies) !== FALSE){
	      //Display something like: you like this movie?
	    }else{
	      array_push($movies, $push_item);
	    }
	  }
	  if($media['kind'] == "TvShow"){
	    $episode = (array)$topics['episode'];
	    $push_item = array('title' => $media['title'], 'poster' => $media['poster_image_url'], 'id' => $episode['media_id'], 'season_num' => $episode['season_num'], 'episode_num' => $episode['episode_num'], 'episode_label' => $episode['label'], 'tvdb_id' => $media['tvdb_id']);
	    //Let's add it in a array so that in the futur you can play with the array for nice display
	    if(array_search($push_item, $series) === FALSE){
	      array_push($series, $push_item);
	    }else{
	      //Display message like: you like this episode?
	    }
	  }
	}else{
	  //print "Entry for user id: " . $user_entry['id'] . " (not you) so ignored.<br><br>";
	}
      }else{
	//print "Entry: " . $entry['type'] . " currently ignored.<br><br>";
      }
    }
    
    //Run another search starting from lowest feed ID so that we can get 100 items
    $parameters['user_id'] = $id;
    $parameters['count'] = 50;
    $parameters['max_id'] = $min_id;
    $oauth->fetch('http://gomiso.com/api/oauth/v1/feeds/home.json', $parameters, OAUTH_HTTP_METHOD_GET);
    $json_feeds = json_decode($oauth->getLastResponse());
    
    //This is ugly to do so but I don't have database and cannot 'isolate' smallest feed ID before parsing it fully
    //Should be in a function but I do not have time to make it clean
    foreach($json_feeds as $json_feed){
      $feed = (array)$json_feed;
      $entry = (array)$feed['feed_item'];
      
      if($entry['type'] == "checkin"){
	$user_entry = (array)$entry['user'];
	if($user_entry['id'] == $id){
	  $topics = (array)$entry['topics'];
	  $media = (array)$topics['media'];
	  if($media['kind'] == "Movie"){
	    $push_item = array('title' => $media['title'], 'poster' => $media['poster_image_url'], 'id' => $media['id']);
	    //Let's add it in a array so that in the futur you can play with the array for nice display                            
	    if(array_search($push_item, $movies) !== FALSE){
	      //Display something like: you like this movie?                                                                       
	    }else{
	      array_push($movies, $push_item);
	    }
	  }
	  if($media['kind'] == "TvShow"){
	    $episode = (array)$topics['episode'];
	    $push_item = array('title' => $media['title'], 'poster' => $media['poster_image_url'], 'id' => $episode['media_id'], 'season_num' => $episode['season_num'], 'episode_num' => $episode['episode_num'], 'episode_label' => $episode['label'], 'tvdb_id' => $media['tvdb_id']);
	    //Let's add it in a array so that in the futur you can play with the array for nice display                            
	    if(array_search($push_item, $series) === FALSE){
	      array_push($series, $push_item);
	    }else{
	      //Display message like: you like this episode?                                                                       
	    }
	  }
	}else{
	  //print "Entry for user id: " . $user_entry['id'] . " (not you) so ignored.<br><br>";                                    
	}
      }else{
	//print "Entry: " . $entry['type'] . " currently ignored.<br><br>";                                                        
      }
    }  
    rsort($movies);
    rsort($series);
    //Let's display results
    print "<table border=1>";
    print "<tr><td colspan=3>Movies</td></tr>";
    foreach($movies as $movie){
      print "<tr><td>" . $movie['title'] . "</td><td><img src=" . $movie['poster'] . "/></td></tr>";
    }
    print "</table><br>";
    
    print "I'm using <a href=http://thetvdb.com>The TVDB</a> for serie's type so please help them have more accurate information about each serie you are watching<br><br>";
    //Not nice but will solve the problem of not using a database...
    $series_episodes = array();
    foreach($series as $serie){
      //Already ordered so we know this will be the last episode watched by the user
      if(!isset($series_episodes[$serie['title']])){
	//Let's find the serie genre using tvdb
	//I do it here and not before because I want to query tvdb only once, when new serie is add
	//Not when new episode is add
	$url = 'http://www.thetvdb.com/' . TVDB_KEY . '/series/' . $serie['tvdb_id'] . "/";
	//Should add a checking or download the whole TVDB database (possible and much better idea for production
	$xml = simplexml_load_file($url);
	//Get rid of "|" first and last position of the string
	$genresTVBD = substr($xml->Series[0]->Genre, 1, strlen($xml->Series[0]->Genre) - 2);
	$serieGenres = explode('|', $genresTVBD);
	$episodeGenres = 'Genre(s): ';
	foreach($serieGenres as $genre){
	  if(isset($genres[$genre])){
	    $genres[$genre]++;
	  }
	  else{
	    $genres[$genre] = 1;
	  }
	  $episodeGenres .= $genre . ", ";
	}
	//Delete last ",". Not clean but works
	$episodeGenres = substr($episodeGenres, 0 , strlen($episodeGenres) - 2);
        $series_episodes[$serie['title']] = array($serie['episode_label'], $serie['poster'], $episodeGenres);
      }else{
	$temp = substr($series_episodes[$serie['title']][2], 10, strlen($series_episodes[$serie['title']][2]));
	$serieGenres = explode(', ', $temp);
	foreach($serieGenres as $genre){
	  if(isset($genres[$genre])){
            $genres[$genre]++;
          }
          else{
            $genres[$genre] = 1;
          }

	  $temp = $series_episodes[$serie['title']];
	  $episode_list = $temp[0];
	  $episode_list = $episode_list. ", " .$serie['episode_label'];
	  $series_episodes[$serie['title']] = array($episode_list, $serie['poster'], $series_episodes[$serie['title']][2]);
	}
      }
    }
    arsort($genres);
    print "<br><table border=1>";
    print "<tr><td colspan=2>Series genre (one show can have several genres)</td></tr>";
    while($serieGenre = current($genres)){
      print "<tr><td>Genre: " . key($genres) . "</td><td>Seen " . $serieGenre . " times</td></tr>";
      next($genres);
    }
    print "</table>";

    print "<br><table border=1>";
    print "<tr><td colspan=4>Series</td></tr>";
    while($serie_all = current($series_episodes)){
      print "<tr><td>" . key($series_episodes) . "</td><td>" . $serie_all[0] . "</td><td>" . $serie_all[2]. "</td><td><img src=" . $serie_all[1] . "/></td></tr>";
      next($series_episodes);
    }
    print "</table>";
  } catch(OAuthException $E) {
    print_r($E);
  }
} else{
  header('Location: http://www.metabaron.net/gomiso/');
}
?>