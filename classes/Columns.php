<?php
defined( 'ABSPATH' ) or die();

final class AC_Columns {

	/**
	 * @since 2.0.1
	 * @var CPAC_Column[]
	 */
	private $columns;

	/**
	 * @since 2.2
	 * @var CPAC_Column[]
	 */
	private $column_types;

	/**
	 * @since NEWVERSION
	 * @var array [ Name => Label ]
	 */
	private $default_columns;

	/**
	 * @var AC_ListScreenAbstract $list_screen
	 */
	private $list_screen;

	public function __construct( AC_ListScreenAbstract $list_screen ) {
		$this->list_screen = $list_screen;
	}

	/**
	 * @since NEWVERSION
	 *
	 * @return CPAC_Column[]
	 */
	public function get_columns() {
		if ( null === $this->columns ) {
			$this->set_columns();
		}

		return $this->columns;
	}

	/**
	 * @return CPAC_Column[]
	 */
	public function get_column_types() {
		if ( null === $this->column_types ) {
			$this->set_column_types();
		}

		return $this->column_types;
	}

	/**
	 * Clears columns variable, which allow it to be repopulated by get_columns().
	 *
	 * @since 2.5
	 */
	public function flush_columns() {
		$this->columns = null;
		$this->column_types = null;
		$this->default_columns = null;
	}

	/**
	 * @since 2.0
	 * @return false|CPAC_Column
	 */
	public function get_column_by_name( $name ) {
		$columns = $this->get_columns();

		return isset( $columns[ $name ] ) ? $columns[ $name ] : false;
	}

	/**
	 * Display column value
	 *
	 * @since NEWVERSION
	 */
	public function get_display_value_by_column_name( $column_name, $id, $value = false ) {
		$column = $this->get_column_by_name( $column_name );

		return $column && ! $column->is_original() ? $column->get_display_value( $id ) : $value;
	}

	/**
	 * @param string $class_name
	 */
	public function register_column_type_by_classname( $class_name ) {

		/* @var CPAC_Column $column */
		$column = new $class_name( $this->get_list_screen()->get_key() );

		$this->register_column_type( $column );
	}

	/**
	 * @param string $dir Absolute path to the column directory
	 * @param string $prefix Autoload prefix
	 */
	public function register_column_types_from_dir( $dir, $prefix ) {
		$class_names = AC()->autoloader()->get_class_names_from_dir( $dir, $prefix );

		foreach ( $class_names as $class_name ) {
			$this->register_column_type_by_classname( $class_name );
		}
	}

	/**
	 * @return AC_ListScreenAbstract|false
	 */
	public function get_list_screen() {
		return $this->list_screen;
	}

	/**
	 * @param CPAC_Column $column
	 */
	private function register_column_type( CPAC_Column $column ) {
		if ( $column->apply_conditional() ) {
			$this->column_types[ $column->get_type() ] = $column;
		}
	}

	/**
	 * @param array $data
	 *
	 * @return CPAC_Column|false
	 */
	public function create_column( $data = array() ) {
		$defaults = array(
			'clone' => false,
			'type'  => false,
			'label' => false,
		);

		$data = array_merge( $defaults, $data );

		$column_type = $this->get_column_type( $data['type'] );

		if ( ! $column_type ) {
			return false;
		}

		$class = get_class( $column_type );

		/* @var CPAC_Column $column */
		$column = new $class( $this->list_screen->get_key() );

		// @deprecated since NEWVERSION
		$column->options = (object) array_merge( (array) $column->options, $data );

		// Populate defaults
		if ( $column->is_original() ) {
			$data['label'] = $this->get_default_label( $data['type'] );

			$column->set_defaults( $data );
		}

		$column->set_clone( $data['clone'] );

		return $column;
	}

	/**
	 * @param array $data Column options
	 */
	private function register_column( CPAC_Column $column ) {
		$this->columns[ $column->get_name() ] = $column;

		do_action( 'ac/column', $column );
	}

	/**
	 * @since NEWVERSION
	 * @param string $column_name Column name
	 */
	public function deregister_column( $column_name ) {
		unset( $this->columns[ $column_name ] );
	}

	/**
	 * @since NEWVERSION
	 */
	private function set_columns() {

		foreach ( $this->get_list_screen()->settings()->get_columns() as $name => $data ) {
			if ( $column = $this->create_column( $data ) ) {
				$this->register_column( $column );
			}
		}

		// Nothing stored. Use WP default columns.
		if ( null === $this->columns ) {
			foreach ( $this->get_default_columns() as $name => $label ) {
				if ( $column = $this->create_column( array( 'type' => $name, 'label' => $label ) ) ) {
					$this->register_column( $column );
				}
			}
		}
	}

	/**
	 * @param string $type
	 *
	 * @return false|CPAC_Column
	 */
	private function get_column_type( $type ) {
		$column_types = $this->get_column_types();

		return isset( $column_types[ $type ] ) ? $column_types[ $type ] : false;
	}

	/**
	 * @var array [ Column Name =>  Column Label ]
	 */
	private function set_default_columns() {
		$this->default_columns = $this->get_list_screen()->settings()->get_default_headings();

		if ( ! $this->default_columns ) {
			$this->default_columns = $this->get_list_screen()->get_default_columns();
		}

		$this->default_columns = apply_filters( 'cac/default_column_names', $this->default_columns, $this->list_screen );
	}

	/**
	 * @return array  [ Column Name =>  Column Label ]
	 */
	private function get_default_columns() {
		if ( null === $this->default_columns ) {
			$this->set_default_columns();
		}

		return $this->default_columns;
	}

	/**
	 * @param string $column_name
	 *
	 * @return string|false
	 */
	public function get_default_label( $column_name ) {
		$default_columns = $this->get_default_columns();

		return isset( $default_columns[ $column_name ] ) ? $default_columns[ $column_name ] : false;
	}

	/**
	 * @param AC_ListScreenAbstract $list_screen
	 *
	 * @return mixed
	 */
	private function get_class_names( AC_ListScreenAbstract $list_screen ) {
		$column_dir = AC()->get_plugin_dir() . 'classes/Column/' . ucfirst( $list_screen->get_type() );

		return AC()->autoloader()->get_class_names_from_dir( $column_dir, 'AC_' );
	}

	/**
	 * Available column types
	 */
	private function set_column_types() {

		// WP default columns
		foreach ( $this->get_default_columns() as $name => $label ) {
			if ( 'cb' === $name ) {
				continue;
			}

			$plugin_column_class_name = apply_filters( 'ac/plugin_column_class_name', 'AC_Column_Plugin' );

			/* @var AC_Column_Plugin $column */
			$column = new $plugin_column_class_name( $this->list_screen->get_key() );

			$column->set_property( 'type', $name );
			$column->set_property( 'label', $label );

			$this->register_column_type( $column );
		}

		// Admin Columns
		$class_names = $this->get_class_names( $this->get_list_screen() );

		// Add-on placeholders
		if ( ! cpac_is_pro_active() ) {

			if ( cpac_is_acf_active() ) {
				$class_names[] = 'AC_Column_ACFPlaceholder';
			}
			if ( cpac_is_woocommerce_active() ) {
				$class_names[] = 'AC_Column_WooCommercePlaceholder';
			}
		}

		// Register column types
		foreach ( $class_names as $class_name ) {
			$this->register_column_type_by_classname( $class_name );
		}

		// For backwards compatibility
		$this->deprecated_register_columns();

		do_action( 'ac/column_types', $this );
	}

	/**
	 * @param AC_ListScreenAbstract $list_screen
	 */
	private function get_post_type( AC_ListScreenAbstract $list_screen ) {
		return method_exists( $list_screen, 'get_post_type' ) ? $list_screen->get_post_type() : false;
	}

	/**
	 * Old way for registering columns. For backwards compatibility.
	 *
	 * @deprecated NEWVERSION
	 */
	private function deprecated_register_columns() {
		$list_screen = $this->get_list_screen();

		$class_names = apply_filters( 'cac/columns/custom', array(), $list_screen );
		$class_names = apply_filters( 'cac/columns/custom/type=' . $list_screen->get_type(), $class_names, $list_screen );
		$class_names = apply_filters( 'cac/columns/custom/post_type=' . $this->get_post_type( $list_screen ), $class_names, $list_screen );

		foreach ( $class_names as $class_name => $path ) {
			$autoload = true;

			// check for autoload condition
			if ( true !== $path ) {
				$autoload = false;

				if ( is_readable( $path ) ) {
					require_once $path;
				}
			}

			if ( ! class_exists( $class_name, $autoload ) ) {
				continue;
			}

			$this->register_column_type_by_classname( $class_name );
		}
	}

}