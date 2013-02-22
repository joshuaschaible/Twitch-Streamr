$(document).ready(function() {
  
	// <object type="application/x-shockwave-flash" height="378" width="620" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=supermcgamer" bgcolor="#000000"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="hostname=www.twitch.tv&channel=supermcgamer&auto_play=true&start_volume=25" /></object>
	// <iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel=supermcgamer&amp;popout_chat=true" height="400" width="300"></iframe>
	
	// Variables
	var members = ['FunForFreedom', 'SuperMCGamer', 'Miketheguyinthehat', 'ChocoPoptartftw', 'Koopatroopa1', 'Jim3798',
	'Lucarimew', 'Purple_link432', 'TeddyMoose', 'LetsNarvik', 'Wilicolo', 'Lizerdoo', 'SilverDSlite',
	'Zelda_queen', 'IceKrabby', 'Animejessica', 'Charissachu', 'MutatedPuppet', 'MLGCOD', '123412341234123412341234123'];
	var social = '{"MLGCOD": "a@a.com;2@2.com"}';
	social = JSON.parse(social);
	var memberData = [];
	
	$.each(members, function(index, member) {
		$.getJSON('https://api.twitch.tv/kraken/users/' + member + '?callback=?', function(d) {
			if(d.status == 404) {
				// Member dose not exist
			} else {
				var data = [];
				data[0] = member;
				data[1] = d.display_name;
				data[2] = d.logo;
				data[3] = '';
				data[4] = '';
				var currentTime = new Date();
				data[5] = ('2000-' + ("0" + (currentTime.getMonth() + 1)).slice(-2) + '-' + ("0" + currentTime.getDate()).slice(-2) + 'T' + ("0" + currentTime.getHours()).slice(-2) + ':' + ("0" + currentTime.getMinutes()).slice(-2) + ':' + ("0" + currentTime.getSeconds()).slice(-2) + 'Z' + currentTime.getMilliseconds()).replace(/\D/g,'');
				data[6] = 'offline';
				memberData.push(data);
			}
			if(index == members.length - 1) {
				update();
			}
		});
	});
	
	function update() {
		$.each(memberData, function(index, data) {
			$.getJSON('https://api.twitch.tv/kraken/channels/' + data[0] + '?callback=?', function(d) {
				if(typeof d == 'object') {
					if(d.game != null) {
						memberData[index][3] = d.game;
					} else {
						memberData[index][3] = '';
					}
					memberData[index][4] = 0;
					memberData[index][6] = 'offline';
				}
			});
			$.getJSON('https://api.twitch.tv/kraken/streams/' + data[0] + '?callback=?', function(d) {
				if(d.stream != null) {
					memberData[index][3] = d.stream.game;
					memberData[index][4] = d.stream.viewers;
					memberData[index][5] = (d.stream.channel.updated_at).replace(/\D/g,'');;
					memberData[index][6] = 'online';
				}
			});
		});
		$.each($('.member'), function() {
			$(this).remove();
		})
		$.each(memberData, function(index, data) {
			if(data[6] == 'online') {
				if($('#player').html() == '') {
					$('#player').empty();
					$('#chat').empty();
					$('#player').append('<object type="application/x-shockwave-flash" height="378" width="620" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=' + data[0] + '" bgcolor="#000000"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="hostname=www.twitch.tv&channel=' + data[0] + '&auto_play=true&start_volume=25" /></object>');
					$('#player').append('<iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel=' + data[0] + '&amp;popout_chat=true" height="400" width="300"></iframe>');
				}
				$('#members').append('<a class="member ' + data[6] + '" id="' + data[0] + '">');
				if(data[2]) {
					$('#' + data[0]).append('<img src="' + data[2] + '" alt="' + data[1] + '"></a>');
				} else {
					$('#' + data[0]).append('<img src="http://open.spotify.com/static/images/user.png" alt="' + data[1] + '"></a>');
				}
				$('#' + data[0]).append('<p>' + data[1] + '</p>');
				$('#' + data[0]).append('<p>' + data[3] + '</p>');
				$('#' + data[0]).append('<p>' + data[4] + ' viewers</p>');
				if(social[data[0]]) {
					$.each(social[data[0]].split(';'), function(index, item) {
						$('#' + data[0]).append('<a href="' + item + '">' + item + '</a>');
					});
				}
			}
		});
		$.each(memberData, function(index, data) {
			if(data[6] == 'offline') {
				$('#members').append('<a class="member ' + data[6] + '" id="' + data[0] + '">');
				if(data[2]) {
					$('#' + data[0]).append('<img src="' + data[2] + '" alt="' + data[1] + '"></a>');
				} else {
					$('#' + data[0]).append('<img src="http://open.spotify.com/static/images/user.png" alt="' + data[1] + '"></a>');
				}
				$('#' + data[0]).append('<p>' + data[1] + '</p>');
				$('#' + data[0]).append('<p>' + data[3] + '</p>');
				$('#' + data[0]).append('<p>' + data[4] + ' viewers</p>');
				if(social[data[0]]) {
					$.each(social[data[0]].split(';'), function(index, item) {
						$('#' + data[0]).append('<a href="' + item + '">' + item + '</a>');
					});
				}
			}
		});
		memberData.sort(function(a,b){
			if(a[5]==b[5]) return a[5]-b[5];
			if(a[5]<b[5]) return 1;
			return -1;
		});
	}
	
	// Callbacks
	function code() {
		// Change video and chat feed onclick
		$('#members > a').unbind('click').click(function() {
			$('#player').empty();
			$('#chat').empty();
			$('#player').append('<object type="application/x-shockwave-flash" height="378" width="620" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=' + $(this).attr('id') + '" bgcolor="#000000"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="hostname=www.twitch.tv&channel=' + $(this).attr('id') + '&auto_play=true&start_volume=25" /></object>');
			$('#player').append('<iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel=' + $(this).attr('id') + '&amp;popout_chat=true" height="400" width="300"></iframe>');
		});
	}
	
	// Game loop
	var time = jQuery.now() + 5000;
	window.setInterval(function() {
		if(jQuery.now() > time) {
			update();
			time = jQuery.now() + 5000;
		}
		code();
	}, 0);
});
