<?php /*

**************************************************************************

Plugin Name:  SyntaxHighlighter Evolved
Plugin URI:   http://www.viper007bond.com/wordpress-plugins/syntaxhighlighter/
Version:      4.0.0-alpha
Description:  Easily post syntax-highlighted code to your site without having to modify the code at all. Uses Alex Gorbatchev's <a href="http://alexgorbatchev.com/wiki/SyntaxHighlighter">SyntaxHighlighter</a>. <strong>TIP:</strong> Don't use the Visual editor if you don't want your code mangled. TinyMCE will "clean up" your HTML.
Author:       Alex Mills (Viper007Bond)
Author URI:   http://www.viper007bond.com/

**************************************************************************

Thanks to:

* Alex Gorbatchev for writing the Javascript-powered synatax-highlighter script
* Andrew Ozz for writing the TinyMCE plugin

**************************************************************************/

class SyntaxHighlighter {

	public $pluginver = '4.0.0-alpha';

	public $settings;
	public $renderer;

	function __construct() {
		global $wp_version;

		// Requires WordPress 3.3+ but you really should be using the latest version!
		if ( ! version_compare( $wp_version, '3.3', '>=' ) ) {
			return;
		}

		// Load localization file
		load_plugin_textdomain( 'syntaxhighlighter', false, dirname( plugin_basename( __FILE__ ) ) . '/localization/' );

		// Queue further initialization for later
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		$this->load_user_settings();
		$this->load_renderer();
	}

	public function load_user_settings() {
		require_once( __DIR__ . '/classes/class-settings.php' );
		$this->settings = new SyntaxHighlighter_Settings();
	}

	public function load_renderer() {
		switch ( $this->settings->renderer ) {
			case 'sh2':
				wp_die( 'not implemented yet' );
				break;

			case 'sh3':
				require_once( __DIR__ . '/classes/class-renderer-syntaxhighlighter3.php' );

				$this->renderer = new SyntaxHighlighter_Renderer_SH3();

				break;

			// You could implement your own render if you wanted
			default;
				do_action( 'syntaxhighlighter_load_renderer', $this );
		}

		// Reset settings to default if the user's renderer setting was invalid
		if ( ! $this->renderer ) {
			$this->settings->reset_all();
		}
	}

	function register_hooks() {
		$this->renderer->register_hooks();
	}
}


/**
 * Returns the single instance of SyntaxHighlighter Evolved, creating a new instance if needed.
 *
 * Use this function rather than the global variable if you need to interact with or
 * call one of SyntaxHighlighter Evolved's methods.
 *
 * For example if you wanted to unhook something that SyntaxHighlighter Evolved hooked, then
 * you would simple need to do this:
 *
 * remove_filter( 'the_filter_name', array( SyntaxHighlighter(), 'the_callback_name' ) );
 *
 * While this function existed before 4.0.0, it did not return the class instance.
 *
 * @since 4.0.0
 *
 * @return SyntaxHighlighter The single instance of SyntaxHighlighter.
 */
function SyntaxHighlighter() {
	global $SyntaxHighlighter;

	if ( ! isset( $SyntaxHighlighter ) ) {
		$SyntaxHighlighter = new SyntaxHighlighter();
	}

	return $SyntaxHighlighter;
}

/**
 * Start up SyntaxHighlighter Evolved once all other plugins have loaded.
 */
add_action( 'plugins_loaded', 'SyntaxHighlighter' );