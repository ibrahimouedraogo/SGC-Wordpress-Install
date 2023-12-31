<?php


namespace Jet_Form_Builder\Compatibility\Woocommerce\Methods\Wc_Product_Modification;


use Jet_Form_Builder\Actions\Methods\Abstract_Modifier;

use Jet_Form_Builder\Actions\Methods\Post_Modification\Post_Thumbnail_Property;
use Jet_Form_Builder\Exceptions\Silence_Exception;

class Product_Image_Property extends Post_Thumbnail_Property {

	public function get_value( Abstract_Modifier $modifier ) {
		parent::get_value( $modifier );
		/** @var Product_Id_Property $id */
		$id      = $modifier->get( 'ID' );
		$product = $id->get_product();

		$product->set_image_id( $this->value );
	}

	public function get_related(): array {
		return array( 'ID' );
	}
}