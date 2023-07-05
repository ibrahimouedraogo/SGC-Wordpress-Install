<?php
/**
 * Email template.
 *
 * @since {{VERSION}}
 *
 * @var array $email_parts All template parts to go in the email.
 */

defined( 'ABSPATH' ) || die();

$email_parts = $email_parts ? $email_parts : array(
	'logo',
	'heading',
	'message',
);

$wrapper_styles = array(
	'background' => '#f1f2f7',
	'padding' => '30px',
);

$container_styles = array(
	'background'    => '#fff',
	'padding'       => '4%',
	'border-radius' => '12px',
	'font-family'   => "'Arial','Helvetica','San-Serif'",
	'width'         => '92%',
	'max-width'     => '640px',
	'margin'        => '0 auto',
	'line-height'	 => '1.65',
	'font-size'	 => '18px'
);
?>

<html>
	<style type="text/css">
		a {
			color: <?php echo psp_get_option('psp_accent_color_1') ?>;
		}
	</style>
	<div style="<?php echo psp_build_style( $wrapper_styles ); ?>">
		<div style="<?php echo psp_build_style( $container_styles ); ?>">
			<?php
			foreach ( $email_parts as $part ) {
				include psp_template_hierarchy( "/email/$part.php" );
			}
			?>
		</div>
	</div>
</html>
