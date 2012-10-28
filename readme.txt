=== Bulk Delete ===
Contributors: sudar 
Tags: post, comment, delete, bulk, draft, revision, page
Requires at least: 2.0
Tested up to: 3.4.2
Donate Link: http://sudarmuthu.com/if-you-wanna-thank-me
Stable tag: 2.2.1
	
Bulk delete posts from selected categories or tags

== Description ==

Bulk Delete is a WordPress Plugin which can be used to delete posts in bulk from selected categories, tags or custom taxonomies. This Plugin can also delete all drafts, post revisions or pages.

More details about the Plugin can be found at the [Plugins Home page][1].

If you looking for just moving posts, instead of deleting, then use [Bulk Move Plugin][2] instead.

### Translation

*   Dutch (Thanks Rene of [WordPress WPwebshop][3])
*   Brazilian Portuguese (Thanks Marcelo of [Criacao de Sites em Ribeirao Preto][4])
*   German (Thanks Jenny Beelens of [professionaltranslation.com][8])
*   Turkish Portuguese (Thanks [Bahadir Yildiz][9])
*   Spanish (Thanks Brian Flores of [InMotion Hosting][10])
*   Italian (Thanks Paolo Gabrielli)
*   Bulgarian (Thanks Nikolay Nikolov of [Skype Fan Blog][11])
*   Russian (Thanks Maxim Pesteev, [Weblancer.net profile][12])
*   Lithuanian (Thanks Vincent G from [http://www.host1free.com][13])
*   Hindi (Thanks Love Chandel)
*   Serbian (Thanks Diana)

### Support

Support for the Plugin is available from the [Plugins home page][1]. If you have any questions or suggestions, do leave a comment there or contact me in [twitter][5].

### Links

*   [Plugin home page][1]
*   [Author's Blog][6]
*   [Other Plugins by the author][7]

 [1]: http://sudarmuthu.com/wordpress/bulk-delete
 [2]: http://sudarmuthu.com/wordpress/bulk-move
 [3]: http://wpwebshop.com/premium-wordpress-plugins/
 [4]: http://www.techload.com.br/
 [5]: http://twitter.com/sudarmuthu
 [6]: http://sudarmuthu.com/blog
 [7]: http://sudarmuthu.com/wordpress
 [8]: http://www.professionaltranslation.com
 [9]: http://www.matematik.us
 [10]: http://www.inmotionhosting.com/
 [11]: http://en.chat4o.com/ 
 [12]: http://www.weblancer.net/users/Kirky/
 [13]: http://www.host1free.com

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

== Frequently Asked Questions ==

= After installing the Plugin, I just see a blank page. =

This can happen if you have huge number of posts and your server is very underpowered. Check your PHP error log to see if there are any errors and correct them. The most common problems are script timeout or running out of memory. Change your PHP.ini file and increase the script timeout and/or amount of memory used by PHP process.

In particular try to change the following settings

*   max_execution_time = 600 ; Maximum execution time of each script, in seconds
*   max_input_time = 30; Maximum amount of time each script may spend parsing request data
*   memory_limit = 256M ; Maximum amount of memory a script may consume (8MB)

Additional FAQ and troubleshooting guide can be found in the [Plugins homepage](http://sudarmuthu.com/bulk-delete)

== Screenshot ==

1. Delete posts based on type

2. Delete posts based on date, post visibility or choose to move them to trash or delete permanently

3. Delete Posts by Categories or Tags

4. Delete Posts by Custom taxonomies

== Changelog ==

###v0.1 (2009-02-02)

*   first version

###v0.2 (2009-02-03)

*   Fixed issues with paging

###v0.3 (2009-04-05)

*   Prevented drafts from deleted when only posts are selected

###v0.4 (2009-07-05)

*   Added option to delete by date.

###v0.5 (2009-07-21)
*   Added option to delete all pending posts.

###v0.6 (2009-07-22)
*   Added option to delete all scheduled posts.

###v0.7 (2010-02-21)
*   Added an option to delete posts directly or send them to trash.
*   Added support for translation.

###v0.8 (2010-03-17)
*   Added support for private posts.

###v1.0 (2010-06-19)
*   Proper handling of limits.

###v1.1 (2011-01-22)
*   Added support to delete posts by custom taxonomies
*   Added Dutch Translation
*   Added Brazilian Portuguese Translation

###v1.2 (2011-02-06)
*   Added some optimization to handle huge number of posts in underpowered servers

###v1.3 (2011-05-11)
*   Added German translations

###v1.4 (2011-08-25)
*   Added Turkish translations

###v1.5 (2011-11-13)
*   Added Spanish translations

###v1.6 (2011-11-28)
*   Added Italian translations

###v1.7 (2012-01-12)
*   Added Bulgarian translations

###v1.8 (2012-01-31)
*   Added roles and capabilities for menu

###v1.9 (2012-03-16)
*   Added support for deleting by permalink. Credit Martin Capodici
*   Fixed issues with translations
*   Added Russian translations

###v2.0 (2012-04-01) Dev Time:  10 hours
*   Fixed a major issue in how dates were handled.
*   Major UI revamp
*   Added debug information and support urls

###v2.1 (2012-04-07) Dev Time:  1 hour
*   Fixed CSS issues in IE
*   Added Lithuanian translations

### v2.2 (2012-07-11) (Dev time: 0.5 hour)
*   Added Hindi translations
*   Added checks to see if elements are present in the array before accessing them.

### v2.2.1 (2012-10-28) (Dev time: 0.5 hour)
* Added Serbian translations

==Readme Generator== 

This Readme file was generated using <a href = 'http://sudarmuthu.com/wordpress/wp-readme'>wp-readme</a>, which generates readme files for WordPress Plugins.
