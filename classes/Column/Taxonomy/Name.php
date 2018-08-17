<?php

namespace AC\Column\Taxonomy;

use AC;
use AC\Settings;

/**
 * @since NEWVERSION
 */
class Name extends AC\Column {

	public function __construct() {
		$this->set_original( true );
		$this->set_type( 'name' );
	}

	public function register_settings() {
		$this->add_setting( new Settings\Column\Pro\Editing( $this ) );
		$this->add_setting( new Settings\Column\Pro\Export( $this ) );
	}
}