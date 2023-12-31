<?php

namespace Jet_Form_Builder\Integrations;

use Jet_Form_Builder\Admin\Tabs_Handlers\Captcha_Handler;
use Jet_Form_Builder\Admin\Tabs_Handlers\Tab_Handler_Manager;
use Jet_Form_Builder\Exceptions\Request_Exception;
use Jet_Form_Builder\Plugin;

/**
 * Captcha manager class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Forms_Captcha class
 */
class Forms_Captcha {

	const PREFIX = 'jet_form_builder_captcha__';

	public static $script_rendered = false;

	private $field_key = '_captcha_token';
	private $api       = 'https://www.google.com/recaptcha/api/siteverify';
	private $defaults  = array(
		'enabled'   => false,
		'key'       => '',
		'secret'    => '',
		'threshold' => 0.5,
	);

	public function __construct() {
		add_filter( 'jet-form-builder/request-handler/request', array( $this, 'handle_request' ) );
	}

	/**
	 * @param $request
	 *
	 * @return mixed
	 * @throws Request_Exception
	 */
	public function handle_request( $request ) {
		$this->verify( $request );

		return $request;
	}

	private function api_front_url( $key ): string {
		return esc_url_raw( sprintf( 'https://www.google.com/recaptcha/api.js?render=%s', $key ) );
	}

	/**
	 * @param $request
	 *
	 * @throws Request_Exception
	 */
	protected function verify( $request ) {
		$form_id = jet_fb_handler()->get_form_id();
		$captcha = $this->get_data( $form_id );

		if ( empty( $captcha['enabled'] ) ) {
			return;
		}

		if ( empty( $request[ $this->field_key ] ) ) {
			throw new Request_Exception( 'captcha_failed', 'Empty token. Spam' );
		}

		$token    = sanitize_text_field( $request[ $this->field_key ] );
		$response = wp_remote_post(
			$this->api,
			array(
				'body' => array(
					'secret'   => $captcha['secret'],
					'response' => $token,
				),
			)
		);

		$body = wp_remote_retrieve_body( $response );
		$body = json_decode( $body, true );

		$action = $body['action'] ?? '';
		$score  = $body['score'] ?? 0;

		$threshold = empty( $captcha['threshold'] )
			? Captcha_Handler::OPTIONS['threshold']
			: $captcha['threshold'];

		if (
			( self::PREFIX . $form_id ) === $action &&
			! empty( $body['success'] ) &&
			$score > $threshold
		) {
			return;
		}

		throw new Request_Exception( 'captcha_failed', $body, $response );
	}

	/**
	 * Returns captcha settings for passed form ID
	 *
	 * @param  [type] $post_id [description]
	 *
	 * @return [type]          [description]
	 */
	public function get_data( $form_id = null ) {
		$captcha = Plugin::instance()->post_type->get_recaptcha( $form_id );

		if ( ! $captcha || ! is_array( $captcha ) ) {
			return $this->defaults;
		} elseif ( isset( $captcha['use_global'] ) && $captcha['use_global'] ) {
			return Tab_Handler_Manager::instance()->options(
				'captcha-tab',
				array( 'enabled' => $captcha['enabled'] )
			);

		} else {
			return wp_parse_args( $captcha, $this->defaults );
		}
	}

	public function render( $form_id ) {

		$captcha = $this->get_data( $form_id );

		if ( empty( $captcha['enabled'] ) || empty( $captcha['key'] ) ) {
			return;
		}

		$key = esc_attr( $captcha['key'] );

		wp_enqueue_script(
			'jet-form-builder-recaptcha',
			$this->api_front_url( $key ),
			array(),
			jet_form_builder()->get_version(),
			true
		);

		wp_enqueue_script(
			'jet-form-builder-recaptcha-handler',
			jet_form_builder()->plugin_url( 'assets/js/re-captcha-v3.js' ),
			array( 'jquery' ),
			jet_form_builder()->get_version(),
			true
		);

		$action_prefix = self::PREFIX;

		wp_add_inline_script(
			'jet-form-builder-recaptcha-handler',
			"
		    window.JetFormBuilderReCaptchaConfig = window.JetFormBuilderReCaptchaConfig || {};
		    window.JetFormBuilderReCaptchaConfig[ $form_id ] = { key: '$key', action_prefix: '$action_prefix' };
		",
			'before'
		);

		?>
        <input type="hidden" class="captcha-token" name="<?php echo esc_attr( $this->field_key ); ?>" value="">
		<?php
	}

}
