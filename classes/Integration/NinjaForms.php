<?php

namespace AC\Integration;

use AC\Integration;
use AC\ListScreen;
use AC\ListScreenPost;
use AC\Screen;

class NinjaForms extends Integration {

	public function __construct() {
		parent::__construct(
			'ac-addon-ninjaforms/ac-addon-ninjaforms.php',
			__( 'Ninja Forms', 'codepress-admin-columns' ),
			'assets/images/addons/ninja-forms.png',
			null,
			'ninja-forms'
		);
	}

	public function is_plugin_active() {
		return class_exists( 'Ninja_Forms' );
	}

	public function get_description() {
		return __( 'Add Ninja Forms columns that can be sorted, filtered and directly edited!', 'codepress-admin-columns' );
	}

	private function get_post_types() {
		return array( 'nf_sub' );
	}

	public function show_notice( Screen $screen ) {
		return 'edit' === $screen->get_screen()->base
		       && in_array( $screen->get_screen()->post_type, $this->get_post_types() );
	}

	public function show_placeholder( ListScreen $list_screen ) {
		return $list_screen instanceof ListScreenPost
		       && in_array( $list_screen->get_post_type(), $this->get_post_types() );
	}

}