=== Weasel's Authorshare ===
Contributors: weasello
Donate link: 
Tags: author, tools, share, analysis, percentage
Requires at least: 2.0.2
Tested up to: 2.3.1
Stable tag: 2.0

A plugin that allows you to display a percentage share of the published database per author, with many display options.

== Description ==

A plugin that allows you to display a percentage share of the published database per author. Strongly recommend <a href="http://www.im-web-gefunden.de/wordpress-plugins/role-manager/">Role Manager</a> plugin for full functionality.

This plugin should be completely self contained and work totally on its own without source editing. It will add an admin panel named "Author Share" underneath the USERS tab, and will create two new user capabilities to Red Alt's User Roles plugin.If you do not have this plugin, you can manually call the percentage generating function like so:

`wz_authorshare(USER_ID,[preface override],[postface override]);`

or

`wz_authorshare_top10(PRE, POST, DECIMALS, SHOWALL, PUBLICUSERS);`

(see installation section for parameter descriptions)

== Installation ==

1. Upload `wz_authorshare.php` or it's containing directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. View the plugin view the admin menu under the "Users" tab, subheading "Author Share"
3. (optional) Place a single-user template tag. This displays the specified user's share of the database:

     `<?php wz_authorshare(USER_ID,PREFACE,POSTFACE); ?>`

   Where:
    - USER_ID is the numerical user ID of the person you wish to show the percentage for
    - PREFACE (optional) is any HTML or text you would like inserted before the percentage, and
    - POSTFACE (optional) is HTML or text you would like to be inserted after the percentage.

   Example:
      `<?php wz_authorshare(2,'<li>','%</li>'); ?>`
      would output
       - 22%

4. (optional) Place a top-ten style list that displays a nice ranking display:

      `<?php wz_authorshare_top10(PRE, POST, DECIMALS, SHOWALL, PUBLICUSERS); ?>`

   Where:
    - PRE is text/HTML to precede the line of generated text. EG: '<li>'
    - POST is text/HTML to follow each line of generated text. EG: '</li>'
    - DECIMALS is number of decimal places to find a percentage to. EG: 2
    - SHOWALL is set to 0 to utilize below. otherwise, this figure denotes the top-X list size. EG: 10
    - PUBLICUSERS is a list of comma seperated user IDs to display - if this is to be used, SHOWALL must be
      zero. EG: '2,4,6,7,14'

== Frequently Asked Questions ==

= What is the difference between version 1.5 and 2.0? =

Resolved an error with total postcounts greater than 1000; updated documentation

== Screenshots ==

1. This is a screenshot of the top-10 interface as seen in the administration control panel.