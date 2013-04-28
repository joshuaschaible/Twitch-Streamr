<?php
/*
Plugin Name: Twitch Plugin
Description: Grabs users from Twitch and plays there live video stream
Version: 0.1
Author: James Bulmer
Author URI: http://jabulmer12.com
*/

// [twitchplugin users='user1, user2, user3']
function twitchplugin_func($atts) {
	extract(shortcode_atts( array(
		'users' => ''
	), $atts));
	$users = str_replace(' ', '', $users);
	$users = explode(',', $users);
	$out = '
<div id="twitch_player"></div>
<div id="twitch_chat"></div>
<div id="twitch_users">';
	foreach($users as $user) {
		$json_data = file_get_contents('https://api.twitch.tv/kraken/channels/' . $user, 0, null, null);
		$json = json_decode($json_data);
		if(!$json->name) {
			// Error
		} else {
			$out = $out . '<a class="twitch_user offline" id="' . $json->name . '">';
			if($json->logo) {
				$out = $out . '<img src="' . $json->logo . '" alt="' . $json->display_name . '">';
			} else {
				$out = $out . '<img src="http://open.spotify.com/static/images/user.png" alt="' . $json->display_name . '">';
			}
			$out = $out . '<p class="game">' . $json->game . '</p>';
			$out = $out . '<p class="status">' . $json->status . '</p>';
			$out = $out . '<p class="viewers">0 viewers</p>';
			$out = $out . '</a>';
		}
	}
	$out = $out . '
</div>
// data-cfasync="false"
<script type="text/javascript" src="' . plugins_url("script.js", __FILE__ ) . '"></script>
';
	return $out;
}

add_shortcode('twitchplugin', 'twitchplugin_func');
