<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_Woocommerce_Wallet
 * @subpackage Ace_Woocommerce_Wallet/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ace_Woocommerce_Wallet
 * @subpackage Ace_Woocommerce_Wallet/includes
 * @author     Acewebx  <developer@acewebx.com>
 */
class Ace_Woocommerce_Wallet {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ace_Woocommerce_Wallet_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ACE_WOOCOMMERCE_WALLET_VERSION' ) ) {
			$this->version = ACE_WOOCOMMERCE_WALLET_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ace-woocommerce-wallet';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ace_Woocommerce_Wallet_Loader. Orchestrates the hooks of the plugin.
	 * - Ace_Woocommerce_Wallet_i18n. Defines internationalization functionality.
	 * - Ace_Woocommerce_Wallet_Admin. Defines all hooks for the admin area.
	 * - Ace_Woocommerce_Wallet_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ace-woocommerce-wallet-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ace-woocommerce-wallet-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ace-woocommerce-wallet-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ace-woocommerce-wallet-public.php';

		$this->loader = new Ace_Woocommerce_Wallet_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ace_Woocommerce_Wallet_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ace_Woocommerce_Wallet_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ace_Woocommerce_Wallet_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'aceWoocommerceGatewayInit' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'woocommerce_payment_gateways', $plugin_admin, 'aceWoocommerceGatewayInsert' );
		// Hook the custom function to the 'before_woocommerce_init' action
		$this->loader->add_action('before_woocommerce_init', $plugin_admin,'declare_cart_checkout_blocks_compatibility');
		
		$this->loader->add_action('admin_menu', $plugin_admin,'walletes_submenu',99);
		//$this->loader->add_action( 'admin_menu', $plugin_admin,'create_teams_post_type_submenu' );
		// Hook the custom function to the 'woocommerce_blocks_loaded' action
        $this->loader->add_action( 'woocommerce_blocks_loaded', $plugin_admin,'oawoo_register_order_approval_payment_method_type' );
		$this->loader->add_action( 'woocommerce_order_refunded', $plugin_admin, 'aceWalletRefundHandler', 10, 2 );

		$this->loader->add_action( 'init', $plugin_admin, 'create_teams_post_type' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'custom_payments_meta_box' );
		$this->loader->add_filter( 'manage_posts_columns', $plugin_admin, 'my_add_new_columns' );
		$this->loader->add_action( 'manage_posts_custom_column', $plugin_admin, 'render_column' );
		$this->loader->add_action( 'manage_posts_custom_column', $plugin_admin, 'render_column2' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_custom_payments_meta_data' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ace_Woocommerce_Wallet_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'init', $plugin_public, 'aceOnInitLoadHandler' );
		$this->loader->add_action( 'template_redirect', $plugin_public, 'templateRedirectHandler' );

		$this->loader->add_filter( 'query_vars', $plugin_public, 'aceCustomQueryVars', 0 );

		$this->loader->add_filter( 'woocommerce_account_menu_items', $plugin_public, 'aceCustomAddMenuItem', 10, 1 );
		$this->loader->add_action( 'woocommerce_account_ace-wallet_endpoint', $plugin_public, 'aceWooWalletQueryHandler' );
	
		$this->loader->add_action( 'wp_ajax_ace_wallet_process_add_money', $plugin_public, 'aceWalletProcessAddMoney' );
	
		$this->loader->add_action( 'woocommerce_available_payment_gateways', $plugin_public, 'aceFilterPaymentGateway' );
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'aceWalletThankyouHandler', 10, 1 );

		$this->loader->add_filter( 'woocommerce_checkout_fields', $plugin_public, 'aceDisableBillingShippingOnWallet' );


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ace_Woocommerce_Wallet_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
