## Changelog ##

### 2019-04-10 - v6.0.2 ###

Enhancements
- Show Bulk WP menu to only administrators.
- Make Delete Comment Meta scheduler more reliable.
- Tweak the message that is shown when a cron job is manually run.

### 2019-04-09 - v6.0.1 ###

New Features

- Added the ability to choose post status in addition to post type while deleting meta fields.

Enhancements

- Enhanced warning and error messages.
- Enhanced the taxonomy dropdown by grouping built-in and custom taxonomies.
- Enhanced UI for scheduling deletion.

### 2019-02-22 - v6.0.0 (10th Anniversary release) ###

New Features

- Added the ability to delete taxonomy terms based on name.
- Added the ability to delete taxonomy terms based on post count.
- Added the ability to delete posts based on comment count.
- Added the ability to delete users who don't belong to any role (no role).
- Added the ability to reassign posts of a user who is going to be deleted to another user before deletion.
- Added the ability to unstick sticky posts.
- Added support for custom post status.
- Added the ability to delete comment meta based on both meta key and value.
- Complete rewrite of the way deletion is handled to improve performance.

Enhancements

- Load all 3rd party library js and css locally and not from CDN. The plugin can work fully in offline mode.
- Introduced a filter to exclude certain posts or users from getting deleted.
- Display schedule label instead of slug in scheduled jobs list table.
- Lot of UI/UX improvements.
- Fully compatible with from PHP 5.3 to 7.3.
- Fully compatible with Gutenberg.

### 2018-01-29 - v5.6.1 ###

- New Features
	- Added the ability to delete users based on partial user meta values.

- Enhancements
	- Fixed a typo in filter text.

### 2017-12-28 - v5.6.0 ###

- New Features
	- Added the ability to delete posts based on custom post status.
	- Added the ability to filter delete users based on post count.
	- Added the ability to filter the deletion of Jetpack contact messages using various filters.

- Enhancements
	- Now works in PHP version from 5.2 to 7.2

### Old Releases ###

We have made more than 50 releases in the last 10 years. You can read the changelog of all the old releases at [https://bulkwp.com/bulk-delete-changelog/](https://bulkwp.com/bulk-delete-changelog/)

## Upgrade Notice ##

### 6.0.1 ###
Added the ability to choose post status in addition to post type while deleting meta fields.

### 6.0.0 ###
Added the ability to delete taxonomy terms and lot of new features.

### 5.6.1 ###
Added the ability to delete users based on partial user meta values.

### 5.6.0 ###
Added the ability to delete posts based on custom post status
