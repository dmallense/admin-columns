<?php

class AC_Admin_Addon_NinjaForms extends AC_Admin_Addon {

	public function __construct() {
		parent::__construct( 'ac-addon-ninjaforms' );

		$this
			->set_title( __( 'Ninja Forms', 'codepress-admin-columns' ) )
			->set_logo( AC()->get_plugin_url() . 'assets/images/addons/ninja-forms.png' )
			->set_icon( AC()->get_plugin_url() . 'assets/images/addons/ninja-forms-icon.png' )
			->set_link( ac_get_site_utm_url( 'ninja-forms', 'addon', 'ninjaforms' ) )
			->set_description( "Add Ninja Forms columns that can be sorted, filtered and directly edited!" )
			->add_plugin( 'ninjaforms' );
	}

	public function display_promo() {
		echo $this->get_title() . ' ';
		$this->display_icon();
	}

	public function is_notice_screen() {
		global $pagenow;

		return 'admin.php' === $pagenow && in_array( filter_input( INPUT_GET, 'page' ), array( 'bp-activity', 'bp-groups' ) );
	}

	public function is_plugin_active() {
		return class_exists( 'Ninja_Forms' );
	}

}