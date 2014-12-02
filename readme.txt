=== Geocode Switch ===
Contributors: elialgranti
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=C7QAD2M3L5T6E
Tags: geocode, geocode switch, geocode filter, geotag, geomarketing, geomarking, geolocation, geofilter, location, local marketing
Version: 1.0.0
Requires at least: 3.0
Tested up to: 4.0.1
Stable tag: 1.0.0
License: GPLv2 or later for plugin code, Apache License version 2.0 for Maxmind library under vendor directory
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

GeoSwitch is a plugin that allows you to change the content of your site based on the location of your client’s IP.
GeoSwitch uses [MaxMind](https://www.maxmind.com) databases to geolocate users based on IP.
MaxMind offers free and paid geolocation databases, the author of this plugin is not affiliated with MaxMind in any way.

**This plugin uses the [MaxMind PHP library](http://maxmind.github.io/GeoIP2-php/) which is released under the Apache License version 2.0**

== Prerequisites ==

The Geoswitch plugin uses MaxMind’s city database you’ll need either the free GeoLite2 city database 
(download from [here](http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz)) or obtain a license from MaxMind 
for a GeoIP2 citydatabase.
After obtaining the database uncompress it before installation.

== Installation ==
1. Copy the Plugin directory to your wordpress plugins directory (usually wp-content/plugins)
2. Copy your MaxMind binary database to the database subdirectory inside the plugin root direcory (GeoSwitch/database). 
   The database should be uncompressed.
3. In the Wordpress administration settings search for the GeoSwitch configuration page and set the name of the database 
   and the units to use for distance calculations (kilometer or miles).

== Usage ==

**GeoSwitch Conditional Blocks**

GeoSwitch uses two shortcodes [geoswitch] and [geoswitch_case] to create conditional blocks.
The [geoswitch] shortcode is used to enclose one or more [geoswitch_case] shortcodes with different conditions. The 
[geoswitch_case] shortcodes enclose content that will be shown if the condition is true.
The following example illustrates a GeoSwitch conditional block that will show different contact information depending 
on the user’s IP location:

[geoswitch]
[geoswitch_case country_code=”AU” state_code=”NSW”]New South Wales Office[/geoswitch_case ]
[geoswitch_case country_code=”AU”]Australian Office[/geoswitch_case ]
[geoswitch_case]International Office[/geoswitch_case ]
[/geoswitch]

The above GeoSwitch block will display “New South Wales Office” to users in New South Wales within Australia, 
“Australian Office” to other Australian users and “International Office” to any other user.

The [geoswitch_case] shortcode accepts the following attributes:

country - comma delimited list of country names
country_code - comma delimited list of country ISO codes
state - comma delimited list of of state names
state_code - comma delimited list of state ISO codes
city - comma delimited list of city names
within, from - This set of attributes is used to test for distances. Within is the distance in kilometers or miles and 
from is the centre point represented as “latitude,longitude” in degrees (example [geoswitch within=”10” from=”-33.7167,151.6”]).

The [geoswitch_case] shortcode matches only the attributes specified so:
[geoswitch]
[geoswitch_case city=”paris”]You are in Paris![/geoswitch_case]
[/geoswitch]

Will display “You are in Paris!” to any user with an IP location in a city named Paris, e.g Paris, France or Paris, Texas, USA.
A [geoswitch_case] shortcode without any attributes always matches and can be used as the last condition in a conditional block 
to show default content.
Content between [geoswitch_case] blocks can contain any markup including any other shortcodes,  but conditional blocks should 
not be nested as this is not supported by Wordpress.
Content between the [geoswitch] and [geoswitch_case] shortcodes should be whitespace but is usually ignored:

[geoswitch] *DON’T WRITE HERE*
[geoswitch_case]...[/geoswitch_case] *OR HERE*
[geoswitch_case]...[/geoswitch_case]
[/geoswitch]

**Informational Shortcodes**

In addition to the conditional block GeoSwitch offers the following shortcodes to display user information:
[geoswitch_ip] - The user’s IP.
[geoswitch_city] - The user’s city name.
[geoswitch_state] - The user’s state name.
[geoswitch_state_code] - The user’s state ISO code.
[geoswitch_country] - The user’s country name.
[geoswitch_country_code] - The user’s country code.
== Change Log ==

= 1.0.0 =
* Initial release
