<?php

$bg_settings = array(
	'image' => psp_get_option('psp_login_background'),
	'color' => psp_get_option('psp_login_background_color'),
	'disable' => psp_get_option('psp_disable_background_image')
);

$style 		= '';
$wrap_class = '';

if( !$bg_settings['image'] && !$bg_settings['disable'] ) {
	$wrap_class = 'default-bg';
}

if( $bg_settings['image'] && !$bg_settings['disable'] ) {
	$style = 'background-image: url("' . esc_url($bg_settings['image']) . '")';
} elseif( $bg_settings['color'] ) {
	$style = 'background-color: ' . $bg_settings['color'];
}

do_action( 'login_head' ); do_action( 'login_enqueue_scripts' ); ?>

<div class="<?php echo esc_attr( 'psp-login ' . $wrap_class ); ?>" style="<?php echo esc_attr($style); ?>">
	<div class="psp-login-sidebar">
		<?php
		do_action( 'psp_login_form_before' ); ?>
		<div class="psp-login-form">
			<?php
			if( psp_get_option('psp_logo') != '' && psp_get_option('psp_logo') != 'http://' ): ?>
				<div class="psp-login-logo">
					<img src="<?php echo esc_url(psp_get_option('psp_logo')); ?>" alt="<?php echo esc_attr_e( 'Login form logo', 'psp_projects' ); ?>">
				</div> <!--/.psp-login-logo-->
			<?php endif; ?>
			<?php
			if( isset($_GET['login']) && $_GET['login'] == 'failed' ): ?>
				<div class="psp-login-error">
					<p><?php esc_html_e( 'Incorrect username or password.', 'psp_projects'); ?><br> <?php esc_html_e( 'Please try again', 'psp_projects' ); ?></p>
				</div>
			<?php
			endif;

			if( psp_get_option('psp_login_content') ):
				echo '<div class="psp-login-content">' . wp_kses_post( wpautop( psp_get_option('psp_login_content') ) ) . '</div>';
			endif;

			do_action( 'psp_login_form_content' );

			if( !is_user_logged_in() ):

				$password_required = post_password_required();

				echo panorama_login_form( $password_required );

			else:

				echo "<p>" . __( 'You don\'t have permission to access this project' , 'psp_projects' ) . "</p>";

			endif; ?>
			<p class="psp-text-center"><a href="<?php echo esc_url(wp_lostpassword_url(site_url().$_SERVER['REQUEST_URI'])); ?>"><?php esc_html_e( 'Lost your password?', 'psp_projects' ); ?></a></p>
		</div> <!--/#psp-login-->
		<?php do_action( 'psp_login_form_after' ); ?>
	</div> <!--/.psp-login-sidebar-->
</div> <!--/.psp-login-->
<?php do_action( 'login_footer' ); ?>
