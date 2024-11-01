=== Plugin Name ===
Tags: twitter, social media, buzz, trend, topic, tweet
Tested up to: 3.3.1
Stable tag: 1.4
Tweet Deets allows you to see the twitter buzz about a topic at a glance.

== Changelog ==

= 1.0 =
* First public release
= 1.1 =
* Admin page added, allowing users to customise how many pages of tweets are returned, what is searched for and what to display.
= 1.2 =
* Tidying
= 1.3 =
* Tidying folder structure
= 1.4=
* Ammending plugin information

== Description == 
Place Tweet Deets on a page of your choice to bring out a tweet highlight, the number of recent tweets on a topic and a
"Hotness Percentage", which shows how current the discussion of the topic is.

Tweet Deets find tweets based on the URL of the current page, and can also be configured to find tweets based on Page Title and Post title of current pages.

== Installation ==
To install manually, place the tweet-deets folder directly into the wp-content/plugins/ directory.

Then activate the plugin from the WP-Admin Dashboard.

Once activated, you should visit the Options page, which is found under the Settings menu in the dashboard. Here, you can set the scope of the search,
how many pages of tweets to gather and which elements you wish to display.

== Usage ==
To use, simply call the tweet_deets() function. Here is an example of how to call it on all pages except the home page
and any archives:

		`<?php if(function_exists('tweet_deets') && !is_home() && !is_archive()){ tweet_deets(); } ?>`

For your convenience, the plugin is styled only mildly and comes jam packed with CSS classes you can call on to customise
the look and feel of your Tweet Deets, or hide sections you do not wish to see.

In the future there will be updates - Feel free to send me feedback if you have any good ideas!