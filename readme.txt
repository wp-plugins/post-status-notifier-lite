=== Post Status Notifier Lite ===
Tags: post, status, notification, notify, change, custom post type, email, log, logging, notify, placeholders,  transition
Contributors: worschtebrot
Requires at least: 3.3
Tested up to: 4.0
Stable tag: 1.5.1


Lets you create individual notification rules to be informed about all post status transitions of your blog. Features custom email texts with many placeholders and custom post types.

== Description ==

= Notify everything! =

You want to **be notified** when one of your contributors have submitted a new post for revision or an editor published one? Vice versa you want to **notify your contributors** when their posts got published?
This is just the beginning of what you can achieve with Post Status Notifier (PSN)!

It works with all kind of **custom post types**, supports all **custom taxonomies** like categories and tags other plugins are using. You can grab all these taxonomy values and custom fields attached to a post and use them as **placeholders** in your custom notification texts. Define as many notification rules as you need with all kind of settings, like custom Cc, Bcc and From emails addresses.

PSN works great with plugins like **WP Job Manager**, **Crowdfunding by Astoundify** or **Advanced Custom Fields**, just to name a few. The possibilities are endless. Want to **automate your publishing workflow** with [Buffer](http://bufferapp.com/)? No problem!

Plugin homepage:
http://www.ifeelweb.de/wp-plugins/post-status-notifier/

Always up-to-date online documentation:
http://docs.ifeelweb.de/post-status-notifier/

FAQ:
http://docs.ifeelweb.de/post-status-notifier/faq.html

= Features =

Get the [Premium version](http://codecanyon.net/item/post-status-notifier/4809420?ref=ifeelweb) for all features

* Define **custom notification rules**
* Support for posts, pages and all **custom post types**
* Support for **all post status** values
* Create **custom email texts** with support for many placeholders
* Manipulate placeholders content with **filters** to completely adjust the output to your needs (uses the filters of the famous PHP template engine Twig / limited to one filter in the Lite version)
* WordPress **multisite compatible**
* Premium version: HTML emails / mail templates
* Premium version: Categories filter: Include or exclude categories (even from custom post types) from notifications
* Premium version: Supports **SMTP**. You find all necessary SMTP settings to connect your SMTP server in the options section.
* Premium version: Supports **user roles** (custom roles too) as email recipients
* Premium version: Optional **logging**: Logs status changes based on your rules
* Premium version: **Dashboard widget** showing the latest log entries (can be disabled)
* Premium version: **Import / Export** of your notification rules
* Premium version: **Copy** rules
* Premium version: Custom sender e-mail. Define the notification sender (**FROM**) per rule or as a default in the options.
* Comprehensive **documentation**
* Included **translations**: english, german
* **Support** in english and german via Zendesk: ifeelwebde.zendesk.com
* Tested on Windows, Mac OS and Linux
* Built on our ifeelweb.de WordPress Plugin Framework
* The Lite version features two notification rules and one CC email


= What customers say =

**"Great plugin, look through maybe 7 plugins until found this one and it is the best."**
- misolek

**"just got the pro version and it’s working great, awesome plugin man and thanks for your excellent support"**
- nomadone

"This plugin is very intuitive and works great. Very helpful support. Top notch!"
- Rick

**"Thank you for your great support – the plugin works great now and has accomplished what 5 other commercial and free plugins couldn’t – to provide simple and configurable email notifications for WP status changes."**
- Jon

**"just got the pro version and it’s working great, awesome plugin man and thanks for your excellent support"**
- nomadone

[Comment-Source](http://codecanyon.net/item/post-status-notifier/discussion/4809420)


== Installation ==

Just unpack the `post-status-notifier-lite` folder into your plugins directory and activate it on your wordpress plugins page.
Then you will have the option `Post Status Notifier Lite` on your wordpress options page.


== Configuration ==

Go to the new option page `Post Status Notifier Lite`. Here you can define custom notification rules.

Here you can find a detailed documentation:

http://docs.ifeelweb.de/post-status-notifier/

== Change Log ==

= 1.5.1 =

- Bugfix: In some cases the service section could produce an error message (Call to undefined function apache_get_version())

= 1.5 =

- New feature: HTML mail support and email templates. Prepare your email templates once and select them for different notification rules.
- New feature: Auto-update via WordPress backend. Never have to upload the files via FTP again. You have to enter your license code in the plugin's settings.
- New feature: More flexible To, Cc, Bcc selection. Multiple selections are possible now.
- New feature: Editor restriction. Select one or more roles the editor of a post must be member of so that the notification will be generated.
- New feature: Recipients lists. Manage email addresses without the need to create user accounts.
- New custom post status "Not pending": Matches all post status values except "pending"
- New custom post status "Not private": Matches all post status values except "private"
- New placeholder [post_editlink]: Contains the backend edit URL
- Removed post types "attachement", "nav_menu_item" from rule settings as they are not treated like post types (have no status before/after)
- Support for placeholders in FROM
- Refactoring for percormance improvements

= 1.4 = 

- New custom placeholders which will specifically match custom categories and tags registered with your blog.
- New dynamic placeholders: You will be able to fetch every custom field attached with your posts.
- New feature: Placeholder filters. This is a very powerful feature. You can use all filters of the famous PHP template engine Twig to manipulate the output of all placeholders PSN offers you, including the new dynamic placeholders. (Limited to 1 filter in Lite version)
- New feature: Import / Export notification rules (Premium)
- New feature: Copy notification rules (Premium)
- New feature: New recipient type "Individual e-mail". Enter a custom e-mail address as main recipient (TO).
- New feature: Custom sender e-mail. Define the notification sender per rule or as a default in the options. (Premium)
- New notification rule status "Not published". This will match every post status but "publish".
- New placeholder: [post_format]

= 1.3 =

- New feature: Notification rules have a categories filter now
- New placeholder: [post_permalink] can be used for notification texts. Contains the post's permalink (uses WP internal get_permalink function)
- Bugfix: Fixed a bug which occured when not logged in users changed post status in the frontend
- Bugfix: German language fix
- Improvement: Backend adjusted to new WordPress 3.8 layout

= 1.2.1 =

- Bugfix: Fixed a bug where scheduled items did not get notified when published by cron

= 1.2 =

* New feature: Notification rule recipient supports user roles (default and custom roles) and special all users
* Improvement: PSN now is completely multisite compatible
* Bugfix: Single quotes in blog name will be shown correctly now

= 1.1 =

* New feature: Bcc field. Set Bcc recipients for your notification rules.
* New feature: SMTP mode. If you want to send many notifications and have a SMTP mail server, PSN now supports it. You find all necessary SMTP settings in the options section.
* New feature: Plugin selftester. The plugin ships with some selftesting routines you can trigger manually in the plugin dashboard.
* Minor bugfixing: Now fully compatible with Windows Server 2008 / PHP 5.2

= 1.0.5 =

* Bugfix in date/time calculation (PHP5.2)

= 1.0.4 =

* Minor bugfixes

= 1.0.3 =

* Further improvements: Removed dependency to PDO at all

= 1.0.2 =

* Bugfix: Recipient “Post author” did not work for notification rules
* Bugfix: Plugin activation could produce error with PHP 5.2 (Parse error: syntax error, unexpected T_PAAMAYIM_NEKUDOTAYIM …)

= 1.0.1 =

* Removed dependency to PHP pdo_mysql (framework database models now work with native wpdb object)
* Improved backwards compatibility up to WP 3.3 (tested on 3.3.x / 3.4.x / 3.5.x)
* Adjusted log timestamp format to blog date/time settings


== Info ==

If you find any bugs please use the comments on the [plugin's homepage](http://www.ifeelweb.de/contact/). Please also contact me for feature requests and ideas how to improve this plugin. Any other reactions are welcome too of course.

== Frequently Asked Questions ==



== Screenshots ==

1. Use case 1: You host a blog with several authors and you want to be informed when a new post is ready for review.
2. Use case 2: This rule sends an email to the author of a post when it got published.
3. Use case 3: This rule is for blog admins who want to be informed about every single post status change.
4. Use case 4: Use with Buffer (http://bufferapp.com)
5. Form to create a new notification rule
6. List of placeholders
7. Buttons to create example rules
8. Options (Logger is a Premium feature)
9. Premium: List of log entries 
10. Premium: Dashboard widget of log entries
11. Example email generated by the plugin
12. Overview screen
13. Selftest feature
14. Example HTML email (Premium feature)
15. German translation

