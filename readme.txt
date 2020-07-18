=== WP Blog Cypher ===
Contributors: topher1kenobe
Tags: toy, cypher
Requires at least: 3.0
Tested up to: 5.4
Stable tag: 1.3
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

= How does this work? =

For every word in the input string it searches for a post with that word in it and grabs the post_id.  That's the first number in the keypair.

Then it strips the html from the post, counts the words in the post, and figures out what number your word is.  That's the send number.

So 385:16 would be post_id 385, word number 16.

Decyphering is a little easier, since we know the numbers.  I simply look up the post by ID, strip the html, and grab the proper word number.

= Why isn't this secure? =

Because there's no encyption happening at all.  It's completely security through obscurity, and it's not even very obscure.

= If it's not secure, why did you make it? =

I made one long ago on my old blog because I think Ottendorf cyphers are neato.  Now that I have a new blog on WordPress I chose to port it.

= Why do I sometimes get a key pair with one side missing? =

Because my selection code is very very simplistic, and sometimes it makes mistakes.

== Screenshots ==

1. The form, with an example cypher

== Changelog ==

= 1.3 =
Consolidate redundant code into method

= 1.2 =
Added some refinements to make it MUCH more accurate

= 1.1 =
Some input validation, no functionality changes

= 1.0 =
* Initial release.
