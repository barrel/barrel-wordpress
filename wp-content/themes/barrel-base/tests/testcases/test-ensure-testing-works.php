<?php

class EnsureTestingWorksTest extends KindTestCase {
	
	function test_tests() {
		$this->assertTrue( true );
	}
	
	function test_active_theme() {
		$this->assertTrue( 'Barrel Base' == wp_get_theme() );
	}
}
