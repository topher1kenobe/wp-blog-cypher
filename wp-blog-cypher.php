<?php
/**
 * Plugin Name: WP Blog Cypher
 * Plugin URI: http://topher1kenobe.com
 * Description: Creates a blog cypher, similar to an Ottendorf cypher
 * Author: Topher
 * Version: 1.1
 * Author URI: http://topher1kenobe.com
 * Text Domain: wp-blog-cypher
 */


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

		$output .= '<form action="./" method="post">' . "\n";
			
			$output .= '<label for="cypher_input">' . "\n";
			$output .= '<input type="text" name="cypher_input" id="cypher_input" />' . "\n";


			$output .= '<label><input type="radio" name="cypher_action" value="encypher" checked="checked" /> Encypher</label>' . "\n";
			$output .= '<label><input type="radio" name="cypher_action" value="decypher" /> Decypher</label>' . "\n";

			$output .= '<input type="submit" value="Submit" />' . "\n";

		$output .= '</form>' . "\n";

		if ( isset( $_POST['cypher_action'] ) AND ( 'encypher' == $_POST['cypher_action'] OR 'decypher' == $_POST['cypher_action'] ) ) {

			$output .= '<div class="cypher-output">' . "\n";

				if ( 'encypher' == $_POST['cypher_action'] ) {
					$output .= '<h4>Your encyphered keys:</h4>' . "\n";
					$output .= '<p>' . $this->encypher_input( $_POST['cypher_input'] ) . '</p>';
				}

				if ( 'decypher' == $_POST['cypher_action'] ) {
					$output .= '<h4>Your decyphered text:</h4>' . "\n";
					$output .= '<p>' . $this->decypher_input( $_POST['cypher_input'] ) . '</p>';
				}

			$output .= '</div>' . "\n";

		}

		return $output;

	}

	/**
	 * Takes a string, searches for word, and returns position number
	 *
	 * @access	private
	 * @param	string	$search
	 * @param	string	$content
	 * @return	int		$key
	 */
	private function get_key_from_word( $search, $content ) {

		$clean_content = strip_tags( $content );

		$content_array = explode( ' ', $clean_content );

		$key = array_search( $search, $content_array );

		return $key;

	}

	/**
	 * Takes a string, parses out the words, and gets a post_id and word_id from _content for each word
	 *
	 * @access	private
	 * @param	string	$string
	 * @return	string	$output
	 */
	private function encypher_input( $string ) {

		$output = '';

		// blow up the string on spaces
		$input_array = explode( ' ', $string );

		foreach ( $input_array as $key => $word ) {

			// find a post with that word with spaces on either side of it
			$query = "SELECT `ID`, `post_content` FROM " . $this->wpdb->posts . " WHERE `post_content` LIKE '% " . wp_kses_post( $word ) . " %' ORDER BY RAND() LIMIT 1";

			$results = $this->wpdb->get_results( $query );

			// within the found post, find the word position number
			if ( 0 < count( $results ) ) {
				$word_number = $this->get_key_from_word( $word, $results[0]->post_content );

				$output .=	$results[0]->ID . ':' . $word_number . ' ';
			} else {
				$output .= 'No number for "' . wp_kses_post( $word ) . '" ';
			}

		}

		return $output;

	}

	/**
	 * Takes a word_id and content string and gets the word matching the id
	 *
	 * @access	private
	 * @param	int		$word_id
	 * @param	string	$content
	 * @return	string	$word
	 */
	private function get_word_from_content( $word_id, $content ) {

		$clean_content = strip_tags( $content );

		$content_array = explode( ' ', $clean_content );

		$word = $content_array[ $word_id ] . ' ';

		return $word;

	}

	/**
	 * Takes a string, teases out post_id and word_id, and gets values from _content
	 *
	 * @access	private
	 * @param	string	$string
	 * @return	string	$output
	 */
	private function decypher_input( $string ) {

		$output = '';

		$input_array = explode( ' ', $string );


		// blow up the string on spaces, giving us something like this: 1234:43
		foreach ( $input_array as $key_pair ) {

			// blow up each item on :
			$key_parts = explode( ':', $key_pair );

			if ( ! isset( $key_parts[1] ) ) {
				return "Cheatin', eh? ";
			}

			$post_id = $key_parts[0];
			$word_id = $key_parts[1];


			$query = "SELECT `post_content` FROM " . $this->wpdb->posts . " WHERE `ID` = " . absint( $post_id );

			$results = $this->wpdb->get_col( $query );

			if ( 0 < count( $results ) ) {
				$word = $this->get_word_from_content( $word_id, $results[0] );

				$output .=	$word;
			} else {
				$output .= "Sorry, I got nuthin' ";
			}

		}

		return $output;
	}

	// end class
}

?>
