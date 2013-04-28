<style>
html
{
   background-color: lightgrey;
}
#myDiv {
    margin: 0 auto;
     width: 150px;
    z-index: 2;
}
#myDiv img
{
position:fixed;
z-index:2;
margin-top:460px;
}


#my-div
{
    width    : 100px;
    height   : 20px;
    overflow : hidden;
    position : relative;
	margin-left : 120px;
	border : solid 1px black;
}
 
#my-iframe
{
    position : absolute;
    top      : 0px;
    left     : -255px;
    width    : 1350px;
    height   : 1200px;
}
</style>
<script type = "text/javascript">
    setTimeout(function() {
        document.getElementById("myDiv").style.display="none";
    }, 500000);  // timing
</script>
<script>
/*
function aman(uname)
{
document.getElementById(uname).style.display="block";
document.getElementById("div1").style.display="none";
}
*/
</script>
<?php
 
 set_time_limit (2500);
 
 function xml2array($contents, $get_attributes=1, $priority = 'tag') 
 {
    if(!$contents) return array();

    if(!function_exists('xml_parser_create')) 
	{
        //print "'xml_parser_create()' function not found!";
        return array();
    }

    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);
	
    if(!$xml_values) return;//Hmm...

    //Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &$xml_array; //Refference

    //Go through the tags.
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
    foreach($xml_values as $data) 
	{
        unset($attributes,$value);//Remove existing values, or there will be trouble

        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data);//We could use the array by itself, but this cooler.

        $result = array();
        $attributes_data = array();
        
        if(isset($value)) 
		{
            if($priority == 'tag') $result = $value;
            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
        }

        //Set the attributes too.
        if(isset($attributes) and $get_attributes) 
		{
            foreach($attributes as $attr => $val) 
			{
                if($priority == 'tag') $attributes_data[$attr] = $val;
                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }

        //See tag status and do the needed.
        if($type == "open") 
		{//The starting of the tag '<tag>'
            $parent[$level-1] = &$current;
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) 
			{ //Insert New tag
                $current[$tag] = $result;
                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                $repeated_tag_index[$tag.'_'.$level] = 1;

                $current = &$current[$tag];

            } 
			else 
			{ //There was another element with the same tag name

                if(isset($current[$tag][0])) 
				{//If there is a 0th element it is already an array
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    $repeated_tag_index[$tag.'_'.$level]++;
                } 
				else 
				{//This section will make the value an array if multiple tags with the same name appear together
                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                    $repeated_tag_index[$tag.'_'.$level] = 2;
                    
                    if(isset($current[$tag.'_attr'])) 
					{ //The attribute of the last(0th) tag must be moved as well
                        $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                        unset($current[$tag.'_attr']);
                    }
				}
                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                $current = &$current[$tag][$last_item_index];
            }
		
        } 
		elseif($type == "complete") 
		{ //Tags that ends in 1 line '<tag />'
            //See if the key is already taken.
            if(!isset($current[$tag])) 
			{ //New Key
                $current[$tag] = $result;
                $repeated_tag_index[$tag.'_'.$level] = 1;
                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
			} 
			else 
			{ //If taken, put all things inside a list(array)
                if(isset($current[$tag][0]) and is_array($current[$tag])) 
				{//If it is already an array...

                    // ...push the new element into that array.
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    
                    if($priority == 'tag' and $get_attributes and $attributes_data)
					{
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag.'_'.$level]++;

                } 
				else 
				{ //If it is not an array...
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $get_attributes) 
					{
                        if(isset($current[$tag.'_attr'])) 
						{ //The attribute of the last(0th) tag must be moved as well
                            
                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }
                        
                        if($attributes_data) 
						{
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                }
            }

        } 
		elseif($type == 'close') 
		{ //End of tag '</tag>'
            $current = &$parent[$level-1];
        }
    }
    
    return($xml_array);
} 
function curl_download($Url)
{
 
    // is cURL installed yet?
    if (!function_exists('curl_init'))
	{
        die('Sorry cURL is not installed!');
    }
 
    // OK cool - then let's create a new cURL resource handle
    $ch = curl_init();
 
    // Now set some options (most are optional)
 
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $Url);
 
    // Set a referer
    curl_setopt($ch, CURLOPT_REFERER, "http://www.example.org/yay.htm");
 
    // User agent
    curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
 
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 0);
 
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
 
    // Download the given URL, and return output
    $output = curl_exec($ch);
 
    // Close the cURL resource, and free system resources
    curl_close($ch);
 
    return $output;
}

function get_string_between1($string, $start, $end)
{
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}	


$query="funforfreedom";
$name="funforfreedom";
if($query==$name) 
{
	$name=$query;
	$data=curl_download('http://www.twitch.tv/team/'.$name.'/live_member_list');
	$info='<?xml version="1.0" encoding="ISO-8859-1"?>';
	$info.=$data;
	$parser=xml2array($data);
	$user_video=str_replace("/",'',$parser['div']['div'][0]['a_attr']['href']);
	$video_data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=$user_video"; 
	?>
	<!--
		<div id="div1" style="width:1000px; height:auto;  float:right;">
					<table width="100%" border="1px" style="background-color: #709000;">
						<tr>
							<td>
							<div>
							<object type="application/x-shockwave-flash" height="378" width="620" id="live_embed_player_flash" data="<?php //echo $video_data;?>" bgcolor="#000000">
							<param name="allowFullScreen" value="true" />
							<param name="allowScriptAccess" value="always" />
							<param name="allowNetworking" value="all" />
							<param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" />
							<param name="flashvars" value="hostname=www.twitch.tv&channel=<?php //echo $user_video;?>&auto_play=true&start_volume=25" />
							</object>
							<iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel=<?php //echo $user_video;?>&amp;popout_chat=true" height="400" width="300"></iframe>
							</div></td>
							
						</tr>
					</table>
		</div> 
		-->
		<?php
		if(isset($_REQUEST['idnum']))	
		{$idnum=$_REQUEST['idnum'];}
		else{$idnum=0;}
		
		$url='http://www.twitch.tv/'.str_replace("/",'',$parser['div']['div'][$idnum]['a_attr']['href']);

				$mymemberhtml=curl_download($url);
				
				$title = get_string_between1($mymemberhtml , "<span class='real_title js-title'>","</span>");

				$views_and_followers = get_string_between1($mymemberhtml , "<div id='channel_stats'>","</div>");
				$viewsandfollowers = explode(" ",$views_and_followers);
				$views = strstr($viewsandfollowers[8],'>');
				$followers = strstr($viewsandfollowers[11],'>');
				
				$playing = get_string_between1($mymemberhtml , "<div class='channel'>","</div>");
				
				$user_video=str_replace("/",'',$parser['div']['div'][$idnum]['a_attr']['href']);
				$video_data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=$user_video";
				
		?>
	
	<div style="width:1000px; height:auto;  float:right;">
		<table width="100%" border="1px" style="background-color: #709000;">
			<tr>
				<td colspan="2">
				<div>
				<object type="application/x-shockwave-flash" height="378" width="620" id="live_embed_player_flash" data="<?php echo $video_data;?>" bgcolor="#000000">
				<param name="allowFullScreen" value="true" />
				<param name="allowScriptAccess" value="always" />
				<param name="allowNetworking" value="all" />
				<param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" />
				<param name="flashvars" value="hostname=www.twitch.tv&channel=<?php echo $user_video;?>&auto_play=true&start_volume=25" />
				</object>
				<iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel=<?php echo $user_video;?>&amp;popout_chat=true" height="400" width="300"></iframe>
				</div>
				</td>
			</tr>
			
			<tr>
			<td>
				Title : <?php echo $title ; ?>
			</td>
			<td>
				Followers : <?php echo substr($followers,1) ; ?>
			</td>
			</tr>
			
			<tr>
			<td>
				Views : <?php echo substr($views,1) ; ?>
			</td>
			<td>
			    Description : <?php echo $playing ; ?>
			</td>
			</tr>
			
			
		</table>
	</div>
		
		
		
		
	
	<!--<div id = "myDiv"><img id ="myImage" src ="loader.gif"></div> -->
		<?php
	function get_string_between($string, $start, $end)
	{
		$string = " ".$string;
		$ini = strpos($string,$start);
		if ($ini == 0) return "";
		$ini += strlen($start);
		$len = strpos($string,$end,$ini) - $ini;
		return substr($string,$ini,$len);
	}	
    $str=get_string_between($data , "page_data","next_page");
    $myarr=explode(" ",strip_tags(str_replace("'>","",$str)));
	
	for($j = 0 ; $j <count($myarr);$j++) 
	{
		if(($myarr[$j]!= '') &&  ($myarr[$j] != 1)) 
		{
			$data=curl_download('http://www.twitch.tv/team/'.$name.'/live_member_list?page='.$myarr[$j]);
			$info='<?xml version="1.0" encoding="ISO-8859-1"?>';
			$info.=$data;
			$parser=xml2array($data);
			for($i= 0 ; $i < count($parser['div']['div']) ; $i++)
			{
				$url='http://www.twitch.tv/'.str_replace("/",'',$parser['div']['div'][$i]['a_attr']['href']);
			
				$mymemberhtml=curl_download($url);
			
				$user_video=str_replace("/",'',$parser['div']['div'][$i]['a_attr']['href']);
			
				$video_data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=$user_video"; 
				$src='';
				if(isset($parser['div']['div'][$i]['a']['img_attr']['src1'])) 
				{
					$src=$parser['div']['div'][$i]['a']['img_attr']['src1'];
				}
				else if(isset($parser['div']['div'][$i]['a']['img_attr']['src']))
				{
					$src=$parser['div']['div'][$i]['a']['img_attr']['src'];
				}
				if($parser['div']['div'][$i]['a']['span'] != '' )
				{ 
					$url='http://www.twitch.tv/'.str_replace("/",'',$parser['div']['div'][$i]['a_attr']['href']);
		
					$mymemberhtml=curl_download($url);
					 
					$title = get_string_between($mymemberhtml , "<h2 id='broadcast_title'>","</h2>");
					$views = get_string_between($mymemberhtml , "<span class='stat' id='views_count'>","</span>");
					$followers = get_string_between($mymemberhtml , "<span class='stat' id='followers_count'>","</span>");
					$channelviewercount = get_string_between($mymemberhtml , "<span id='channel_viewer_count' class='stat'>","</span>");
					$follow = get_string_between($mymemberhtml , "<span id='metagame'>","</span>");
					
					$uname=str_replace("/",'',$parser['div']['div'][$i]['a_attr']['href']);
					$member=str_replace("/",'',$parser['div']['div'][$i]['a_attr']['href']);
					?>
				
					<div style="width:190px " class="<?php echo $uname; ?>">

					<div style="float:right" >
					<div name="chatting">
					<?php
					$channel = $user_video=str_replace("/",'',$parser['div']['div'][$i]['a_attr']['href']);
					$json_file = @file_get_contents("http://api.justin.tv/api/stream/list.json?".
								"channel={$channel}", 0, null, null);
					$json_array = json_decode($json_file, true);
					if($json_array[0]['name'] == "live_user_{$channel}" )
					{
						$channelTitle = $json_array[0]['channel']['title'];
						$title        = $json_array[0]['channel']['status'];
						printf('Online'); 
					}
					else
					{
						printf('Offline');
					}
					?>
					</div>
					<!--
					<a  href="#<?php //echo $uname; echo $i; ?>" onclick="return aman('<?php //echo $uname; ?>');" style="float:left; width:124px;">
					<?php //echo $parser['div']['div'][$i]['a']['span']; ?>
					</a>
					-->
					<a style=" float: left; width: 124px !important;" href="index.php?id=<?php echo str_replace("/",'',$parser['div']['div'][$i]['a_attr']['href']);?>&idnum=<?php echo $i?>">
					<?php echo $parser['div']['div'][$i]['a']['span']; ?>
					</a>
					
					
					</div>

					<div style=""> 
					<img src="<?php echo $src; ?>" class="<?php echo $src ;?>">
					</div>
					<!--
					<div id="<?php echo $uname; ?>" style="width:1000px; height:auto;  margin-left:100px; display:none">
							<table width="100%" border="1px" style="background-color: #709000;">
								<tr>
									<th width="10%">Title</th> <td><?php echo $title ?></td>	
									<th>Description</th> <td><?php echo $description ?></td>
								</tr>
								<tr>
									<th>Views</th> <td><?php echo $views ?></td>	
									<th>Followers</th> <td><?php echo $followers ?></td>
								</tr>
								<tr>
									<th>Follow</th> <td><?php echo $follow2 ?></td>	
									<td><div><object type="application/x-shockwave-flash" height="378" width="620" id="live_embed_player_flash" data="<?php echo $video_data;?>" bgcolor="#000000">
									<param name="allowFullScreen" value="true" />
									<param name="allowScriptAccess" value="always" />
									<param name="allowNetworking" value="all" />
									<param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" />
									<param name="flashvars" value="hostname=www.twitch.tv&channel=<?php echo $user_video;?>&auto_play=true&start_volume=25" />
									</object></div></td>
									<td><div><iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel=<?php echo $user_video;?>&amp;popout_chat=true" height="400" width="300"></iframe></div></td>
								</tr>
							</table>
						</div>
					</div>
					-->
				</div>
				<hr style="color:ivory" />
				<?php
				}
			}
			
		}
	}
	
}
?>
