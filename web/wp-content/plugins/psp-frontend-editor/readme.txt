=== Project Panorama Frontend Edit ===
Contributors: Ross Johnson
Tags: project, management, project management, basecamp, status, client, admin, intranet
Requires at least: 3.5.0
Tested up to: 4.7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create and edit projects on the front end.

== Description ==

Create and edit projects on the front end.

= Website =
http://www.projectpanorama.com

= Documentation =
http://www.projectpanorama.com/docs

= Bug Submission and Forum Support =
http://www.projectpanorama.com/support


== Installation ==

1. Upload 'psp-frontend-editor' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to your panorama dashboard, typically at http://www.yourdomain.com/panorama
4. You can create a new project, or click on an existing project and edit it


== Changelog ==

= 1.5.9 =
* Fixes issue with phantom input when no project calcuation is set
* Fixes issue trying to add a new phase to the beginning of phases

= 1.5.8.2 =
* Fixes bug with blank phase editing description after re-edit

= 1.5.8.1 =
* Renames status arg to project_status to prevent conflicts

= 1.5.8 =
* Adds hooks and filters to support upcoming add-on releases

= 1.5.7 =
* Fixes issue with task detail re-init after edit / add

= 1.5.6 =
* Better support for non-pretty permalinks
* UI updates for new modals

= 1.5.5 =
* Adds support for multiple users assigned per task

= 1.5.2 =
* BUG: Fixes issue where more than one project type doesn't load in the front end editor

= 1.5.1 =
* Creating a new task doesn't automatically close the modal, quicker rapid buildout
* Drag and drop phases
* Drag and drop tasks
* Better checking of project completion after edits
* Fixes issue where Firefox didn't always load front end editor

= 1.5.0 =
* Added support for adding and removing single phases from the front end
* Fixes issues with double sending notifications
* Updated UI for Panorama 2.0


= 1.3.9 =
* BUG: Fixed issue with cross threading tasks
* Enhancement: Added a loading indicator to edit / add task forms

= 1.3.8 =
* Supports task descriptions

= 1.3.6.2 =
* Better support for embedded projects
* BUG: Duplicate from template on front end

= 1.3.6.1 =
* Finesse on modal fade-out

= 1.3.6 =
* Added individual permissions for front end modifications (add task, delete task, edit task, edit phase, edit dates)
* Unset variable notice
* Fixes consistency issues with editing multiple phases and deleting tasks on the front end

= 1.3.2 =
* Minor bug fix around project title

= 1.3.1 =
* ACF5 upgrade
* Adds support for deleting documents on the front end
* Adds support for editing phase description and details from front end
* Adds support for setting and changing project dates from the front end

= 1.3 =
* Adds support for new task discussions and tasks

= 1.2.3 =
* Fixes issue where you couldn't manually adjust progress on front end

= 1.2.2 =
* Fixes textdomain issue on new projects

= 1.2.1 =
* Fixes 404 on new project from project page
* Prep for sub-tasks add-on

= 1.2 =
* New front end creation wizard, much nicer interface for creating projects
* Does a better job removing theme stylesheets
* New ability to edit each individual section and tab through sections rather than one long view
* New wizard for creating a project
* New interface for editing portions of a project
* Inline links for editing portions of a project (milestones, phases, overview, access, etc...)
* Now restricts images on the front end to yes.
* Updates project timestamp on front end edit

= 1.1.3 =
* Now getting the entire phase array passed into tasks to prevent a DB query per each individual task
* Passes page variable into task filter

= 1.1.2 =
* Added support for task assignment notifications from front end
* Fixed issue with missing team members on front end for assignment
* More attempts to fix the Yoast SEO bugs -- reached out to their support for more help

= 1.1 =
* Added ability to add, edit and delete tasks inline with a project without having to click "edit project"
* Prevents Yoast SEO from creating 1,000s of duplicates due to their weird internal linking script

= 1.0.9 =
* Adjusted how the license key is obtained

= 1.0.8 =
* Added support for having a URL base prefix in permalinks

= 1.0.7 =
* Improved editing and project creation interfaces
* Custom page titles for editor views
* Fixed issue with deleting projects on front end
* Save button is now fixed to the bottom of the page for quicker editing

= 1.0.6 =
* COMPATIBILITY: Necessary for Panorama 1.5
* ENHANCEMENT: Nicer experience when selecting users to notify
* ENHANCEMENT: Usernames are now clickable to select to notify

= 1.0.5.7 =
* COMPATIBILITY: Increased priority on template include

= 1.0.5.6 =
* COMPATIBILITY: Fixes issues with ACF Pro 5.5.5 and dates

= 1.0.5.5 =
* COMPATIBILITY: Added support for plain permalinks

= 1.0.5.4 =
* ENHANCEMENT: Added button to check license activation response
* ENHANCEMENT: Added ability to delete a project on the front end (if the user has permission)
* BUG: Fixed issue where you sometimes got an error on duplicating projects if debug was on

= 1.0.5.3 =
* BUG: Prevents using front end templates from creating additional new front end templates
* BUG: Prevented issue where updating the title didn't work properly
* ENHANCEMENT: Updating the title will change the slug

= 1.0.5.2 =
* BUG: Reworked init function to prevent load order issues

= 1.0.5.1 =
* FEATURE: Added new project link to dashboard menu
* COMPATIBILITY: Better highlighting of sub-nav items

= 1.0.5 =
* COMPATIBILITY: Improved front end notification variable support
* FEATURE: Added ability to dequeue scripts for better styling

= 1.0.4 =
* Fixed issue with array_diff notices

= 1.0.3 =
* Added ability to create projects from a template

= 1.0.2.1 =
* Fixed issue with permalinks on notification e-mails
* Checks and requires Panorama 4.0 is installed

= 1.0.2 =
* Fixed issue with custom permalinks and creating projects
* Fixed issue with notifications not sending from the front end with latest version of Panorama

= 1.0.1 =
* Removed references to nonexistent assets
* Fixes issues with WP_DEBUG being enabled
* Adds english POT files

= 1.0 =
* Initial release
