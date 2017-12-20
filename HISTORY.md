## Changelog ##

### v5.6.0 - (In Development) ###

- New Features
	- Added the ability to delete posts based on custom post status.
	- Added the ability to filter delete users based on post count.

- Enhancements
	- Now works in PHP version from 5.2 to 7.2

### 2017-11-07 - v5.5.7  ###

- Enhancements
	- Improved the UI by removing all unnecessary sidebars.

### 2017-07-06 - v5.5.6 - (Dev time: 1 hour) ###

- New Features
	- Ability to delete users who have empty value in a user meta field.

- Enhancements
	- Added custom taxonomies in System Info.

### 2017-01-28 - v5.5.5 - (Dev time: 1 hour) ###

- Bug Fixes
	- Fixed a bug that caused Jetpack messages to be not deleted properly.

### 2016-02-13 - v5.5.4 - (Dev time: 5 hours) ###

- Bug Fixes
	- Security fix that prevents non-privileged users from deleting posts.

- Enhancements
	- Lot of code refactoring to improve quality.

### 2015-11-02 - v5.5.3 - (Dev time: 3 hours) ###
- New Features
	- Added the ability to delete users based on their registered date. (Issue #115)

- Enhancements
	- Sanitize action post field before using it.
	- Updated Screenshots that are linked in the readme.

- Bug Fixes
	- Fixed a bug that caused a warning while updating addons. (Issue #113)
	- Fixed typos and enhanced labels.

### 2015-10-05 - v5.5.2 (Dev time: 2 hours) ###
- New Features
	- Added the ability to delete users who have never logged in.

- Enhancements
	- Added compatibility with "Advanced Custom Fields Pro" plugin.

- Bug Fixes
	- Fixed issue in deleting posts by category

### 2015-08-15 - v5.5.1 (Dev time: 2.5 hours) ###
- New Features
	- Added actions that are executed before and after a query is executed.
	- Added actions that are executed before and after scripts and styles are enqueued.

- Enhancements
	- Added compatibility with "The Events Calendar" plugin.
	- Added compatibility with "WooCommerce" plugin. (Issue #111)
	- Display warning in the System Info page if certain required options are disabled. (Issue #106)
	- Added information about "WP_CRON_LOCK_TIMEOUT" to System Info

### 2015-07-21 - v5.5 (Dev time: 50 hours) ###
- New Features
    - Added the ability to delete users based on user meta. (Issue #79)
    - Improved the UI of dropdowns. (Issue #101)
    - (Addon) Added the ability to delete attachments. (Issue #98)

- Enhancements
    - Tweaked the code that generates the UI and lot of hidden improvements.
    - Tweaked the code that retrieves Mysql version. (Issue #102)
    - Tweaked the license handling code. (Issue #92)
    - Tweaked the build process
    - Use compressed JS and CSS files for better performance. (Issue #62)

### 2015-03-03 - v5.4.2 (Dev time: 5.0 hours) ###
- Tweak: Improve performance of DB queries to prevent timeouts. (Issue #93)
- Tweak: Add details about different post types in system info. (Issue #100)
- Tweak: Add details about timezone settings in system info. (Issue #100)

### 2015-02-14 - v5.4.1 - (Dev time: 0.5 hours) ###
- Tweak: Use Google CDN for jQuery UI CSS, instead of ASP.NET

### 2014-09-14 - v5.4 - (Dev time: 20 hours) ###
- New: Added the ability to delete post meta fields (Issue #43)
- New: Added the ability to delete comment meta fields (Issue #70)
- New: Added the ability to delete user meta fields (Issue #87)
- New: (Addon) Added the ability to delete posts based on attachment
- New: Added the ability to hook into JavaScript message, date picker and validation (Issue 82, 83, 84)
- New: Prevent PHP from timing out while performing bulk operations(Issue #81)

- Tweak: Group memory related info together in system info output
- Tweak: Tweak the warning and error messages that are shown to the users
- Tweak: Remove hard dependency on "Bulk Delete From Trash" addon in code
- Tweak: Tweak the admin UI for WordPress 4.0 and added custom plugin icons
- Tweak: Tweak the code that handles automatic update of addons

### 2014-08-17 - v5.3 - (Dev time: 17 hours) ###
- New: Ability to delete Jetpack Contact Form Messages (Issue #72)
- New: New Addon to send email whenever a Bulk WP Scheduler runs
- New: Settings screen for addons (Issue #78)
- New: Add setting helper functions for addons

- Tweak: Tweak the names of the menu items (Issue #73)
- Tweak: Add information about `DISABLE_WP_CRON` in system info
- Tweak: Tweak labels in Scheduled Jobs page (Issue #71)
- Tweak: Removed unused variable
- Tweak: Removed old compatibility code for `The Events Calendar` plugin
- Tweak: Add filters to extend menu items (Issue #74)
- Tweak: Add filters to extend meta boxes in each page (Issue #75)
- Tweak: Remove `upgraded from` from system info (Issue #77)

- Bug: Fixed a warning that happened because of duplicate call to `add_meta_boxes`

### 2014-07-03 - v5.2 - (Dev time: 8 hours) ###
- New: Ability to delete users in batches (Issue #47)
- New: A new addon to delete posts based on users (Issue #6)

### 2014-06-14 - v5.1 - (Dev time: 8 hours) ###
- New: Added the "Delete posts from trash" addon (Issue #65)

- Tweak: Added `EMPTY_TRASH_DAYS` to system info page (Issue #67)
- Tweak: Change the contextual help content for admin screens (Issue #68)

- Bug: Added compatibility for PHP version 5.2.4 (Issue #66)
- Bug: Fixed a bug in JavaScript validation in "Delete by URL" module (Issue #69)

### 2014-06-12 - v5.0.2 - (Dev time: 1 hours) ###
- Bug: Added compatibility for PHP version 5.2.4 (Issue #64)

### 2014-06-10 - v5.0.1 - (Dev time: 1 hours) ###
- Fix: Deleting users had as issue that was introduced in v5.0

### 2014-06-10 - v5.0 - (Dev time: 60 hours) ###
- New: Add the ability to delete posts by duplicate title (#56)
- New: Add a new page that displays system information for debugging
- New: Add the ability to handle addon license
- New: Use `add_settings_error` method to display information to users

- Tweak: Make Bulk_Delete class singleton
- Tweak: Move all deprecated functions and code to a separate file
- Tweak: Add the ability to filter text displayed in admin footer
- Tweak: Change the menu text for Schedule page
- Tweak: Move delete page modules to a separate page
- Tweak: Refactored the way request was handled
- Tweak: Update screenshots
- Tweak: Handle expired license properly

### 2014-01-26 - v4.4.3 - (Dev time: 1.5 hours) ###
- Tweak: Ability to delete posts from non-public post types as well
- Fix: Fix the height of the sidebar

### 2014-01-05 - v4.4.2 - (Dev time: 1.5 hours) ###
- Tweak: Move request processing code for deleting by custom field to addon
- Fix: Deleting first x posts deletes all posts while deleting by category (#44)
- Fix: Posts are moved to trash even if "Delete permanently" option is selected (#45)

### 2013-12-18 - v4.4.1 - (Dev time: 0.5 hours) ###
- Fix: Bulk Delete menu overrides other menus at the same position

### 2013-12-14 - v4.4 - (Dev time: 10 hours) ###
- New: Ability to delete all published posts from "Post Status" module
- New: Ability to delete all sticky posts from "Post Status" module
- New: Ability to delete posts by title
- Tweak: Moved all option page to a separate top menu
- Tweak: Tweak UI for WordPress 3.8
- Fix: Fix undefined notices and strict warnings

### 2013-12-08 - v4.3 - (Dev time: 2 hours) ###
- New: Ability to delete custom post type posts by categories
- New: Ability to delete custom post type posts by custom taxonomy
- Fix: Fix link to "Custom Field" Addon

### 2013-11-11 - v4.2.2 - (Dev time: 1 hour) ###
- Fix: Bug in deleting custom post types with hypen

### 2013-11-07 - v4.2.1 - (Dev time: 0.5 hours) ###
- Explicitly mark static methods as static

### 2013-10-22 - v4.2 - (Dev time: 3 hours) ###
- Add the ability to custom post type posts by post status

### 2013-10-12 - v4.1 - (Dev time: 6 hours) ###
- Add the "delete by custom field" pro addon

### 2013-10-07 - v4.0.2 - (Dev time: 1 hours) ###
- Fix issue in displaying meta boxes
- Show taxonomy label instead of slug
- Fix issue in deleting posts by custom taxonomy

### 2013-09-12 - v4.0.1 - (Dev time: 1 hours) ###
- Fix JavaScript bug that prevented deleting posts by days and in batches

### 2013-09-09 - v4.0 - (Dev time: 25 hours) ###
- Add the ability to delete users
- Move menu items under tools

### 2013-07-07 - v3.6.0 - (Dev time: 2 hours) ###
- Change minimum requirement to WordPress 3.3
- Fix compatibility issues with "The event calendar" Plugin

### 2013-06-01 - v3.5 - (Dev time: 10 hours) ###
- Added support to delete custom post types
- Added Gujarati translations
- Ignore sticky posts when deleting drafts

### 2013-05-22 - v3.4 - (Dev time: 20 hours) ###
* Incorporated Screen API to select/deselect different sections of the page
* Load only sections that are selected by the user

### 2013-05-11 - v3.3 - (Dev time: 10 hours) ###
* Enhanced the deletion of posts using custom taxonomies
* Added the ability to schedule auto delete of taxonomies by date
* Cleaned up all messages that are shown to the user
* Added on screen help tab

### 2013-05-04 - v3.2 - (Dev time: 20 hours) ###
* Added support for scheduling auto delete of pages
* Added support for scheduling auto delete of drafts
* Fixed issue in deleting post revisions
* Move post revisions to a separate section
* Better handling of post count to improve performance
* Moved pages to a separate section
* Added ability to delete pages in different status
* Added the option to schedule auto delete of tags by date
* Fixed a bug which was not allowing categories to be deleted based on date

### 2013-04-28 - v3.1 - (Dev time: 5 hours) ###
* Added separate delete by sections for pages, drafts and urls
* Added the option to delete by date for drafts, revisions, future posts etc
* Added the option to delete by date for pages

### 2013-04-27 - v3.0 - (Dev time: 10 hours) ###
* Added support for pro addons
* Added GUI to see cron jobs

### v2.2.2 (2012-12-20) (Dev time: 0.5 hour) ###
* Removed unused wpdb->prepare() function calls

### v2.2.1 (2012-10-28) (Dev time: 0.5 hour) ###
* Added Serbian translations

### v2.2 (2012-07-11) (Dev time: 0.5 hour) ###
*   Added Hindi translations
*   Added checks to see if elements are present in the array before accessing them.

### v2.1 (2012-04-07) Dev Time:  1 hour ###
*   Fixed CSS issues in IE
*   Added Lithuanian translations

### v2.0 (2012-04-01) Dev Time:  10 hours ###
*   Fixed a major issue in how dates were handled.
*   Major UI revamp
*   Added debug information and support urls

### v1.9 (2012-03-16) ###
*   Added support for deleting by permalink. Credit Martin Capodici
*   Fixed issues with translations
*   Added Russian translations

### v1.8 (2012-01-31) ###
*   Added roles and capabilities for menu

### v1.7 (2012-01-12) ###
*   Added Bulgarian translations

### v1.6 (2011-11-28) ###
*   Added Italian translations

### v1.5 (2011-11-13) ###
*   Added Spanish translations

### v1.4 (2011-08-25) ###
*   Added Turkish translations

### v1.3 (2011-05-11) ###
*   Added German translations

### v1.2 (2011-02-06) ###
*   Added some optimization to handle huge number of posts in underpowered servers

### v1.1 (2011-01-22) ###
*   Added support to delete posts by custom taxonomies
*   Added Dutch Translation
*   Added Brazilian Portuguese Translation

### v1.0 (2010-06-19) ###
*   Proper handling of limits.

### v0.8 (2010-03-17) ###
*   Added support for private posts.

### v0.7 (2010-02-21) ###
*   Added an option to delete posts directly or send them to trash.
*   Added support for translation.

### v0.6 (2009-07-22) ###
*   Added option to delete all scheduled posts.

### v0.5 (2009-07-21) ###
*   Added option to delete all pending posts.

### v0.4 (2009-07-05) ###
*   Added option to delete by date.

### v0.3 (2009-04-05) ###
*   Prevented drafts from deleted when only posts are selected

### v0.2 (2009-02-03) ###
*   Fixed issues with paging

### v0.1 (2009-02-02) ###
*   First version

## Upgrade Notice ##

### 5.5.7 ###
Improved the UI by removing all unnecessary sidebars.

### 5.5.5 ###
Fixed a bug that caused Jetpack messages to be not deleted properly

### 5.5.4 ###
Fixed a security bug that allowed non-privileged users to delete posts

### 5.5.3 ###
Added the ability to delete users based on registration date

### 5.5.2 ###
Added the ability to delete users who have never logged in

### 5.5.1 ###
Fixed compatibility issues with WooCommerce and The Event Calendar plugins

### 5.5 ###
Added the ability to delete users based on user meta and lot of UI improvement

### 5.4.1 ###
Changed jQuery UI CSS CDN to Google CDN from ASP.NET, which seems to be discontinued

### 5.4 ###
Ability to delete post, comment and user meta fields

### 5.3 ###
Ability to delete Jetpack Contact Form messages

### 5.2 ###
Ability to delete users in batches and a new addon to delete posts based on users

### 5.1 ###
Added the ability to delete posts and pages from trash

### 5.0.2 ###
Added compatibility for PHP version 5.2.4

### 5.0.1 ###
Fix delete users. Note: This version is only compatible with addons above v0.5

### 5.0 ###
This version is only compatible with addons above v0.5

### 4.4.1 ###
Fix: Prevent Bulk Delete from overriding other menus

### 4.2.2 ###
Fix: Bug in deleting custom post types with hypen

### 4.2.1 ###
Fix warning message in PHP 5.2.x

### 4.2 ###
Add the ability to custom post type posts by post status

### 4.0.2 ###
Fixed issue in deleting posts by custom taxonomy

### 4.0.1 ###
Fixed JS bug that was introduced in v4.0

### 4.0 ###
Add the ability to delete users

### 3.6.0 ###
Fix compatibility issues with "The event calendar" Plugin

### 3.5 ###
Added the ability to delete posts by custom post types.

### 3.4 ###
Added the ability to disable different sections of the Plugin.

### 3.3 ###
Fixed issues in deleting posts using custom taxonomy

### 3.2 ###
Fixed bugs in handling post revisions and dates in categories. Added more options to delete pages.

### 3.1 ###
Added the option to delete by date for pages, drafts, revisions, future posts etc
