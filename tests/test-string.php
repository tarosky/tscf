<?php
/**
 * Test string utilities.
 */

class TestStringHelpers extends WP_UnitTestCase {

	/**
	 * Test string replacers.
	 */
	public function test_replacer() {
		$lazy_loader = \Tarosky\RenderFaster\Services\LazyLoader::get_instance();
		$src  = file_get_contents( __DIR__ . '/data/img-simple.html' );
		$dest = file_get_contents( __DIR__ . '/data/img-converted.html' );
		$converted = $lazy_loader->convert_attributes( $src, 'img', ['skip'], ['post-thumbnail'] );
		$this->assertEquals( $dest, $converted );
	}
}
