## Changelog ##

### 2014-04-26 - v4.5 - (Dev time: 4.5 hours) ###
- New: Add the ability to delete posts by duplicate title (#56)
- Tweak: Make Bulk_Delete class singleton
- Tweak: Move all deprecated functions and code to a separate file
- New: Add a new page that displays system information for debugging
- Tweak: Add the ability to filter text displayed in admin footer

### 2014-01-26 - v4.4.3 - (Dev time: 1.5 hours) ###
- Tweak: Ability to delete posts from non-public post types as well
- Fix: Fix the height of the sidebar

### 2014-01-05 - v4.4.2 - (Dev time: 1.5 hours) ###
- Fix: Deleting first x posts deletes all posts while deleting by category (#44)
- Fix: Posts are moved to trash even if "Delete permanently" option is selected (#45)
- Tweak: Move request processing code for deleting by custom field to addon

### 2013-12-18 - v4.4.1 - (Dev time: 0.5 hours) ###
- Fix: Bulk Delete menu overrides other menus at the same position

### 2013-12-14 - v4.4 - (Dev time: 10 hours) ###
- New: Ability to delete all published posts from "Post Status" module
- New: Ability to delete all sticky posts from "Post Status" module
- Tweak: Moved all option page to a separate top menu
- Fix: Fix undefined notices and strict warnings
- New: Ability to delete posts by title
- Tweak: Tweak UI for WordPress 3.8

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
