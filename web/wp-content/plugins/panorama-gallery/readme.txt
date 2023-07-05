=== Project Panorama Photo Gallery ===
Contributors: Ross Johnson
Tags: project, management, project management, basecamp, status, client, admin, intranet
Requires at least: 3.5.0
Tested up to: 4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allow users to upload files to your Panorama projects via the front end of your website.

== Description ==

Add a photo gallery to your project page, giving your clients a clear idea of your progress in a visual way. Comes with it's own built in lightbox, and three locations to select for the gallery to appear.

= Website =
http://www.projectpanorama.com

= Documentation =
http://www.projectpanorama.com/docs

= Bug Submission and Forum Support =
http://www.projectpanorama.com/forums
http://www.projectpanorama.com/support


== Installation ==

1. Make sure you have the most recent version of Project Panorama Professional or Individual
2. Upload 'panorama-gallery' to the '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Make sure you have Project Panorama Professional installed and activated
5. Visit any project page and add your photogallery title, description and images
6. Add captions and alternate text by clicking the rows icon at the bottom of the gallery and then editing an image

= 2.0.3 =
* Adds extra check for PSP_GET_OPTION to prevent gallery from loading before Panorama

= 2.0.2 =
* Gate for lite versions or no install of Panorama

= 2.0.1. =
* Misc bug fixes and tweaks
* New (better) version of lightbox

= 2.0 =
* Updated for Panorama 2.0

= 1.5 =
* Updated ACF Gallery to 2.0

= 1.4.3 =
* Added option to disable description for larger photos
* Converted settings to work with new options system

= 1.4.1 =
* Added support for the front end editor

= 1.3.2 =
* Another init method to try and prevent loading before Panorama loads

= 1.3.1 =
* Added shortcode [psp-gallery id="project_id"]

= 1.3 =
* Huge update!
* Added three additional gallery styles including: Slideshow, banded and masonry
* Reworked styling to better integrate with Project Panorama
* New and improved lightbox that takes full advantage of caption and description fields

= 1.2 =
Changed hook to ensure all plugins are loaded before registering fields

= 1.1 =
Changed fire order of plugin init to prevent conflicts with ACF Pro / ACF Gallery Module

= 1.0 =
* Initial Release!
