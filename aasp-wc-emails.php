<?php

/**
 * Plugin Name
 *
 * @package           AASPWCEmails
 * @author            Rafael dos Santos
 * @copyright         2022 Codash - Software House
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       AASP WC E-mails
 * Plugin URI:        https://codash.com.br/wordpress/portfolio/plugins/aasp-wc-emails
 * Description:       Estende a funcionalidade de e-mail do WooCommerce &nbsp;✉️
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rafael dos Santos
 * Author URI:        https://rafael.business
 * Text Domain:       aasp-wc-emails
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Update URI:        https://codash.com.br/plugins/aasp-wc-emails
 */

if( !defined( 'AASPWCEMAILS_VER' ) )
	define( 'AASPWCEMAILS_VER', '1.0.0' );

// Start up the engine
class AASPWCEmails
{

	/**
	 * Static property to hold our singleton instance
	 *
	 */
	static $instance = false;

	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	private function __construct() {
		// back end
		add_action( 'plugins_loaded', 			array( $this, 'textdomain'		) );
		add_action( 'admin_enqueue_scripts',	array( $this, 'admin_scripts'	) );
		add_action( 'admin_menu', 				array( $this, 'admin_menu' 		) );
	}

	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * retuns it.
	 *
	 * @return AASPWCEmails
	 */

	public static function getInstance() {
		if ( !self::$instance )
			self::$instance = new self;
		return self::$instance;
	}

	/**
	 * load textdomain
	 *
	 * @return void
	 */

	public function textdomain() {

		load_plugin_textdomain( 'aasp-wc-emails', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Admin styles
	 *
	 * @return void
	 */

	public function admin_scripts() {

		wp_enqueue_style( 
			'aasp-wc-emails-admin-css', 
			plugins_url('admin/css/admin.css', __FILE__), 
			array(), 
			AASPWCEMAILS_VER, 
			'all' 
		);

	}

	/**
     * Registers a new settings page under Settings.
     */
    public function admin_menu() {
        add_submenu_page(
			'woocommerce',
            __( 'AASP WC E-Mails - Configurações', 'aasp-wc-emails' ),
            __( '<span style="color: yellow;">WC E-Mails</span>', 'aasp-wc-emails' ),
            'manage_options',
            'aasp_wc_emails_config',
            array(
                $this,
                'settings_page'
            )
        );
    }

	public function get_custom_email_html( $order, $heading = false, $mailer ) {

		$template = 'emails/customer-order-enviado-correios.php';
	
		return wc_get_template_html( $template, array(
			'order'			=> $order,
			'email_heading' => $heading,
			'sent_to_admin' => false,
			'plain_text'    => false,
			'email'         => $mailer
		) );
	
	}
 
    /**
     * Settings page display callback.
     */
    public function settings_page() {
		?>
		<div class="wrap">
			<h1><?= __( 'AASP WC E-mails', 'aasp-wc-emails' ) ?></h1>
			<p><?= __( 'Configurações relacionadas ao disparo de e-mails transacionais da loja WooCommerce.', 'aasp-wc-emails' ) ?></p>
		</div>
		<?php

		$order_id = 16;

		$mailer = WC()->mailer();
		$order = wc_get_order( $order_id );

		$recipient = "rafael@codash.com.br";
		$subject = __( "Pedido enviado para os Correios! 🚚", 'aasp-wc-emails' );
		$content = $this->get_custom_email_html( $order, $subject, $mailer );
		$headers = "Content-Type: text/html\r\n";

		$mailer->send( $recipient, $subject, $content, $headers );
    }

/// end class
}


// Instantiate our class
$AASPWCEmails = AASPWCEmails::getInstance();