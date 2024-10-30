=== Comment Analysis ===
Contributors: lambic
Tags: comments, analysis, statistics
Requires at least: 2.0.2
Tested up to: 2.6
Stable tag: 2.6.2

Functions to output comment statistics and lists

== Description ==

Contains functions to show comment count, pingback count, trackback count, top commentors, most recent comments, and more.


== Installation ==

1. Download the appropriate version
1. Make sure the file is called comment\_analysis.php
1. Put it into your /wp-content/plugins/ directory.
1. Activate the plugin from your WordPress admin "Plugins" page.
1. Make use of the functions in your template (see examples below).


== Frequently Asked Questions ==


= What functions are available? =

This plugin is now widgetized, so you can use the widget, or use these functions:

ca\_comment\_count()
Show total count of comments for your blog, excluding pingbacks and trackbacks.

ca\_pingback\_count()
Show total count of pingbacks for your blog.

ca\_trackback\_count()
Show total count of trackbacks for your blog.

ca\_spam\_count()
Show total count of comments marked as spam. (Version 2.1 only)

ca\_comment\_last ($format=.)
Show date of last comment. The optional parameter allows you to format the date using standard date formatting.

ca\_pingback\_last ($format=.)
Show date of last pingback. The optional parameter allows you to format the date using standard date formatting.

ca\_trackback\_last ($format=.)
Show date of last trackback. The optional parameter allows you to format the date using standard date formatting.

ca\_commentor\_latest ($count=10, $exclude='' $before='<li>', $after='</li>')
Shows latest $count commentors, with links to their websites if provided. $exclude is a comma separated list of commentors not to include in the list. $before and $after define the tags which will be output at the start and end of each line.

ca\_commentor\_most ($count=10, $exclude='', $before='<li>', $after='</li>', $before_count='(', $after_count=')')
Show the top $count commentors, with links. $exclude is a comma separated list of commentors not to include in the list. $before and $after define the tags which will be output at the start and end of each line.

ca\_comment\_latest ($count=10, $length=60, $before='<li>', $after='</li>', $linktext='Go')
Shows the first $length chars of the latest $count comments. $before and $after define the tags which will be output at the start and end of each line.

ca\_comment\_latest\_posts ($count=10, $show\_count='yes', $before='<li>', $after='</li>', $before_count='(', $after_count=')')
Show the latest $count posts with comments (with a count of comments if $show\_count is not no. $before and $after define the tags which will be output at the start and end of each line.

ca\_comment\_most($count=10, $show\_count='yes', $before='<li>', $after='</li>', $before_count='(', $after_count=')', $exclude='', $hours=0)
Show the top $count commented posts (with a count of comments if $show\_count is not no. $before and $after define the tags which will be output at the start and end of each line.
$exclude can exclude certain posts, it should contain a comma separated list of post IDs.
If $hours is not zero, only comments in the last $hours hours will be looked at.

ca\_author\_most ($count=10, $exclude='', $before='<li>', $after='</li>', $before_count='(', $after_count=')')
Show the top $count authors on your blog (with a count of posts written). I realise this isn.t comment related, but someone asked for it and I was too lazy to write a new plugin for it.

