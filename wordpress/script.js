jQuery.ajaxPrefilter(function( options ) {
	options.global = true;
});

jQuery(document).ready(function($) {
	var online = [];
	var status = true;
	function updateUsers() {
		$(".twitch_user").each(function(index, user) {
			$.ajax({ url: "https://api.twitch.tv/kraken/streams/" + $(user).attr("id") + "?callback=?", success: function(d) {
				if(d.stream) {
					$(user).addClass("online");
					$(user).removeClass("offline");
					$(user).children(".viewers").text(d.stream.viewers + " viewers");
					var content = $(user);
					$(user).remove();
					online.push([d.stream.channel.updated_at, content, $(user).attr("id")]);
				} else {
					$(user).addClass("offline");
					$(user).removeClass("online");
					$(user).children(".viewers").text("0 viewers");
				}
			}, dataType: "json"});
		});
	}
	function updateScreen() {
		$.each(online, function(index, data) {
			if($('#twitch_player').html() == '') {
				$("#twitch_player").empty();
				$("#twitch_chat").empty();
				$("#twitch_player").append('<object type="application/x-shockwave-flash" height="378" width="620" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=' + data[2] + '" bgcolor="#000000"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="hostname=www.twitch.tv&channel=' + data[2] + '&auto_play=true&start_volume=25" /></object>');
				$("#twitch_player").append('<iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel=' + data[2] + '&amp;popout_chat=true" height="400" width="300"></iframe>');
			}
			$("#twitch_users").prepend(data[1]);
		});
	}
	$("#twitch_users").ajaxStop(function() {
		online.sort(function(a,b){
			if(a[5]==b[5]) return a[5]-b[5];
			if(a[5]<b[5]) return 1;
			return -1;
		});
		updateScreen();
		online = [];
		if(status == false) {
			setTimeout(function() {
				updateUsers();
			}, 20000);
		} else {
			updateUsers();
			status = false;
		}
	});
	$("#twitch_users > a").unbind("click").click(function() {
		$("#twitch_player").empty();
		$("#twitch_chat").empty();
		$("#twitch_player").append('<object type="application/x-shockwave-flash" height="378" width="620" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=' + $(this).attr('id') + '" bgcolor="#000000"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="hostname=www.twitch.tv&channel=' + $(this).attr('id') + '&auto_play=true&start_volume=25" /></object>');
		$("#twitch_player").append('<iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel=' + $(this).attr('id') + '&amp;popout_chat=true" height="400" width="300"></iframe>');
	});
	updateUsers();
});
