<?php
/**
 * Encoder interface
 *
 * @package    Meta Box
 * @subpackage MB Custom Taxonomy
 */

if ( ! interface_exists( 'MB_CPT_Encoder_Interface' ) ) {
	interface MB_CPT_Encoder_Interface {

		/**
		 * Encode data.
		 *
		 * @param  array $data Data to encode.
		 * @return string      Encoded string.
		 */
		public function encode( $data );
	}
}
