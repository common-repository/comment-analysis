<?php
/*
Plugin Name: Comment Analysis
Plugin URI: http://www.lambic.co.uk/wp-plugins/
Description: Various functions for analyzing comments
Version: 2.6.2
Author: Mark Styles
Author URI: http://www.lambic.co.uk

=>> Visit the plugin's homepage for more information and latest updates  <<=

Installation:

1. Download the file http://www.lambic.co.uk/wp-plugins/comment_analysis.phps
2. rename the file to comment_analysis.php and put it into your /wp-content/plugins/ directory.
3. Activate the plugin from your WordPress admin 'Plugins' page.
4. Add the widget or make use of the functions in your template (see examples below).

Notes:

function ca_comment_count()   - show total count of comments for whole blog
function ca_pingback_count()  - show total count of pingbacks for whole blog
function ca_trackback_count() - show total count of trackbacks for whole blog
function ca_spam_count()      - show total count of comments marked as spam

function ca_comment_last ($format='')   - show date of last comment
function ca_pingback_last ($format='')  - show date of last pingback
function ca_trackback_last ($format='') - show date of last trackback

function ca_commentor_latest ($count=10, $exclude='', $before='<li>', $after='</li>')
Shows latest $count commentors, with links to their websites if provided

function ca_commentor_most ($count=10, $exclude='', $before='<li>', $after='</li>', $before_count='(', $after_count=')') 
Show the top $count commentors, with links

function ca_comment_latest ($count=10, $length=60, $before='<li>', $after='</li>', $linktext='Go')
Shows the first $length chars of the latest $count comments

function ca_comment_latest_posts ($count=10, $show_count='yes', $before='<li>', $after='</li>', $before_count='(', $after_count=')')
Show the latest $count posts with comments (with a count of comments if $show_count is not no

function ca_comment_most($count=10, $show_count='yes', $before='<li>', $after='</li>', $before_count='(', $after_count=')', $exclude='', $hours=0)
Show the top $count commented posts (with a count of comments if $show_count is not no). 
$exclude can be used to exclude specific posts (comma separated list of post IDs). 
$hours can be used to only look at comments made in the last $hours hours (0 means show all).

function ca_author_most ($count=10, $exclude='', $before='<li>', $after='</li>', $before_count='(', $after_count=')')
Shows the top $count authors on your blog. I realise this isn't comment related, but I was too lazy to create a separate plugin.

Examples:

   <ul>
     <li>Total comments: <?php ca_comment_count(); ?></li>
     <li>Total pingbacks: <?php ca_pingback_count(); ?></li>
     <li>Total trackbacks: <?php ca_trackback_count(); ?></li>
     <li>Last comment: <?php ca_comment_last(); ?></li>
     <li>Last pingback: <?php ca_pingback_last(); ?></li>
     <li>Last trackback: <?php ca_trackback_last(); ?></li>
     <li>Latest comments
       <ul><?php ca_comment_latest(10,23); ?></ul>
     </li>
     <li>Latest commented posts
       <ul><?php ca_comment_latest_posts(10); ?></ul>
     </li>
     <li>Most commented posts
       <ul><?php ca_comment_most(10); ?></ul>
     </li>
   </ul>
*/

/*
Copyright (c) 2004, Mark Styles
Released under the GPL License
All rights reserved.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT
NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL
THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/* Widgetizer */
function widget_comment_analysis_init() {

    if (!function_exists('register_sidebar_widget'))
        return;

    function ca_default_option($options, $option, $default) {
        return isset($options[$option]) ? $options[$option] : $default;
    }

    function ca_get_options() {
        $options = get_option('widget_comment_analysis');

        $options['counts_title']                = ca_default_option($options, 'counts_title', 'Counts');
        $options['comment_count']               = ca_default_option($options, 'comment_count', true);
        $options['pingback_count']              = ca_default_option($options, 'pingback_count', false);
        $options['trackback_count']             = ca_default_option($options, 'trackback_count', false);
        $options['spam_count']                  = ca_default_option($options, 'spam_count', false);
        $options['comment_last']                = ca_default_option($options, 'comment_last', true);
        $options['pingback_last']               = ca_default_option($options, 'pingback_last', false);
        $options['trackback_last']              = ca_default_option($options, 'trackback_last', false);
        $options['commentor_latest']            = ca_default_option($options, 'commentor_latest', false);
        $options['commentor_most']              = ca_default_option($options, 'commentor_most', true);
        $options['comment_latest']              = ca_default_option($options, 'comment_latest', true);
        $options['comment_latest_posts']        = ca_default_option($options, 'comment_latest_posts', true);
        $options['comment_most']                = ca_default_option($options, 'comment_most', true);
        $options['author_most']                 = ca_default_option($options, 'author_most', false);

        $options['before_comment_count']        = $options['before_comment_count'];
        $options['after_comment_count']         = ca_default_option($options, 'after_comment_count', 'comments');
        $options['before_pingback_count']       = $options['before_pingback_count'];
        $options['after_pingback_count']        = ca_default_option($options, 'after_pingback_count', 'pingbacks');
        $options['before_trackback_count']      = $options['before_trackback_count'];
        $options['after_trackback_count']       = ca_default_option($options, 'after_trackback_count', 'trackbacks');
        $options['before_spam_count']           = $options['before_spam_count'];
        $options['after_spam_count']            = ca_default_option($options, 'after_spam_count', 'marked spam');

        $options['before_comment_last']         = ca_default_option($options, 'before_comment_last', 'Last comment');
        $options['after_comment_last']          = $options['after_comment_last'];
        $options['before_pingback_last']        = ca_default_option($options, 'before_pingback_last', 'Last pingback');
        $options['after_pingback_last']         = $options['after_pingback_last'];
        $options['before_trackback_last']       = ca_default_option($options, 'before_trackback_last', 'Last trackback');
        $options['after_trackback_last']        = $options['after_trackback_last'];

        $options['before_commentor_latest']     = ca_default_option($options, 'before_commentor_latest', 'Latest Commentors');
        $options['after_commentor_latest']      = $options['after_commentor_latest'];
        $options['commentor_latest_count']      = ca_default_option($options, 'commentor_latest_count', 10);
        $options['commentor_latest_exclude']    = $options['commentor_latest_exclude'];

        $options['before_commentor_most']       = ca_default_option($options, 'before_commentor_most', 'Most Comments');
        $options['after_commentor_most']        = $options['after_commentor_most'];
        $options['commentor_most_count']        = ca_default_option($options, 'commentor_most_count', 10);
        $options['commentor_most_exclude']      = $options['commentor_most_exclude'];

        $options['before_comment_latest']       = ca_default_option($options, 'before_comment_latest', 'Latest Comments');
        $options['after_comment_latest']        = $options['after_comment_latest'];
        $options['comment_latest_count']        = ca_default_option($options, 'comment_latest_count', 10);
        $options['comment_latest_length']       = ca_default_option($options, 'comment_latest_length', 60);

        $options['before_comment_latest_posts'] = ca_default_option($options, 'before_comment_latest_posts', 'Latest Commented Posts');
        $options['after_comment_latest_posts']  = $options['after_comment_latest_posts'];
        $options['latest_posts_count']          = ca_default_option($options, 'latest_posts_count', 10);
        $options['latest_posts_show_count']     = ca_default_option($options, 'latest_posts_show_count', true);

        $options['before_comment_most']         = ca_default_option($options, 'before_comment_most', 'Most Commented Posts');
        $options['after_comment_most']          = $options['after_comment_most'];
        $options['comment_most_count']          = ca_default_option($options, 'comment_most_count', 10);
        $options['comment_most_show_count']     = ca_default_option($options, 'comment_most_show_count', true);

        $options['before_author_most']          = ca_default_option($options, 'before_author_most', 'Most Authored Posts');
        $options['after_author_most']           = $options['after_author_most'];
        $options['author_most_count']           = ca_default_option($options, 'author_most_count', 10);
        $options['author_most_exclude']         = $options['author_most_exclude'];
        return $options;
    }

    function ca_do_list_item($do_it, $before, $func, $after, $param1 = '', $param2 = '') {
        if ($do_it) {
            echo '<li>'.$before.' ';
            call_user_func($func, $param1, $param2);
            echo ' '.$after.'</li>';
        }
    }

    function ca_do_list($do_it, $before, $func, $after, $param1 = '', $param2 = '') {
        if ($do_it) {
            echo $before;
            echo '<ul class="'.$func.'">';
            call_user_func($func, $param1, $param2);
            echo '</ul>'.$after;
        }
    }

    function ca_do_row($label, $id, $options) {
        echo '<td>'.$label.'</td>';
        echo '<td><input class="checkbox" id="'.$id.'" name="'.$id.'" type="checkbox" ';
        if ($options[$id])
            echo 'checked="checked"'; 
        echo '</td>';
        echo '<td><input id="before_'.$id.'" name="before_'.$id.'" type="text" size="10" value="'.$options['before_'.$id].'" /></td>';
        echo '<td><input id="after_'.$id.'" name="after_'.$id.'" type="text" size="10" value="'.$options['after_'.$id].'" /></td>';
    }
        
    function widget_comment_analysis($args) {
        extract($args);
        $options = ca_get_options();

        echo $before_widget;

        if ($options['comment_count'] || $options['pingback_count'] || $options['trackback_count'] || $options['spam_count']
            || $options['comment_last'] || $options['pingback_last'] || $options['trackback_last'])
        {
            echo $before_title . $options['counts_title'] . $after_title;
            echo '<ul class="comment_counts">';
            ca_do_list_item($options['comment_count']
                , $options['before_comment_count'], 'ca_comment_count', $options['after_comment_count']);
            ca_do_list_item($options['pingback_count']
                , $options['before_pingback_count'], 'ca_pingback_count', $options['after_pingback_count']);
            ca_do_list_item($options['trackback_count']
                , $options['before_trackback_count'], 'ca_trackback_count', $options['after_trackback_count']);
            ca_do_list_item($options['spam_count']
                , $options['before_spam_count'], 'ca_spam_count', $options['after_spam_count']);
            ca_do_list_item($options['comment_last']
                , $options['before_comment_last'], 'ca_comment_last', $options['after_comment_last']);
            ca_do_list_item($options['pingback_last']
                , $options['before_pingback_last'], 'ca_pingback_last', $options['after_pingback_last']);
            ca_do_list_item($options['trackback_last']
                , $options['before_trackback_last'], 'ca_trackback_last', $options['after_trackback_last']);
            echo '</ul>';
        }

        ca_do_list($options['commentor_latest'], $before_title . $options['before_commentor_latest'] . $after_title
            , 'ca_commentor_latest', $options['after_commentor_latest']
            , $options['commentor_latest_count'], $options['commentor_latest_exclude']);

        ca_do_list($options['commentor_most'], $before_title . $options['before_commentor_most'] . $after_title
            , 'ca_commentor_most', $options['after_commentor_most']
            , $options['commentor_most_count'], $options['commentor_most_exclude']);

        ca_do_list($options['comment_latest'], $before_title . $options['before_comment_latest'] . $after_title
            , 'ca_comment_latest', $options['after_comment_latest']
            , $options['comment_latest_count'], $options['comment_latest_length']);

        ca_do_list($options['comment_latest_posts'], $before_title . $options['before_comment_latest_posts'] . $after_title
            , 'ca_comment_latest_posts', $options['after_comment_latest_posts']
            , $options['latest_posts_count'], $options['latest_posts_show_count']);

        ca_do_list($options['comment_most'], $before_title . $options['before_comment_most'] . $after_title
            , 'ca_comment_most', $options['after_comment_most']
            , $options['comment_most_count'], $options['comment_most_show_count']);

        ca_do_list($options['author_most'], $before_title . $options['before_author_most'] . $after_title
            , 'ca_author_most', $options['after_author_most']
            , $options['author_most_count'], $options['author_most_show_count']);

        echo $after_widget;
    }

    function widget_comment_analysis_control() {

        $options = ca_get_options();

        if ($_POST['ca_submit']) {
            $new['counts_title']                = $_POST['counts_title'];
            $new['comment_count']               = isset($_POST['comment_count']);
            $new['pingback_count']              = isset($_POST['pingback_count']);
            $new['trackback_count']             = isset($_POST['trackback_count']);
            $new['spam_count']                  = isset($_POST['spam_count']);
            $new['comment_last']                = isset($_POST['comment_last']);
            $new['pingback_last']               = isset($_POST['pingback_last']);
            $new['trackback_last']              = isset($_POST['trackback_last']);
            $new['commentor_latest']            = isset($_POST['commentor_latest']);
            $new['commentor_most']              = isset($_POST['commentor_most']);
            $new['comment_latest']              = isset($_POST['comment_latest']);
            $new['comment_latest_posts']        = isset($_POST['comment_latest_posts']);
            $new['comment_most']                = isset($_POST['comment_most']);
            $new['author_most']                 = isset($_POST['author_most']);
            $new['before_comment_count']        = $_POST['before_comment_count'];
            $new['after_comment_count']         = ca_default_option($options, 'after_comment_count', 'comments');
            $new['before_pingback_count']       = $_POST['before_pingback_count'];
            $new['after_pingback_count']        = ca_default_option($options, 'after_pingback_count', 'pingbacks');
            $new['before_trackback_count']      = $_POST['before_trackback_count'];
            $new['after_trackback_count']       = ca_default_option($options, 'after_trackback_count', 'trackbacks');
            $new['before_spam_count']           = $_POST['before_spam_count'];
            $new['after_spam_count']            = ca_default_option($options, 'after_spam_count', 'marked spam');
            $new['before_comment_last']         = ca_default_option($options, 'before_comment_last', 'Last comment');
            $new['after_comment_last']          = $_POST['after_comment_last'];
            $new['before_pingback_last']        = ca_default_option($options, 'before_pingback_last', 'Last pingback');
            $new['after_pingback_last']         = $_POST['after_pingback_last'];
            $new['before_trackback_last']       = ca_default_option($options, 'before_trackback_last', 'Last trackback');
            $new['after_trackback_last']        = $_POST['after_trackback_last'];
            $new['before_commentor_latest']     = ca_default_option($options, 'before_commentor_latest', 'Latest Commentors');
            $new['after_commentor_latest']      = $_POST['after_commentor_latest'];
            $new['before_commentor_most']       = $_POST['before_commentor_most'];
            $new['after_commentor_most']        = $_POST['after_commentor_most'];
            $new['before_comment_latest']       = $_POST['before_comment_latest'];
            $new['after_comment_latest']        = $_POST['after_comment_latest'];
            $new['comment_latest_count']        = $_POST['comment_latest_count'];
            $new['comment_latest_length']       = $_POST['comment_latest_length'];
            $new['before_comment_latest_posts'] = $_POST['before_comment_latest_posts'];
            $new['after_comment_latest_posts']  = $_POST['after_comment_latest_posts'];
            $new['latest_posts_count']          = $_POST['latest_posts_count'];
            $new['latest_posts_show_count']     = $_POST['latest_posts_show_count'];
            $new['before_comment_most']         = $_POST['before_comment_most'];
            $new['after_comment_most']          = $_POST['after_comment_most'];
            $new['comment_most_count']          = $_POST['comment_most_count'];
            $new['comment_most_show_count']     = $_POST['comment_most_show_count'];
            $new['before_author_most']          = $_POST['before_author_most'];
            $new['after_author_most']           = $_POST['after_author_most'];
            $new['author_most_count']           = $_POST['author_most_count'];
            $new['author_most_exclude']         = $_POST['author_most_exclude'];

            if ($options != $new) {
                update_option('widget_comment_analysis', $new);
            }
        }
        ?>

        <p>
            <label for="counts_title">
                <?php _e('Counts Title: '); ?>
                <input class="widefat" id="counts_title" name="counts_title" type="text" size="10"
                    value="<?php echo $options['counts_title']; ?>" />
            </label>
        </p>
        <table class="widefat">
            <thead>
                <tr>
                    <th scope="col"><?php _e('Section') ?></th>
                    <th scope="col"><?php _e('Show?') ?></th>
                    <th scope="col"><?php _e('Prefix') ?></th>
                    <th scope="col"><?php _e('Suffix') ?></th>
                    <th scope="col"><?php _e('Count') ?></th>
                    <th scope="col"><?php _e('Other') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr> <?php ca_do_row('Comment Count', 'comment_count', $options); ?> </tr>
                <tr> <?php ca_do_row('Pingback Count', 'pingback_count', $options); ?> </tr>
                <tr> <?php ca_do_row('Trackback Count', 'trackback_count', $options); ?> </tr>
                <tr> <?php ca_do_row('Spam Count', 'spam_count', $options); ?> </tr>
                <tr> <?php ca_do_row('Last Comment', 'comment_last', $options); ?> </tr>
                <tr> <?php ca_do_row('Last Pingback', 'pingback_last', $options); ?> </tr>
                <tr> <?php ca_do_row('Last Trackback', 'trackback_last', $options); ?> </tr>
                <tr> <?php ca_do_row('Latest Commentors', 'commentor_latest', $options); ?>
                    <td><input id="commentor_latest_count" name="commentor_latest_count" type="text" size="2"
                        value="<?php echo $options['commentor_latest_count']; ?>" /></td>
                </tr>

                <tr> <?php ca_do_row('Highest Commentors', 'commentor_most', $options); ?>
                    <td><input id="commentor_most_count" name="commentor_most_count" type="text" size="2" 
                        value="<?php echo $options['commentor_most_count']; ?>" /></td>
                </tr>

                <tr> <?php ca_do_row('Latest Comments', 'comment_latest', $options); ?>
                    <td><input id="comment_latest_count" name="comment_latest_count" type="text" size="2" 
                        value="<?php echo $options['comment_latest_count']; ?>" /></td>
                    <td>Length:<input id="comment_latest_length" name="comment_latest_length" type="text" size="2" 
                        value="<?php echo $options['comment_latest_length']; ?>" /></td>
                </tr>

                <tr> <?php ca_do_row('Latest Commented Posts', 'comment_latest_posts', $options); ?>
                    <td><input id="latest_posts_count" name="latest_posts_count" type="text" size="2" 
                        value="<?php echo $options['latest_posts_count']; ?>" /></td>
                    <td><input class="checkbox" id="latest_posts_show_count" name="latest_posts_show_count" type="checkbox"
                         <?php if ($options['latest_posts_show_count']) echo 'checked="checked"'; ?> /> Show count?</td>
                </tr>
                <tr> <?php ca_do_row('Most Commented Posts', 'comment_most', $options); ?>
                    <td><input id="comment_most_count" name="comment_most_count" type="text" size="2" 
                        value="<?php echo $options['comment_most_count']; ?>" /></td>
                    <td><input class="checkbox" id="comment_most_show_count" name="comment_most_show_count" type="checkbox"
                         <?php if ($options['comment_most_show_count']) echo 'checked="checked"'; ?> /> Show count?</td>
                </tr>
                <tr> <?php ca_do_row('Most Authored', 'author_most', $options); ?>
                    <td><input id="author_most_count" name="author_most_count" type="text" size="2" 
                        value="<?php echo $options['author_most_count']; ?>" /></td>
                    <td>Exclude: <input id="author_most_exclude" name="author_most_exclude" type="text" size="10" 
                        value="<?php echo $options['author_most_exclude']; ?>" /></td>
                </tr>
            </tbody>
        </table>
        <input type="hidden" name="ca_submit" id="ca_submit" value="1" />
        <?php
    }

    register_sidebar_widget(array('Comment Analysis', 'widgets'), 'widget_comment_analysis');
    register_widget_control(array('Comment Analysis', 'widgets'), 'widget_comment_analysis_control', 630,620);
}


/* Shows the total count of comments for the whole blog */
function ca_comment_count() {
global $wpdb;
    $count=$wpdb->get_row("SELECT count(*) cnt
                               FROM   $wpdb->comments
                               WHERE  comment_approved = '1'
                               AND    comment_type NOT IN ('pingback','trackback')");
    echo $count->cnt;
}

/* Shows the total count of pingbacks for the whole blog */
function ca_pingback_count() {
global $wpdb;
    $count=$wpdb->get_row("SELECT count(*) cnt
                               FROM   $wpdb->comments
                               WHERE  comment_approved = '1'
                               AND    comment_type = 'pingback'");
    echo $count->cnt;
}

/* Shows the total count of trackbacks for the whole blog */
function ca_trackback_count() {
global $wpdb;
    $count=$wpdb->get_row("SELECT count(*) cnt
                               FROM   $wpdb->comments
                               WHERE  comment_approved = '1'
                               AND    comment_type = 'trackback'");
    echo $count->cnt;
}

/* Shows the total count of comments marked as spam */
function ca_spam_count() {
global $wpdb;
	$count=$wpdb->get_row("SELECT count(*) cnt)
				FROM $wpdb->comments
				WHERE $wpdb->comment_approved = 'spam'");
	echo $count->cnt;
}

function ca_format_date($dt, $format) {
    if ($format=='')
        echo mysql2date(get_settings('date_format'), $dt);
    else
        echo mysql2date($format, $dt);
}

/* Show the date of the last comment */
function ca_comment_last ($format='') {
global $wpdb;
    $dt=$wpdb->get_row("SELECT MAX(comment_date) dt
                            FROM   $wpdb->comments
                            WHERE  comment_approved = '1'
                            AND    comment_type NOT IN ('pingback','trackback')");
    ca_format_date($dt->dt, $format);
}

/* Show the date of the last pingback */
function ca_pingback_last ($format='') {
global $wpdb;
    $dt=$wpdb->get_row("SELECT MAX(comment_date) dt
                            FROM   $wpdb->comments
                            WHERE  comment_approved = '1'
                            AND    comment_type = 'pingback'");
    ca_format_date($dt->dt, $format);
}

/* Show the date of the last trackback */
function ca_trackback_last ($format='') {
global $wpdb;
    $dt=$wpdb->get_row("SELECT MAX(comment_date) dt
                            FROM   $wpdb->comments
                            WHERE  comment_approved = '1'
                            AND    comment_type = 'trackback'");
    ca_format_date($dt->dt, $format);
}

/* Show the latest $count commentors */
function ca_commentor_latest ($count=10, $exclude='', $before='<li>', $after='</li>') {
global $wpdb, $comment;

    $comments=$wpdb->get_results("SELECT comment_author_url
                                           , comment_author
                                      FROM   $wpdb->comments 
                                      WHERE  comment_approved = '1' 
                                      AND    comment_type NOT IN ('pingback','trackback')
                                      ORDER BY comment_date DESC");

    $excludes=explode(',', $exclude);

    foreach($comments as $comment) {
         if (!in_array($comment->comment_author, $excludes)) {
            $excludes[]=$comment->comment_author;
            echo $before;
            comment_author_link();
            echo $after;
            if (++$ccount == $count) break;
        }
    }
}

/* Show the top $count commentors */
function ca_commentor_most ($count=10, $exclude='', $before='<li>', $after='</li>', $before_count='(', $after_count=')') {
global $wpdb, $comment;

    $comments=$wpdb->get_results("SELECT comment_author_url
                                           , comment_author
                                           , COUNT(*) cnt 
                                      FROM   $wpdb->comments 
                                      WHERE  comment_approved = '1' 
                                      AND    comment_type NOT IN ('pingback','trackback')
                                      GROUP BY comment_author_url, comment_author
                                      ORDER BY 3 DESC");
    $excludes=explode(',', $exclude);

    foreach($comments as $comment) {
         if (!in_array($comment->comment_author, $excludes)) {
            echo $before;
            comment_author_link();
            echo $before_count.$comment->cnt.$after_count.$after;
            if (++$ccount == $count) break;
        }
    }
}

/* Shows the first $length chars of the latest $count comments */
function ca_comment_latest ($count=10, $length=60, $before='<li>', $after='</li>', $linktext='Go') {
global $wpdb, $comment;

    $comments=$wpdb->get_results("SELECT comment_post_id
                                           , comment_author_url
                                           , comment_author
                                           , comment_content 
                                      FROM   $wpdb->comments 
                                      WHERE  comment_approved = '1' 
                                      AND    comment_type NOT IN ('pingback','trackback')
                                      ORDER BY comment_date DESC");

    foreach($comments as $comment) {
        echo $before;
        comment_author_link();
        $id=$comment->comment_post_id;
        echo ' '.substr(strip_tags($comment->comment_content),0,$length).'..(<a href="';
        echo get_permalink($id);
        echo '">'.$linktext.'</a>)';
        echo $after;
        if (++$ccount == $count) break;
    }
}

/* Show the latest $count posts with comments (with a count of comments if $show_count is not no */
function ca_comment_latest_posts ($count=10, $show_count='yes', $before='<li>', $after='</li>', $before_count='(', $after_count=')') {
global $wpdb, $comment;

    $comments=$wpdb->get_results("SELECT id
                                            ,post_title
                                            ,COUNT(*) cnt 
                                      FROM  $wpdb->comments, $wpdb->posts 
                                      WHERE comment_approved = '1' 
                                      AND   comment_post_id = id 
                                      GROUP BY id, post_title 
                                      ORDER BY comment_date DESC");

    foreach($comments as $comment) {
        echo $before.'<a href="'.get_permalink($comment->id).'">'.stripslashes($comment->post_title).'</a>';
        if ($show_count != 'no')
            echo $before_count.$comment->cnt.$after_count;
        echo $after;
        if (++$ccount == $count) break;
    }
}

/* Show the top $count commented posts (with a count of comments if $show_count is not no */
function ca_comment_most($count=10, $show_count='yes', $before='<li>', $after='</li>', $before_count='(', $after_count=')', $exclude='', $hours=0) {
global $wpdb, $comment;

    $comments=$wpdb->get_results("SELECT id
                                            ,post_title
                                            ,count(*) cnt
                                      FROM  $wpdb->comments, $wpdb->posts
                                      WHERE comment_approved = '1'
                                      AND   comment_post_id = id
									  AND   ($hours = 0 
									  	OR comment_date > SYSDATE() - INTERVAL $hours HOUR)
                                      GROUP BY id, post_title
                                      ORDER BY 3 DESC");

    $excludes=explode(',', $exclude);

    foreach($comments as $comment) {
        if (!in_array($comment->id, $excludes)) {
        	echo $before.'<a href="'.get_permalink($comment->id).'">'.stripslashes($comment->post_title).'</a>';
        	if ($show_count != 'no')
            	echo $before_count.$comment->cnt.$after_count;
        	echo $after;
        	if (++$ccount == $count) break;
		}
    }
}

/* Show the top $count post authors */
function ca_author_most ($count=10, $exclude='', $before='<li>', $after='</li>', $berfore_count='(', $after_count=')') {
global $wpdb, $posts;

    $posts=$wpdb->get_results("SELECT display_name
                                           , COUNT(*) cnt 
                                      FROM   $wpdb->posts, $wpdb->users
                                      WHERE  post_status = 'publish'
                                      AND      $wpdb->posts.post_author = $wpdb->users.id
                                      GROUP BY display_name
                                      ORDER BY 2 DESC");
    $excludes=explode(',', $exclude);

    foreach($posts as $post) {
         if (!in_array($post->display_name, $excludes)) {
            echo $before.$post->display_name.' '.$before_count.$post->cnt.$after_count.$after;
            if (++$ccount == $count) break;
        }
    }
}

add_action('widgets_init', 'widget_comment_analysis_init');

