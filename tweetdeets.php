<?php
/*
Plugin Name: Tweet Deets
Plugin URI: http://adamburt.com/work/?p=15
Description: Tweet Deets lets you know how a topic, page or post is performing on Twitter, with recent tweets and statistics
Version: 1.4
Author: Adam Burt
Author URI: http://www.adamburt.com
License: GPL2
*/
?>
<?php
/*  Copyright 2012  Adam Burt (email : Adam@adamburt.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php

//Admin menu
function tweetdeet_admin(){
	echo "<h2>Tweet Deets Options</h2>";
	
	//How many pages of tweets
	echo '<h3>Pages</h3><p>Select how many pages of tweets to search. Each page will return a maximum of 100 results.</p>
	<p>Note: The higher this number is, the slower the page containing your plugin may become</p>
	<form method="post" action="options.php">';
	settings_fields( 'tweetdeet-group' );

	echo '<label for="num-pages">Number of pages:</label><select id="num-pages" name="num-tweet-pages">';
	$i = 1;
	while ($i < 16){
		?><option value="<?php echo $i; ?>" <?php if(get_option('num-tweet-pages') == $i){ echo "selected"; } ?>><?php echo $i; ?></option><?php
		$i++;
	}
	echo '</select>';
	
	//Checkbox for page title, post title, URL
	if(get_option('inc-page-title') == 1){
		$incpagetitle = "checked='checked'";
	} else {
		$incpagetitle = "";
	}
	if(get_option('inc-post-title') == 1){
		$incposttitle = "checked='checked'";
	} else {
		$incposttitle = "";
	}

	
	echo "<h3>Scope</h3><p>Select which elements of the page to include in the search terms. If you find the results are too vague you could remove some of these to narrow your search down</p>";
	echo '<label>Include Page Title in search: <input type="checkbox" name="inc-page-title" value="1" '.$incpagetitle.'/></label><br />';
	echo '<label>Include Post Title in search: <input type="checkbox" name="inc-post-title" value="1" '.$incposttitle.'/></label><br />';


	if(get_option('inc-recent-tweets') == 1){
		$increcenttweets = "checked='checked'";
	} else {
		$increcenttweets = "";
	}
	if(get_option('inc-highlight-tweet') == 1){
		$inchighlighttweet = "checked='checked'";
	} else {
		$inchighlighttweet = "";
	}
	if(get_option('inc-hot-percent') == 1){
		$inchotpercent = "checked='checked'";
	} else {
		$inchotpercent = "";
	}	
	//Checkbox for how many tweets, higlighted tweet, hotness percentage.
	echo "<h3>Display</h3><p>Select which elements of Tweet Deets to show.</p>";
	echo '<label>Include Number of Recent Tweets: <input type="checkbox" name="inc-recent-tweets" value="1" '.$increcenttweets.'/></label><br />';
	echo '<label>Include Highlighted Tweet: <input type="checkbox" name="inc-highlight-tweet" value="1" '.$inchighlighttweet.'/></label><br />';
	echo '<label>Include Hotness Percentage: <input type="checkbox" name="inc-hot-percent" value="1" '.$inchotpercent.'/></label><br />';

	?><br /><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /><?php
	echo "</form>";
}

function register_tweetdeet_settings() {
	//register our settings
	register_setting( 'tweetdeet-group', 'num-tweet-pages' );
	register_setting( 'tweetdeet-group', 'inc-page-title' );
	register_setting( 'tweetdeet-group', 'inc-post-title' );
	register_setting( 'tweetdeet-group', 'inc-recent-tweets' );
	register_setting( 'tweetdeet-group', 'inc-highlight-tweet' );
	register_setting( 'tweetdeet-group', 'inc-hot-percent' );
}

add_action('admin_menu', 'tweetdeets_menu');

function tweetdeets_menu() {
	add_options_page('Tweet Deets Options', 'Tweet Deets', 'manage_options', 'tweet_deets_options', 'tweetdeet_admin');
	
	//call register settings function
	add_action( 'admin_init', 'register_tweetdeet_settings' );
}

//Add stylesheet for Tweet Deets
add_action( 'wp_enqueue_scripts', 'prefix_add_tweetdeets_stylesheet' );
function prefix_add_tweetdeets_stylesheet() {
	wp_register_style( 'tweetdeets-style', plugins_url('tweetdeets-styles.css', __FILE__) );
    wp_enqueue_style( 'tweetdeets-style' );
}

//Our Tweet Deets function
function tweet_deets(){

	//Get page title
	if(get_option('inc-page-title') == 1){
		$current_page_title = "%20OR%20".urlencode(wp_title("", 0));
	} else {
		$current_page_title = "";
	}
	
	//Get post title
	if(get_option('inc-post-title') == 1){
		$current_post_title = "%20OR%20".urlencode(get_the_title());
	} else {
		$current_post_title = "";
	}
	
	//Get current URL
	$current_page_URL = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	//echo urlencode($current_page_URL).$current_page_title;
	//die();
	
	//Twitter search by page title or post title (or containing link to this page)
	$tweets = array();
	$i = 1;
	while ($i < (get_option('num-tweet-pages') + 1)){
	$tweetget = file_get_contents("http://search.twitter.com/search.json?page=".$i."&q=".$current_page_URL.$current_page_title.$current_post_title."&rpp=100", "r");
	$tweet_array = json_decode($tweetget, true);
	array_push($tweets, $tweet_array['results']);
	$i++;
	}
	
	
	//A count of how many tweets are returned.
	$tweetcount = 0;
	$total_tweets = array();
	$i_2 = 1;
	foreach($tweets as $key=>$value){
		foreach ($tweets[$key] as $tweetkey=>$tweetvalue){
			$tweetnum = count($tweetvalue['text']);
			$tweetcount = $tweetcount + $tweetnum;
			array_push($total_tweets, $tweetvalue);
		}
	}
	echo '<div class="widget tweet-deets"><h3 class="widget-title tweet-deets-title">Tweet Deets</h3>';
	echo '<div class="tweet-deets-info"><p>Tweet Deets allows you to see at a glance the buzz this topic has on Twitter.</p></div>';
	
	if($tweetcount > 0){
	if(get_option('inc-recent-tweets') == 1){
	echo '<p class="tweet-deets-count"><span class="tweet-deets-count-text">Recent tweets about this topic:</span> <span class="tweet-deets-count-number">'.$tweetcount.'</span></p>';
	}
	//Get most recent tweet
	$tweethighlight = $total_tweets[0];
		//Display it
		if(get_option('inc-highlight-tweet') == 1){
		echo '<div class="tweet-deets-highlighted-tweet"><h4 class="tweet-deets-tweet-highlight">Tweet Highlight</h4><p class="tweet-deets-highlighted-tweet-actual"><span class="tweet-deets-higlighted-tweet-author">By @'.$tweethighlight['from_user'].':</span><br />'.$tweethighlight['text'].'</p></div>';
		}
		//Take date and compare to current time to generate a hotness percentage
		$tweetdate_format = strtotime($tweethighlight['created_at']);
		$current_time = strtotime("now");

		
	$backtweet = end($total_tweets);
	$backtweetdate = strtotime($backtweet['created_at']);
	
	$distance = $current_time - $tweetdate_format;
	$backtweet_distance = $current_time - $backtweetdate;
	$backtweet_days = ((($backtweet_distance / 60) / 60) / 24) * 2;
	
	$distance = $distance / 604800;
	$antipercent = $distance * 100;
	$percent = (100 - $antipercent) - $backtweet_days;
		
	//Display Hotness percentage
	if(get_option('inc-hot-percent') == 1){
		echo '<div class="tweet-deets-hotness-percentage"><h4 class="tweet-deets-hotness-percentage-title">Hotness Percentage</h4><p class="tweet-deets-hotness-percentage-actual">'.number_format($percent, 0).'%</p></div>';
	}
	} else {
	echo '<div class="tweet-deets-no-tweets">No current Twitter buzz about this topic.</div>';
	}
	echo '</div>';
}

?>
