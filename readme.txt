=== Plugin Name ===
Contributors: brendaegeland
Tags: pages, navigation, widgets, subpages
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Lists pages and subpages for a given page, or the top ancestor of the current page and all of the ancestor's descendants

== Description ==

A common web design pattern is to have a primary navigation with the top level pages, and then a separate
navigation element on each page that shows only those pages related to a common top-most page. This widget helps
to build those secondary navigation elements.

If your page structure was like this:

* Home
* About
* -- Our History
* -- Our Staff
* -- -- Employment Opportunities
* Services
* -- Widget Development
* -- Gadget Deployment
* Contact Us

and you added the widget with its default settings to a sidebar, it would create a list like this:

* About
* Our History
* Our Staff
* -- Employment Opportunities

on __any__ of the pages in the About subtree. Notice that the top-most ancestor, About, is included as the first element of the list.


= Options =

1. __Title__. The widget title. If left blank, it defaults to the title of the top ancestor page.
1. __Show Title__. Maybe you don't want a title. Just turn it off here.
1. __Top Page__. You can select one of your pages to be the top page, or you can choose Default, which determines the top-most ancestor of the page being displayed.
1. __Menu Class__. Gets applied to the outermost &lt;ul&gt; element.
1. __Before Links__. HTML to be inserted before links in the list.
1. __After Links__. HTML to be inserted after links in the list.

== Installation ==

1. Download and unzip the latest release zip file
1. Upload the entire subpages-in-context directory to your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. The Subpages In Context Widget

== Changelog ==

= 0.2 =
* Fixed bug in widget form settings
* Added option to omit top-most page from output list

= 0.1 =
* Initial version.

== Upgrade Notice ==

= 0.2 =
Adds option to omit top-most page from output list.