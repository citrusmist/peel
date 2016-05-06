<?php 

/**
 * General plugin class. 
 *
 * This class imposes a singleton pattern on classes which extend it. Only one 
 * instance of a derived class should be able to be instantiated at time.
 *
 * Maintains a unique identifier of plugin as well as the current version 
 * of the plugin. Provides an interface for instantiating modules.
 */
abstract class PL_Plugin {
	
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      projects_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;


	/**
	 * Holds path to root directory of plugin
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugindir_path;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $name    The string used to uniquely identify this plugin.
	 */
	protected $name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	/**
	 * The array of modules registered with this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $modules    The modules this plugin consists of
	 */
	protected $modules;
	
	protected $router;

	public function __construct( $version, $plugindir_path, $route ) {
		
		$this->plugindir_path = $plugindir_path;
		$this->loader         = new PL_Plugin_Loader();
		$this->router         = $route;
		// $this->registry       = $this->load_modules( $modules );

		$this->set_locale();
		$this->loader->run();
	}

	public function get_plugindir_path() {
		return $this->plugindir_path;
	}

	/**
	 * Return the plugin path.
	 *
	 * @since    0.0.1
	 *
	 * @return    Path to root directory of the plugin
	 */
	public function get_path() {
		return $this->get_plugindir_path();
	}


	/*public function add_post_route( $name, $action ) {
		$this->router->post( $name, $action, $this->get_name() );
	}


	public function add_get_route( $name, $action ) {
		$this->router->get( $name, $action, $this->get_name() );
	}

	public function add_resource_route( $name, $action ) {
		$this->router->resource( $name, $action, $this->get_name() );
	}*/

/*	public function add_cpt_route( $name, $action, $qv ) {
		$this->router->cpt( $name, $action, $qv, $this->get_name() );
	}*/

	public function add_cpt_resource_routes( $name, $controller ) {
		$this->router->cpt_resource( $name, $controller, $this->get_name() );
	}

	/**
	 * Generates routes based on the parameters CPT has been registered with.
	 * If CPT doesnt support archive pages then index action isn't registered
	 */
	public function add_cpt_builtin_routes( $name, $actions ) {
		$this->router->cpt_builtin( $name, $actions, $this->get_name() );
	}

	public function route_get( $name, $args ) {
		$this->router->get( $name, $args, $this->get_name() );
	}

	public function route_post( $name, $args ) {
		$this->router->post( $name, $args, $this->get_name() );
	}

	public function route_resource( $name, $args ) {
		$this->router->resource( $name, $args, $this->get_name() );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->get_name();
	}


	public static function get_slug() {
		return self::get_instance()->get_plugin_slug();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * If plugin name isn't explicitly set it is inferred from the class name.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_name() {

		if( empty( $this->name ) ) {
			$name = explode( '_', strtolower( get_called_class() ) );
			array_shift( $name );
			$name = implode( '-', $name );
			$this->name = $name;
		}
		
		return $this->name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    projects_Loader    Orchestrates the hooks of the plugin.
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

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the SS_Shows_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new PL_Plugin_i18n();
		$plugin_i18n->set_domain( $this->get_name() );
		$plugin_i18n->set_path( $this->get_plugindir_path() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}
}