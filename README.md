Gomiso Python
=============

PHP code interacting with [Gomiso](http://www.gomiso.com/) through oAuth to extract users information
Such as movies watched, series, etc.

Purpose
-------
Coded using [php](http://www.php.net/), this is a try to provide users with informations regarding
their profile and the kind of series and movies they watched.
This is calling the last 100 events from the user so it's not fully accurate.
It's kind of a proof of concept of good things that you can do in one night with Gomiso API, a try to
win the iPad 2 ;) and a way to show that if you use this code to query Gomiso databases, you can
display interesting things to the user. Users are [looking for this](http://gomiso.uservoice.com/forums/54334-general/suggestions/706790-create-list-of-movies-shows-i-ve-watched-and-episo)
 [kind of thing!!](http://gomiso.uservoice.com/forums/54334-general/suggestions/724738-develop-more-user-stats)

Contributing
------------
When to contribut? Please contact [me](https://github.com/metabaron) instead of forking the project.

Usage
-----
Or directly go to [the dedicated website](http://www.metabaron.net/gomiso) or donwload the PHP code
and install it (no need for database) and update:

"display.php"

	 4 - define('TVDB_KEY', 'XXXXXXXXXXX');
	 4 - define('TVDB_KEY', 'Your TVDB API key');

"index.php" and "callback.php"

	2 - define('GOMISO_KEY', 'XXXXXXXXXXX');
	3 - define('GOMISO_SECRET', 'XXXXXXXXXXX');
	
	2 - define('GOMISO_KEY', 'Your Gomiso API key');
	3 - define('GOMISO_SECRET', 'Your Gomiso API secret');
	 
About me
-------------
You will find more about me through my [blog](http://blog.metabaron.net)