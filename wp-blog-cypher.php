<?php
/**
 * Plugin Name: WP Blog Cypher
 * Plugin URI: https://github.com/topher1kenobe/wp-blog-cypher
 * Description: Creates a blog cypher, similar to an Ottendorf cypher
 * Author: Topher
 * Version: 1.4
 * Author URI: https://topher1kenobe.com
 * Requires at least: 4.0
 * Requires PHP: 5.6
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-blog-cypher
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Creates a mechanism for using a blog as a cypher, similar to an Ottendorf cypher
 *
 * @package T1K_Blog_Cypher
 * @since T1K_Blog_Cypher 1.0
 * @author Topher
 */


/**
 * Instantiate the T1K_Blog_Cypher instance
 * @since T1K_Blog_Cypher 1.0
 */
add_action( 'plugins_loaded', array( 'T1K_Blog_Cypher', 'instance' ) );

/**
 * Main T1K Blog Cypher Class
 *
 * Contains the main functions for the admin side of T1K Blog Cypher
 *
 * @class T1K_Blog_Cypher
 * @version 1.0.0
 * @since 1.0
 * @package T1K_Blog_Cypher
 * @author Topher
 */
class T1K_Blog_Cypher {

	/**
	* Instance handle
	*
	* @static
	* @since 1.2
	* @var string
	*/
	private static $__instance = null;

	/**
	* WordPress database handle
	*
	* @since 1.3
	* @var wpdb
	*/
	private $wpdb;

	/**
	 * T1K_Blog_Cypher Constructor, actually contains nothing
	 *
	 * @access public
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Instance initiator, runs setup etc.
	 *
	 * @access public
	 * @return self
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, __CLASS__ ) ) {
			self::$__instance = new self;
			self::$__instance->setup();
		}
		
		return self::$__instance;
	}

	/**
	 * Runs things that would normally be in __construct
	 *
	 * @access private
	 * @return void
	 */
	private function setup() {

		global $wpdb;

		$this->wpdb = $wpdb;

		add_shortcode( 'cypher-form', array( $this, 'form_shortcode' ) );

	}

	/**
	 * Make shortcode for rendering form
	 *
	 * @access	public
	 * @return	string	$output;
	 */
	public function form_shortcode() {
		$output = '';

		$output .= '<form action="./" method="post" id="encypher-form">' . "\n";
			
			$output .= '<label for="cypher_input">' . "\n";
			$output .= '<input type="text" name="cypher_input" id="cypher_input" /></label>' . "\n";


			$output .= '<label><input type="radio" name="cypher_action" value="encypher" checked="checked" /> Encypher</label>' . "\n";
			$output .= '<label><input type="radio" name="cypher_action" value="decypher" /> Decypher</label>' . "\n";

			$output .= wp_nonce_field( 'cypher_form', 'cypher_nonce', true, false ) . "\n";

			$output .= '<input type="submit" value="Submit" />' . "\n";

		$output .= '</form>' . "\n";

		if (
			isset( $_POST['cypher_action'], $_POST['cypher_input'], $_POST['cypher_nonce'] )
			AND ( 'encypher' == $_POST['cypher_action'] OR 'decypher' == $_POST['cypher_action'] )
			AND wp_verify_nonce( wp_unslash( $_POST['cypher_nonce'] ), 'cypher_form' )
		) {

			$output .= '<div class="cypher-output">' . "\n";

				if ( 'encypher' == $_POST['cypher_action'] ) {
					$output .= '<h4>Your encyphered keys:</h4>' . "\n";
					$output .= '<p>' . $this->encypher_input( wp_unslash( $_POST['cypher_input'] ) ) . '</p>';
				}

				if ( 'decypher' == $_POST['cypher_action'] ) {
					$output .= '<h4>Your decyphered text:</h4>' . "\n";
					$output .= '<p>' . ucfirst( strtolower( $this->decypher_input( wp_unslash( $_POST['cypher_input'] ) ) ) ) . '</p>';
				}

			$output .= '</div>' . "\n";

		}

		return $output;

	}

	/**
	 * Splits plain text into words
	 *
	 * A word is a run of letters and numbers, optionally joined by
	 * apostrophes or hyphens, so "don't" and "well-known" each count once.
	 * Everything else is treated as a separator.
	 *
	 * @access	private
	 * @param	string	$text
	 * @return	array	$words
	 */
	private function get_words( $text ) {

		// treat curly apostrophes like straight ones
		$text = str_replace( array( "\xE2\x80\x99", "\xE2\x80\x98" ), "'", $text );

		if ( ! preg_match_all( "/[\p{L}\p{N}]+(?:['-][\p{L}\p{N}]+)*/u", $text, $matches ) ) {
			return array();
		}

		return $matches[0];

	}

	/**
	 * Turns post content into the words a reader actually sees, in order
	 *
	 * Encyphering and decyphering both count words with this, so keys always line up.
	 *
	 * @access	private
	 * @param	string	$content
	 * @return	array	$words
	 */
	private function get_words_from_content( $content ) {

		// registered shortcodes never show up as text
		$text = strip_shortcodes( $content );

		// put a space before block level tags so <p>one</p><p>two</p> doesn't become "onetwo"
		$block_tags = 'address|article|aside|blockquote|br|caption|dd|div|dl|dt|figcaption|figure|footer|h[1-6]|header|hr|img|li|main|nav|ol|p|pre|section|table|tbody|td|tfoot|th|thead|tr|ul';
		$text = preg_replace( '#</?(?:' . $block_tags . ')(?=[\s/>])#i', ' $0', $text );

		// remove tags, comments, and the contents of script and style tags
		$text = wp_strip_all_tags( $text );

		// &nbsp; &amp; &#8217; etc. should be characters, not words
		$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		return $this->get_words( $text );

	}

	/**
	 * Lowercases a word for comparison
	 *
	 * @access	private
	 * @param	string	$word
	 * @return	string
	 */
	private function normalize_word( $word ) {

		return function_exists( 'mb_strtolower' ) ? mb_strtolower( $word, 'UTF-8' ) : strtolower( $word );

	}

	/**
	 * Finds a published post containing the word and returns its post_id:word_number key
	 *
	 * @access	private
	 * @param	string	$word
	 * @return	string|false	$key
	 */
	private function get_key_for_word( $word ) {

		$needle = $this->normalize_word( $word );

		// the database only sees raw content, so search loosely on the longest chunk of the word
		$fragment = '';
		foreach ( preg_split( "/['-]/", $word ) as $part ) {
			if ( strlen( $part ) > strlen( $fragment ) ) {
				$fragment = $part;
			}
		}

		$wpdb = $this->wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT `ID`, `post_content` FROM {$wpdb->posts}
				WHERE `post_content` LIKE %s
				AND `post_status` = 'publish'
				AND `post_type` = 'post'
				ORDER BY RAND() LIMIT 50",
				'%' . $wpdb->esc_like( $fragment ) . '%'
			)
		);

		// confirm the word is really in the visible text, not just somewhere in the markup
		foreach ( $results as $post ) {
			$words = array_map( array( $this, 'normalize_word' ), $this->get_words_from_content( $post->post_content ) );

			$position = array_search( $needle, $words, true );

			if ( false !== $position ) {
				// word numbers start at 1, like a person counting
				return $post->ID . ':' . ( $position + 1 );
			}
		}

		return false;

	}

	/**
	 * Takes a string, parses out the words, and gets a post_id and word_number from _content for each word
	 *
	 * @access	private
	 * @param	string	$string
	 * @return	string	$output
	 */
	private function encypher_input( $string ) {

		$output = '';

		foreach ( $this->get_words( $string ) as $word ) {

			$key = $this->get_key_for_word( $word );

			if ( false !== $key ) {
				$output .= $key . ' ';
			} else {
				$output .= 'No number for "' . esc_html( $word ) . '" ';
			}

		}

		return $output;

	}

	/**
	 * Takes a string, teases out post_id and word_number, and gets values from _content
	 *
	 * @access	private
	 * @param	string	$string
	 * @return	string	$output
	 */
	private function decypher_input( $string ) {

		$output = '';

		$wpdb = $this->wpdb;

		// blow up the string on whitespace, giving us something like this: 1234:43
		foreach ( preg_split( '/\s+/', trim( $string ) ) as $key_pair ) {

			if ( ! preg_match( '/^(\d+):(\d+)$/', $key_pair, $key_parts ) ) {
				return "Cheatin', eh? ";
			}

			$post_id     = absint( $key_parts[1] );
			$word_number = absint( $key_parts[2] );

			// only published posts, the same ones encypher draws from
			$results = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT `post_content` FROM {$wpdb->posts} WHERE `ID` = %d AND `post_status` = 'publish' AND `post_type` = 'post'",
					$post_id
				)
			);

			if ( 0 < count( $results ) ) {
				$words = $this->get_words_from_content( $results[0] );
			} else {
				$words = array();
			}

			if ( isset( $words[ $word_number - 1 ] ) ) {
				$output .= $words[ $word_number - 1 ] . ' ';
			} else {
				$output .= "Sorry, I got nuthin' ";
			}

		}

		return $output;
	}

	// end class
}
