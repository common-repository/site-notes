=== Site Notes ===
Contributors: KTC_88
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ZBN6VSE6UM4A
Tags: notes, pages, posts
Requires at least: 3.0.1
Tested up to: 6.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin that adds a note box to  your posts and pages which can be viewed in the admin bar

== Description ==

Save page and post notes in the editor or while logged in, add notes from the front end using the integrated notes button found in the admin bar.

= Features =
* Make page and post notes from both the front or back end (while logged in)
* Front end: Lock the note box open or closed on a per page basis so you can select which pages or posts show your notes automatically
* Front end: Movable note boxes
* Front end: Notes are saved using Ajax which means no refresh when you click save
* Front end: The positon of the note box saves automatically once you have finished moving it to your desired location (per page basis). 
* Front end: The note box is resizable, the dimentions are saved after each resize
* Front end: Save status appears after every save action so you know when your notes, size and position are being saved  
* Dashboard: Dashboard widget that will display all page/post notes with links to each page

= Future Updates =
* Record user who made note (Pro version)
* Create multiple notes/annotations on a single page for logged in users by role (Pro version)
* Assign users to notes (users receive notification of new notes) (Pro version)
* Make comments on notes (Pro version)

== Installation ==

1. Upload contents to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Plugin works upon activation, you will now see a notes box in posts and pages.

== Frequently Asked Questions ==

= Q: How do you add a note to the dashboard? =
A: Move your mouse to the right side of the "Dashboard Notes" widget title, you should see the word configure appear. Click configure to add or edit your dashboard note.

= Q: How do you move the note box in the admin bar? =
A: Move your mouse over the bottom of the note box with the darker background, then click and drag to desired position.

== Screenshots ==

1. Notes button on admin bar
2. Notes button on admin bar clicked 
3. Notes button indicating saved note
4. Notes box on page
5. To catch editors attention, notes box background turns yellow when there is a saved note 
6. Dashboard Notes widget
7. Dashboard Notes widget after you click configure

== Changelog ==

= 2.0.0 [09/27/2023] =
* Refactored code
* Cleaned up styling
* Removed all 3rd party libraries (jQuery, FontAwesome)
* Fixed bugs

= 1.6.0 =
* NEW: Added a checkbox to the "Saved Page/Post Notes" dashboard widget to toggle the Notes on the front end admin bar (https://wordpress.org/support/topic/hide-notes-from-admin-bar-in-front-end/)
* Tweak: The links in the "Saved Page/Post Notes" dashboard widget now opens in new tab

= 1.5.1 =
* Fixed: jQuery UI is now pulled in through HTTPS instead of HTTP to support sites using SSL

= 1.5 =
* Fixed: Added post notes to the dashboard note list, previously it was only showing page notes
* Tweak: Dashboard notes will now save as individual notes that can be removed on an individual basis
* Tweak: Hid front end notes button when not on pages or posts

= 1.4.2 =
* Fixed Undefined variable PHP errors

= 1.4.1 =
* Fixed a JavaScript error with the new auto height functionality which occured when not logged in. (A big thanks to Mike Jackson for reporting the bug and providing a fix!)
* Fixed: Moved a required file call outside of a function which has been causing some issues with the lock and save functionality

= 1.4.0 =
* Added auto height functionality to the note box textarea

= 1.3.0 =
* Added jQuery dependency to included javascript files (Thanks Sorbing!)
* NEW: Added dashboard widget to display all saved page/post notes 
* Minor CSS styling update to fix line hight of text in note box

= 1.2.1 =
* Fixed CSS on toggle button to keep the height within the admin bar

= 1.2 =
* Position and size of note box in front end automatically save on change
* Notes, note box size and note box postion saved using ajax
* Added status bar to show what has been saved
* Minor CSS changes for better theme support 

= 1.1 =
* Added Lock option to admin bar note box that will force the notes to remain visible on a per page basis
* Made note box draggable and expandable
* Fixed CSS/JS file path

= 1.0 =
* Initial release