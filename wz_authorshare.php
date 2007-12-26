<?php
/*
Plugin Name: Weasel's AuthorShare
Plugin URI: http://www.thedailyblitz.org/weasels-authorshare-plugin
Description: A plugin that allows you to display a percentage share of the published database per author. Requires <a href="http://www.redalt.com/wiki/Role+Manager">Red Alt's Role-Manager</a> plugin for full functionality.
Author: Andy Moore
Version: 2.0
Author URI: http://www.thedailyblitz.org
*/

// ##### ---------- NOTHING USER-CONFIGURABLE AFTER HERE ------------

add_option('wz_authorshare_pre','Percentage Share: ','Preface to Authorshare output'); // ### FIXME: can probably turn this all into an array
add_option('wz_authorshare_post','% <BR />','Postface to Authorshare output');
add_option('wz_authorshare_decimals',2,'Decimal places for Authorshare output');
add_option('wz_authorshare_showall',0,'Show x users');
add_option('wz_authorshare_publicusers',array('0' => 'user id'));

function wz_users_split($supplied) { // ### Breaks up the user IDs into an array from comma seperated values
    return preg_split('/\s*,+\s*/', $supplied, -1, PREG_SPLIT_NO_EMPTY);
}
  
function wz_get_nicename($who) {
	global $wpdb;
	$sql = "SELECT
			user_nicename
		FROM
			$wpdb->users U
		WHERE
			U.ID = $who";

	return($wpdb->get_col($sql));
}

function wz_authorshare_top10($pre, $post, $decimals, $showall, $publicusers) { 

	global $user_ID, $wpdb;

	if (!$pre) { $pre = get_option('wz_authorshare_pre'); }
	if (!$post) { $post = get_option('wz_authorshare_post'); }
	if (!$decimals) { $decimals = get_option('wz_authorshare_decimals'); }
    if (!$publicusers) { $publicusers = get_option('wz_authorshare_publicusers'); }
	if (!$showall) { $showall = get_option('wz_authorshare_showall'); }

  	if ($showall == 0) {
     $publicusersarray = wz_users_split($publicusers);
	 foreach ($publicusersarray as $pubuserid) {
	  	$username = wz_get_nicename($pubuserid);
		echo(wz_authorshare($pubuserid,$pre,'% ('.$username[0].')'.$post, $decimals)); }
    } else {
		$sql = "
			SELECT
				ID
			FROM
				$wpdb->users U
		";
		$userids = $wpdb->get_col($sql);
		foreach ($userids as $id) {
			$count[$id] = wz_authorshare_percent($id);
		}
		arsort($count);
		$count = array_slice($count,0,$showall,1);
		foreach ($count as $id => $percentage) {
			$username = wz_get_nicename($id);
			echo(wz_authorshare($id,$pre,'% ('.$username[0].')'.$post, $decimals));
		}
	 }
}

function wz_authorshare_menu() { // ### this generates and displays the admin panel menu
	global $user_ID, $wpdb;

	if (isset($_POST['info_update'])) {
		update_option('wz_authorshare_pre',$_POST['wz_authorshare_pre']);
		update_option('wz_authorshare_post',$_POST['wz_authorshare_post']);
		update_option('wz_authorshare_decimals',$_POST['wz_authorshare_decimals']);
		update_option('wz_authorshare_publicusers',wz_users_split($_POST['wz_authorshare_publicusers']));
		update_option('wz_authorshare_showall',$_POST['wz_authorshare_showall']);
	}

	$pre = get_option('wz_authorshare_pre');
	$post = get_option('wz_authorshare_post');
	$decimals = get_option('wz_authorshare_decimals');
	$publicusers = get_option('wz_authorshare_publicusers');
	$showall = get_option('wz_authorshare_showall');

	echo '<div class="wrap" id="main_page"><h2>' . __('Author Share Percentages', 'author-share') . '</h2>';
	echo '<p>' . __('The following are the percentages that each author comprises in the published article databases. For example, if there is 10 total posts, and you have 1 post, you would be at 10%. You (bold) are always visable, but an administrator has to set other users\' scores to public before you will be able to see them.', 'author-share') . '</p>';

	echo('<strong>'. wz_authorshare($user_ID,'You: ','%',$decimals) .'</strong><BR /><BR />');

	wz_authorshare_top10(' ','<Br />',$decimals,$showall,implode(',',$publicusers));

	echo '</div>';
	
 if(current_user_can('Edit Authorshare')) {
 	echo '<div class="wrap" id="main_page"><h2>' . __('Author Share Administration', 'author-share') . '</h2>';
	echo '<p>See the below config interface for easy-to-change options for general use. For the more complicated, here are some template functions:<BR /><BR />
	
<strong>wz_authorshare</strong> is a function designed to be used on its own; some people place it in author bios as a quick look at how much this user has contributed. The first option (the UserID) is required, the three trailing are optional; Usage:<BR /><BR />

<code>wz_authorshare(USER_ID[,"Preface Text","Postface Text", DECIMALS]);</code><BR />
eg:<BR />
<code>wz_authorshare(2,"Your Percentage:"," &#60;BR />", 2);</code><BR />
or<BR />
<code>wz_authorshare(2);</code><BR /><Br />

<strong>wz_authorshare_top10</strong> is a method of displaying a top-10-style list, similar to that most probably shown at the top of this page currently. This can be placed into your sidebar or a user bio, about page, or anywhere you can squeeze the appropriate code. By default this function will use the values entered into the form below, but you can override these.<BR /><BR />

The "ShowAll" variable is how many entries you would like in your Top-X list. If you set this to 0, you are expected to fill out PublicUserIDS, which is a comma-seperated string of all the user IDs you would like displayed. Usage:<BR /><BR />

<code>wz_authorshare_top10("Preface Text","Postface Text",DECIMALS,ShowAll,PublicUserIDs);</code><BR />
eg:<BR />
<code>wz_authorshare_top10();</code><BR />
or:<br />
<code>wz_authorshare_top10(" "," &#60;BR />", 2, 15);</code><BR />
or:<BR />
<code>wz_authorshare_top10("&#60;li> "," &#60;/li>", 2, 0, "1,2,3,5,9");</code><BR /><BR />

Any questions, comments, bug reports, or feature requests? <a href="mailto:weasel@thedailyblitz.org">Mail the author</a> or <a href="http://www.thedailyblitz.org/weasels-authorshare-plugin">visit the website</a>.</p>';

 ?> <form method="post">
    <fieldset name="wz_authorshare_pre">
	<legend>Preface text:</legend>
	<input type="text" value="<?php echo $pre; ?>" name="wz_authorshare_pre">
     </fieldset>
     <fieldset name="wz_authorshare_post">
	<legend>Postface text:</legend>
	<input type="text" value="<?php echo $post; ?>" name="wz_authorshare_post">
     </fieldset>
	 <fieldset name="wz_authorshare_decimals">
	 <legend>Decimal Places:</legend>
	 <input type="text" value="<?php echo $decimals; ?>" name="wz_authorshare_decimals">
	 </fieldset>
	 <fieldset name="wz_authorshare_showall">
	 <legend>Show the top _ list: (enter "0" to list specific authors below)</legend>
	 <input type="text" value="<?php echo $showall; ?>" name="wz_authorshare_showall">
	 </fieldset>
	 <fieldset name="wz_authorshare_publicusers">
	 <legend>Public Users (comma seperated user IDs): (only works if "0" entered above)</legend>
	 <input type="text" value="<?php echo implode(',',$publicusers); ?>" name="wz_authorshare_publicusers">
	 </fieldset>	 
	<div class="submit">
	  <input type="submit" name="info_update" value="Update Options" /></div>
  </form>
<?php
	echo '</div>';
 }
}

function wz_authorshare_percent($who) { // ### this returns the straight percentage from the DB
 global $wpdb;

 $totalentries = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post'");

 if ($totalentries < 1) return(0); else
   if ($who < 1) return(0); else {
     $whocount = get_usernumposts($who);
     return($whocount / $totalentries * 100);
   } 
}

function wz_authorshare_go($who, $pre, $post, $decimals) { // ### This grabs the percentage shares and returns it (with pre/post)
  $percent = wz_authorshare_percent($who);
  return $pre . number_format($percent,$decimals) . $post;
}

function wz_authorshare($who,$pre='',$post='',$decimals) { // ### This will define default values if none are supplied
	if (!$pre) $pre=get_option('wz_authorshare_pre');
	if (!$post) $post=get_option('wz_authorshare_post');
	return(wz_authorshare_go($who,$pre,$post, $decimals));
}

function wz_authorshare_menu_hook() { // ### Simply adds the submenu to the USERS page
	add_submenu_page('profile.php','Author Share','Author Share','View Authorshare','wz_authorshare_menu','wz_authorshare_menu');
}

add_action('admin_menu','wz_authorshare_menu_hook'); // ### hooks the menu into the admin interface

add_filter('capabilities_list','wz_authorshare_addcapability'); // ### Integrates a role with Red Alt's Role-Manager plugin
function wz_authorshare_addcapability($cap) {
	$cap[] = 'View Authorshare';
	$cap[] = 'Edit Authorshare';
	return $cap;
}

?>