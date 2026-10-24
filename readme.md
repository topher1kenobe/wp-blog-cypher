# WP Blog Cypher

Uses the posts table as an Ottendorf cypher.

**Contributors:** [topher1kenobe](http://profiles.wordpress.org/topher1kenobe)  
**Tags:** [toy](http://wordpress.org/plugins/tags/toy), [cypher](http://wordpress.org/plugins/tags/cypher)  
**Requires at least:** 4.0  
**Requires PHP:** 5.6  
**Tested up to:** 7.1  
**Stable tag:** 1.4  
**License:** [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)  

## Description ##

This plugin provides a shortcode that renders a form that allows you to enter a string of words and get keys back for each word.  You can then put those keys back into the same form to get the words back out.

<a href="https://en.wikipedia.org/wiki/Book_cipher">Please read this entry from Wikipedia.</a>

NOTE: THIS IS NOT SECURE.  THIS IS A TOY.

## Installation ##

This section describes how to install the plugin and get it working.

1. Upload the `wp-blog-cypher` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create new or Edit a Post.

## Usage ##

Place this shortcode anywhere you'd like: [cypher-form] .  View the form.  Enter a string of words, something like "this is cool" and choose Encypher and submit.

## Frequently Asked Questions ##

### How does this work? ###
For every word in the input string it searches for a published post with that word in it and grabs the post_id.  That's the first number in the keypair.

Then it strips the html and shortcodes from the post, counts the words a reader would see, and figures out what number your word is.  That's the second number.  Counting starts at 1, so the first word in the post is word 1.

So 385:16 would be post_id 385, word number 16.

Decyphering is a little easier, since we know the numbers.  I simply look up the published post by ID, count the words the same way, and grab the proper word number.

### What counts as a word? ###
Any run of letters and numbers.  Apostrophes and hyphens inside a word keep it together, so "don't" and "well-known" each count as one word.  Punctuation, html tags, and shortcodes don't count at all.

### Why isn't this secure? ###
Because there's no encyption happening at all.  It's completely security through obscurity, and it's not even very obscure.

### If it's not secure, why did you make it? ###
I made one long ago on my old blog because I think Ottendorf cyphers are neato.  Now that I have a new blog on WordPress I chose to port it.

### Why do I sometimes get "No number for" a word? ###
None of the published posts contain that word, so there's nothing to point to.  Write more posts!


## Screenshots ##


## Changelog ##

### 1.4 ###
Word counts now match the text a reader sees, so keys are accurate every time.  Word numbers start at 1.  Decyphering only reads published posts.  Security fixes: all database queries are now prepared.  Tested up to WordPress 7.1.

### 1.3 ###
Consolidate redundant code into method

### 1.2 ###
Added some refinements to make it MUCH more accurate

### 1.1 ###
Some input validation, no functionality changes

### 1.0 ###
* Initial release.


