Twitch Streamr


How It Works
==================

== Automatic ==
This pulls in all the members under a specific team. Just change the team name in lines 257 and 258:

$query="funforfreedom";
$name="funforfreedom";


== Manual ==
For the most part, the script is entirely automatic. The only areas that need to be adjusted are the users you’re looking to pull in. With the standalone version, simply replace the users below within the twitch-stream.js file. You can add as many or as little as you like:

var members = ['FunForFreedom', 'SuperMCGamer', 'Miketheguyinthehat', 'ChocoPoptartftw', 'Koopatroopa1', 'Jim3798', 'Lucarimew', 'Purple_link432', 'TeddyMoose', 'LetsNarvik', 'Wilicolo', 'Lizerdoo', 'SilverDSlite', 'Zelda_queen', 'IceKrabby', 'Animejessica', 'Charissachu', 'MutatedPuppet', 'MLGCOD', '123412341234123412341234123'];


== Wordpress Plugin ==
You can snag the Wordpress plugin of this here: https://github.com/noahshrader/stream-list-twitch-plugin


Changelog
==================

v1.5
- Automatic team pulls
- Moved the Wordpress plugin into the main Twitch Stream Feeder repository

v1.2.2
- Facelift

v1.2
- Bug fixes and performance enhancements

v1.0
- Initial build 