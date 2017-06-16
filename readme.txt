=== Geocode Switch ===
Contributors: elialgranti
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=C7QAD2M3L5T6E
Tags: geocode, geocode switch, geocode filter, geotag, geomarketing, geomarking, geolocation, geofilter, location, local marketing, GeoIP2, MaxMind
Version: 1.1.2
Requires at least: 3.0
Tested up to: 4.8
Stable tag: 1.1.2
License: GPLv2 or later for plugin code, Apache License version 2.0 for Maxmind library under vendor directory
License URI: http://www.gnu.org/licenses/gpl-2.0.html

GeoSwitch is a plugin that allows you to change the content of your site based on the location of your client’s IP.

== Description ==
GeoSwitch is a plugin that allows you to change the content of your site based on the location of your client’s IP.
To geolocate users based on IP GeoSwitch supports can user either the new the new GeoIP2 [MaxMind](https://www.maxmind.com) 
databases or GeoIP2 Precision Service.
MaxMind offers free and paid geolocation databases and the paid GeoIP2 Precision web service, 
the author of this plugin is not affiliated with MaxMind in any way.

The main development of this plugin is in [github](https://github.com/elialgranti/geoswitch). 
Please open a new [issue](https://github.com/elialgranti/geoswitch/issues) if you find a bug in this plugin.

**This plugin uses the [MaxMind PHP library](http://maxmind.github.io/GeoIP2-php/) which is released under the 
Apache License version 2.0**

== Installation ==
= Prerequisites =
The Geoswitch plugin uses either MaxMind’s city database or webservice.

* To use a local database for geocoding you’ll need either the free GeoLite2 city database 
(download from [here](http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz)) or obtain a license from MaxMind 
for a GeoIP2 citydatabase. After obtaining the database uncompress it before installation.
* To use the webservice you'll need to obtain a user ID and license key for [GeoIP2 Precision Services](https://www.maxmind.com/en/geoip2-precision-services).

= Installation =
1. Copy the Plugin directory to your wordpress plugins directory (usually wp-content/plugins)
2. Optionally copy your MaxMind binary database to the database subdirectory inside the plugin root direcory (GeoSwitch/database). 
   The database should be uncompressed.
3. In the Wordpress administration settings search for the GeoSwitch configuration page:
4. Select the type of geocoding service to use (local database or webservice). 
5. Enter the name of the database or the user ID and license key depending on the service you've selected.
6. Set the units to use for distance calculations (kilometer or miles).

*Note: if you use the local database you should update it periodically.*

== Usage ==
= GeoSwitch Conditional Blocks =

GeoSwitch uses two shortcodes [geoswitch] and [geoswitch_case] to create conditional blocks.
The [geoswitch] shortcode is used to enclose one or more [geoswitch_case] shortcodes with different conditions. The 
[geoswitch_case] shortcodes enclose content that will be shown if the condition is true.
The following example illustrates a GeoSwitch conditional block that will show different contact information depending 
on the user’s IP location:

`
[geoswitch]
[geoswitch_case country_code="AU" state_code="NSW"]New South Wales Office[/geoswitch_case]
[geoswitch_case country_code="AU"]Australian Office[/geoswitch_case]
[geoswitch_case]International Office[/geoswitch_case]
[/geoswitch]
`

The above GeoSwitch block will display “New South Wales Office” to users in New South Wales within Australia, 
“Australian Office” to other Australian users and “International Office” to any other user.

The [geoswitch_case] shortcode accepts the following attributes:

* `country` - comma delimited list of country names
* `country_code` - comma delimited list of country ISO codes
* `state` - comma delimited list of of state names
* `state_code` - comma delimited list of state ISO codes
* `city` - comma delimited list of city names
* `within`, `from` - This set of attributes is used to test for distances. Within is the distance in kilometers or miles and 
from is the centre point represented as “latitude,longitude” in degrees (example [geoswitch within=”10” from=”-33.7167,151.6”]).

The `[geoswitch_case]` shortcode matches only the attributes specified so:

`
[geoswitch]
[geoswitch_case city="paris"]You are in Paris![/geoswitch_case]
[/geoswitch]
`

Will display "You are in Paris!" to any user with an IP location in a city named Paris, e.g Paris, France or Paris, Texas, USA.
A `[geoswitch_case]` shortcode without any attributes always matches and can be used as the last condition in a conditional block 
to show default content.
Content between [geoswitch_case] blocks can contain any markup including any other shortcodes,  but conditional blocks should 
not be nested as this is not supported by Wordpress.
Content between the [geoswitch] and [geoswitch_case] shortcodes should be whitespace but is usually ignored:

`
[geoswitch] *DON’T WRITE HERE*
[geoswitch_case]...[/geoswitch_case] *OR HERE*
[geoswitch_case]...[/geoswitch_case]
[/geoswitch]
`

= Informational Shortcodes =
In addition to the conditional block GeoSwitch offers the following shortcodes to display user information:

* `[geoswitch_ip]` - The user’s IP.
* `[geoswitch_city]` - The user’s city name.
* `[geoswitch_state]` - The user’s state name.
* `[geoswitch_state_code]` - The user’s state ISO code.
* `[geoswitch_country]` - The user’s country name.
* `[geoswitch_country_code]` - The user’s country code.

If the IP of the user cannot be geo located these shortcodes return '?'

== Change Log ==
= 1.1.2 =
Bug fix: when IP cannot be found plugin correctly evaluates all cases and uses default empty case (if it exists), instead of not displaying anything.
Implemented support for comma separated values in geoswitch_case shortcode. Previous version mentioned this feature in the Readme file, but it was not actually implemented.
= 1.1.1 =
Added setting to for user IP for debugging purposes and updated MaxMind Libraries.
= 1.1.0 =
* Added support for MaxMind GeoIP2 Precision Service (thanks to [Paul Scarrone](https://github.com/ninjapanzer) 
and [carlcapozza](https://github.com/carlcapozza)).
* Fixed bug with measurement units. Units were always considered kilometers.
* Tested under Wordpress 4.1.
= 1.0.0 =
* Initial release

== Upgrade Notice ==
= 1.1.2 =
Bug fix: when IP cannot be found plugin correctly evaluates all cases and uses default empty case (if it exists), instead of not displaying anything.
Implemented support for comma separated values in geoswitch_case code block. Previous version mentioned this feature in the Readme file, but it was not actually implemented.
= 1.1.1 =
Added setting to for user IP for debugging purposes and updated MaxMind Libraries.
= 1.1.0 =
Added support for MaxMind GeoIP2 Precision Service and fixed bug with measurement units.

== Frequently Asked Questions ==
= Why do I see only question marks instead of my location? =
Your IP was not found in the database. The most usual cause if that your browser and server are behind a NAT and you are 
getting a private IP not a public one. For debugging purposes you can set the IP used by plug-in in the settings page. 
= My location is wrong/partial. How come? =
Geolocation using IP addresses is not entirely accurate. The geolocation relies on a static database of addresses and 
their approximate location, not exact location like mobile GPS. 
To ensure maximum accuracy make sure you have the latest database. Purchasing the non-lite version of the database from MaxMind 
or a license for their service may also yield better information.
I am not affiliated in any way with MaxMind, so it is up to you to contact them and evaluate their offers for suitability to your purposes.
= How do I test other locations? =
You can set the IP used in by the pug-in in the settings page and use Google to search for IPs in the location you are interested 
in (i.e. IP in California).
Debug overrides for other setting are coming.

== Screenshots ==
