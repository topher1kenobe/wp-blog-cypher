=== WP Blog Cypher ===
Contributors: topher1kenobe
Tags: toy, cypher
Requires at least: 3.0
Tested up to: 4.0
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Uses the posts table as an Ottendorf cypher.

== Description ==

This plugin provides a shortcode that renders a form that allows you to enter a string of words and get keys back for each word.  You can then put those keys back into the same form to get the words back out.

<a href="https://en.wikipedia.org/wiki/Book_cipher">Please read this entry from Wikipedia.</a>

NOTE: THIS IS NOT SECURE.  THIS IS A TOY.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `wp-blog-cypher` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create new or Edit a Post.

== Usage ==

Please this shortcode anywhere you'd like: [cypher-form] .  View the form.  Enter a string of words, something like "this is cool" and choose Encypher and submit.

== Frequently Asked Questions ==

= Why isn't this secure? =

Because there's no encyption happening at all.  It's completely security through obscurity, and it's not even very obscure.

= If it's not secure, why did you make it? =

I made one long ago on my old blog because I think Ottendorf cyphers are neato.  Now that I have a new blog on WordPress I chose to port it.

= Why do I sometimes get a key pair with one side missing? =

Because my selection code is very very simplistic, and sometimes it makes mistakes.

== Screenshots ==

1. The form, with an example cypher

== Changelog ==

= 1.0 =
* Initial release.
