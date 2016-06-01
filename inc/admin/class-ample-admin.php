<?php
/**
 * Ample Admin Class.
 *
 * @author  ThemeGrill
 * @package ample
 * @since   1.1.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ample_Admin' ) ) :

/**
 * Ample_Admin Class.
 */
class Ample_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );
		add_action( 'load-themes.php', array( $this, 'admin_notice' ) );
	}

	/**
	 * Add admin menu.
	 */
	public function admin_menu() {
		$theme = wp_get_theme( get_template() );

		$page = add_theme_page( esc_html__( 'About', 'ample' ) . ' ' . $theme->display( 'Name' ), esc_html__( 'About', 'ample' ) . ' ' . $theme->display( 'Name' ), 'activate_plugins', 'ample-welcome', array( $this, 'welcome_screen' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function enqueue_styles() {
		global $ample_version;

		wp_enqueue_style( 'ample-welcome', get_template_directory_uri() . '/css/admin/welcome.css', array(), $ample_version );
	}

	/**
	 * Add admin notice.
	 */
	public function admin_notice() {
		global $ample_version, $pagenow;

		wp_enqueue_style( 'ample-message', get_template_directory_uri() . '/css/admin/message.css', array(), $ample_version );

		// Let's bail on theme activation.
		if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
			update_option( 'ample_admin_notice_welcome', 1 );

		// No option? Let run the notice wizard again..
		} elseif( ! get_option( 'ample_admin_notice_welcome' ) ) {
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
		}
	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public static function hide_notices() {
		if ( isset( $_GET['ample-hide-notice'] ) && isset( $_GET['_ample_notice_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['_ample_notice_nonce'], 'ample_hide_notices_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'ample' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'ample' ) );
			}

			$hide_notice = sanitize_text_field( $_GET['ample-hide-notice'] );
			update_option( 'ample_admin_notice_' . $hide_notice, 1 );
		}
	}

	/**
	 * Show welcome notice.
	 */
	public function welcome_notice() {
		?>
		<div id="message" class="updated ample-message">
			<a class="ample-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'activated' ), add_query_arg( 'ample-hide-notice', 'welcome' ) ), 'ample_hide_notices_nonce', '_ample_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'ample' ); ?></a>
			<p><?php printf( esc_html__( 'Welcome! Thank you for choosing Spacious! To fully take advantage of the best our theme can offer please make sure you visit our %swelcome page%s.', 'ample' ), '<a href="' . esc_url( admin_url( 'themes.php?page=ample-welcome' ) ) . '">', '</a>' ); ?></p>
			<p class="submit">
				<a class="button-secondary" href="<?php echo esc_url( admin_url( 'themes.php?page=ample-welcome' ) ); ?>"><?php esc_html_e( 'Get started with Spacious', 'ample' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Intro text/links shown to all about pages.
	 *
	 * @access private
	 */
	private function intro() {
		global $ample_version;
		$theme = wp_get_theme( get_template() );

		// Drop minor version if 0
		$major_version = substr( $ample_version, 0, 3 );
		?>
		<div class="ample-theme-info">
				<h1>
					<?php esc_html_e('About', 'ample'); ?>
					<?php echo $theme->display( 'Name' ); ?>
					<?php printf( '%s', $major_version ); ?>
				</h1>

			<div class="welcome-description-wrap">
				<div class="about-text"><?php echo $theme->display( 'Description' ); ?></div>

				<div class="ample-screenshot">
					<img src="<?php echo esc_url( get_template_directory_uri() ) . '/screenshot.png'; ?>" />
				</div>
			</div>
		</div>

		<p class="ample-actions">
			<a href="<?php echo esc_url( 'http://themegrill.com/themes/ample/' ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Theme Info', 'ample' ); ?></a>

			<a href="<?php echo esc_url( 'http://demo.themegrill.com/ample/' ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'View Demo', 'ample' ); ?></a>

			<a href="<?php echo esc_url( 'http://themegrill.com/themes/ample-pro/' ); ?>" class="button button-primary docs" target="_blank"><?php esc_html_e( 'View PRO version', 'ample' ); ?></a>

			<a href="<?php echo esc_url( 'https://wordpress.org/support/view/theme-reviews/ample?filter=5#postform' ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'Rate this theme', 'ample' ); ?></a>
		</p>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( empty( $_GET['tab'] ) && $_GET['page'] == 'ample-welcome' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ample-welcome' ), 'themes.php' ) ) ); ?>">
				<?php echo $theme->display( 'Name' ); ?>
			</a>
			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'supported_plugins' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ample-welcome', 'tab' => 'supported_plugins' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Supported Plugins', 'ample' ); ?>
			</a>
			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'free_vs_pro' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ample-welcome', 'tab' => 'free_vs_pro' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Free Vs Pro', 'ample' ); ?>
			</a>
			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'changelog' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ample-welcome', 'tab' => 'changelog' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Changelog', 'ample' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Welcome screen page.
	 */
	public function welcome_screen() {
		$current_tab = empty( $_GET['tab'] ) ? 'about' : sanitize_title( $_GET['tab'] );

		// Look for a {$current_tab}_screen method.
		if ( is_callable( array( $this, $current_tab . '_screen' ) ) ) {
			return $this->{ $current_tab . '_screen' }();
		}

		// Fallback to about screen.
		return $this->about_screen();
	}

	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		$theme = wp_get_theme( get_template() );
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<div class="changelog point-releases">
				<div class="under-the-hood two-col">
					<div class="col">
						<h3><?php echo esc_html_e( 'Theme Customizer', 'ample' ); ?></h3>
						<p><?php esc_html_e( 'All Theme Options are available via Customize screen.', 'ample' ) ?></p>
						<p><a href="<?php echo admin_url( 'customize.php' ); ?>" class="button button-secondary"><?php esc_html_e( 'Customize', 'ample' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php echo esc_html_e( 'Documentation', 'ample' ); ?></h3>
						<p><?php esc_html_e( 'Please view our documentation page to setup the theme.', 'ample' ) ?></p>
						<p><a href="<?php echo esc_url( 'http://themegrill.com/theme-instruction/ample/' ); ?>" class="button button-secondary"><?php esc_html_e( 'Documentation', 'ample' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php echo esc_html_e( 'Got theme support question?', 'ample' ); ?></h3>
						<p><?php esc_html_e( 'Please put it in our dedicated support forum.', 'ample' ) ?></p>
						<p><a href="<?php echo esc_url( 'http://themegrill.com/support-forum/' ); ?>" class="button button-secondary"><?php esc_html_e( 'Support', 'ample' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php echo esc_html_e( 'Need more features?', 'ample' ); ?></h3>
						<p><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'ample' ) ?></p>
						<p><a href="<?php echo esc_url( 'http://themegrill.com/themes/ample-pro/' ); ?>" class="button button-secondary"><?php esc_html_e( 'View PRO version', 'ample' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php echo esc_html_e( 'Got sales related question?', 'ample' ); ?></h3>
						<p><?php esc_html_e( 'Please send it via our sales contact page.', 'ample' ) ?></p>
						<p><a href="<?php echo esc_url( 'http://themegrill.com/contact/' ); ?>" class="button button-secondary"><?php esc_html_e( 'Contact Page', 'ample' ); ?></a></p>
					</div>

					<div class="col">
						<h3>
							<?php
							echo esc_html_e( 'Translate', 'ample' );
							echo ' ' . $theme->display( 'Name' );
							?>
						</h3>
						<p><?php esc_html_e( 'Click below to translate this theme into your own language.', 'ample' ) ?></p>
						<p>
							<a href="<?php echo esc_url( 'http://translate.wordpress.org/projects/wp-themes/ample' ); ?>" class="button button-secondary">
								<?php
								esc_html_e( 'Translate', 'ample' );
								echo ' ' . $theme->display( 'Name' );
								?>
							</a>
						</p>
					</div>
				</div>
			</div>

			<div class="return-to-dashboard ample">
				<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
					<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
						<?php is_multisite() ? esc_html_e( 'Return to Updates', 'ample' ) : esc_html_e( 'Return to Dashboard &rarr; Updates', 'ample' ); ?>
					</a> |
				<?php endif; ?>
				<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? esc_html_e( 'Go to Dashboard &rarr; Home', 'ample' ) : esc_html_e( 'Go to Dashboard', 'ample' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Output the changelog screen.
	 */
	public function changelog_screen() {
		global $wp_filesystem;

		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php esc_html_e( 'View changelog below:', 'ample' ); ?></p>

			<?php
				$changelog_file = apply_filters( 'ample_changelog_file', get_template_directory() . '/readme.txt' );

				// Check if the changelog file exists and is readable.
				if ( $changelog_file && is_readable( $changelog_file ) ) {
					WP_Filesystem();
					$changelog = $wp_filesystem->get_contents( $changelog_file );
					$changelog_list = $this->parse_changelog( $changelog );

					echo wp_kses_post( $changelog_list );
				}
			?>
		</div>
		<?php
	}

	/**
	* Parse changelog from readme file.
	* @param  string $content
	* @return string
	*/
	private function parse_changelog( $content ) {
		$matches   = null;
		$regexp    = '~==\s*Changelog\s*==(.*)($)~Uis';
		$changelog = '';

		if ( preg_match( $regexp, $content, $matches ) ) {
			$changes = explode( '\r\n', trim( $matches[1] ) );

			$changelog .= '<pre class="changelog">';

			foreach ( $changes as $index => $line ) {
				$changelog .= wp_kses_post( preg_replace( '~(=\s*Version\s*(\d+(?:\.\d+)+)\s*=|$)~Uis', '<span class="title">${1}</span>', $line ) );
			}

			$changelog .= '</pre>';
		}

		return wp_kses_post( $changelog );
	}

	/**
	 * Output the supported plugins screen.
	 */
	public function supported_plugins_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php esc_html_e( 'This theme recommends following plugins:', 'ample' ); ?></p>
			<ol>
				<li><?php printf(__('<a href="%s" target="_blank">Social Icons</a>', 'ample'), esc_url('https://wordpress.org/plugins/social-icons/')); ?>
					<?php esc_html_e(' by ThemeGrill', 'ample'); ?>
				</li>
				<li><?php printf(__('<a href="%s" target="_blank">Easy Social Sharing</a>', 'ample'), esc_url('https://wordpress.org/plugins/easy-social-sharing/')); ?>
					<?php esc_html_e(' by ThemeGrill', 'ample'); ?>
				</li>
				<li><?php printf(__('<a href="%s" target="_blank">Contact Form 7</a>', 'ample'), esc_url('https://wordpress.org/plugins/contact-form-7/')); ?></li>
				<li><?php printf(__('<a href="%s" target="_blank">WP-PageNavi</a>', 'ample'), esc_url('https://wordpress.org/plugins/wp-pagenavi/')); ?></li>
				<li><?php printf(__('<a href="%s" target="_blank">Breadcrumb NavXT</a>', 'ample'), esc_url('https://wordpress.org/plugins/breadcrumb-navxt/')); ?></li>
				<li><?php printf(__('<a href="%s" target="_blank">WooCommerce</a>', 'ample'), esc_url('https://wordpress.org/plugins/woocommerce/')); ?></li>
				<li>
					<?php printf(__('<a href="%s" target="_blank">Polylang</a>', 'ample'), esc_url('https://wordpress.org/plugins/polylang/')); ?>
					<?php esc_html_e('Fully Compatible in Pro Version', 'ample'); ?>
				</li>
				<li>
					<?php printf(__('<a href="%s" target="_blank">WMPL</a>', 'ample'), esc_url('https://wpml.org/')); ?>
					<?php esc_html_e('Fully Compatible in Pro Version', 'ample'); ?>
				</li>
			</ol>

		</div>
		<?php
	}

	/**
	 * Output the free vs pro screen.
	 */
	public function free_vs_pro_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'ample' ); ?></p>

			<table>
				<thead>
					<tr>
						<th class="table-feature-title"><h3><?php esc_html_e('Features', 'ample'); ?></h3></th>
						<th><h3><?php esc_html_e('Ample', 'ample'); ?></h3></th>
						<th><h3><?php esc_html_e('Ample Pro', 'ample'); ?></h3></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><h3><?php esc_html_e('Price', 'foodhunt'); ?></h3></td>
						<td><?php esc_html_e('Free', 'foodhunt'); ?></td>
						<td><?php esc_html_e('$69', 'foodhunt'); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Use as One Page theme', 'foodhunt'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Slider', 'foodhunt'); ?></h3></td>
						<td><?php esc_html_e('4 Slides', 'foodhunt'); ?></td>
						<td><?php esc_html_e('Unlimited Slides', 'foodhunt'); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Slider Settings', 'foodhunt'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><?php esc_html_e('Slides type, duration & delay time', 'foodhunt'); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Google Fonts', 'foodhunt'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><?php esc_html_e('600+', 'foodhunt'); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Color Palette', 'foodhunt'); ?></h3></td>
						<td><?php esc_html_e('Primary Color Option', 'foodhunt'); ?></td>
						<td><?php esc_html_e('Primary color option & 35+', 'foodhunt'); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Font Size options', 'foodhunt'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Business Template', 'foodhunt'); ?></h3></td>
						<td><?php esc_html_e('1', 'foodhunt'); ?></td>
						<td><?php esc_html_e('5', 'foodhunt'); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Social Links', 'foodhunt'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Additional Top Header', 'ample'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><?php esc_html_e('Social Links + Header text option', 'ample'); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Translation Ready', 'foodhunt'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Woocommerce Compatible', 'foodhunt'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Woocommerce archive page Layout', 'foodhunt'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('WPML/Polylang Compatible', 'foodhunt'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Custom Widgets', 'foodhunt'); ?></h3></td>
						<td><?php esc_html_e('4', 'foodhunt'); ?></td>
						<td><?php esc_html_e('6', 'foodhunt'); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('TG: Testimonial', 'foodhunt'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('TG: Our Clients', 'foodhunt'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Footer Copyright Editor', 'foodhunt'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Demo Content', 'foodhunt'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Support', 'foodhunt'); ?></h3></td>
						<td><?php esc_html_e('Forum', 'foodhunt'); ?></td>
						<td><?php esc_html_e('Emails/Priority Support Ticket', 'foodhunt'); ?></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td class="btn-wrapper">
							<a href="<?php echo esc_url( apply_filters( 'ample_pro_theme_url', 'http://themegrill.com/themes/ample-pro/' ) ); ?>" class="button button-secondary docs" target="_blank"><?php _e( 'View Pro', 'ample' ); ?></a>
						</td>
				</tbody>
			</table>

		</div>
		<?php
	}
}

endif;

return new Ample_Admin();
