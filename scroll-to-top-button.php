<?php
/*
Plugin Name: Scroll to Top Button
Plugin URI: https://wordpress.org/plugins/scroll-to-top-button/
Description: A simple plugin for displaying a scroll to top button in bottom right corner.
Author: Rene Puchinger
Version: 1.0
Author URI: https://profiles.wordpress.org/rene-puchinger/
License: GPL3

    Copyright (C) 2013  Rene Puchinger

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

@package Scroll_To_Top_Button
@since 1.0

*/

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'Scroll_To_Top_Button' ) ) {

	class Scroll_To_Top_Button {

		var $id = 'scroll_to_top_button';
		private $options;

		public function __construct() {

			add_action( 'wp_head', array( &$this, 'wp_head' ) );
			add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
			add_action( 'admin_init', array( $this, 'page_init' ) );

		}

		/**
		 * WP head action
		 */
		public function wp_head() {

			$this->options = get_option( $this->id . '_option' );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_style( $this->id . '_style', plugins_url( 'assets/css/style.css', __FILE__ ) );
			wp_enqueue_script( $this->id . '_script', plugins_url( 'assets/js/scroll-to-top.js', __FILE__ ), 'jquery' );

			$params = array(
				'scheme' => isset( $this->options['scheme'] ) ? esc_attr( $this->options['scheme'] ) : 'dark',
				'size' => isset( $this->options['size'] ) ? esc_attr( $this->options['size'] ) : 'large'
			);

			wp_localize_script( $this->id . '_script', 'scrollTopParams', $params );

		}

		/**
		 * Add options page
		 */
		public function add_plugin_page() {

			// This page will be under "Appearance"
			add_theme_page(
				'Scroll to Top Button Admin',
				'Scroll to Top Button',
				'manage_options',
				$this->id . '_admin',
				array( $this, 'create_admin_page' )
			);

		}

		/**
		 * Options page callback
		 */
		public function create_admin_page() {

			$this->options = get_option( $this->id . '_option' );

			?>
			<div class="wrap">
				<?php screen_icon(); ?>
				<h2><?php _e( 'Scroll to Top Button Settings', $this->id ); ?></h2>

				<form method="post" action="options.php">
					<?php
					settings_fields( $this->id . '_option_group' );
					do_settings_sections( $this->id . '_admin' );
					submit_button();
					?>
				</form>
			</div>

		<?php

		}

		/**
		 * Register and add settings
		 */
		public function page_init() {

			register_setting(
				$this->id . '_option_group', // Option group
				$this->id . '_option', // Option name
				array( $this, 'sanitize' ) // Sanitize
			);

			add_settings_section(
				$this->id . 'setting_section', // ID
				__( 'Scroll to Top Button Appearance', $this->id ), // Title
				array( $this, 'print_section_info' ), // Callback
				$this->id . '_admin' // Page
			);

			add_settings_field(
				'scheme', // ID
				'Button colour scheme', // Title
				array( $this, 'scheme_callback' ), // Callback
				$this->id . '_admin', // Page
				$this->id . 'setting_section' // Section
			);

			add_settings_field(
				'size',
				'Button size',
				array( $this, 'size_callback' ),
				$this->id . '_admin',
				$this->id . 'setting_section'
			);

		}

		/**
		 * Sanitize each setting field as needed
		 *
		 * @param array $input Contains all settings fields as array keys
		 */
		public function sanitize( $input ) {

			$new_input = array();

			if ( isset( $input['scheme'] ) )
				$new_input['scheme'] = sanitize_text_field( $input['scheme'] );

			if ( isset( $input['size'] ) )
				$new_input['size'] = sanitize_text_field( $input['size'] );

			return $new_input;

		}

		public function print_section_info() {

		}

		public function scheme_callback() {

			$selected = isset( $this->options['scheme'] ) ? esc_attr( $this->options['scheme'] ) : '';

			echo '<select id="' . $this->id . '_scheme" name="' . $this->id . '_option[scheme]">';
			echo '<option value="dark" ' . ( $selected == 'dark' ? 'selected' : '' ) . '>' . __( 'Dark' ) . '</option>';
			echo '<option value="light" ' . ( $selected == 'light' ? 'selected' : '' ) . '>' . __( 'Light' ) . '</option>';
			echo '</select>';

		}

		public function size_callback() {

			$selected = isset( $this->options['size'] ) ? esc_attr( $this->options['size'] ) : '';

			echo '<select id="' . $this->id . '_size" name="' . $this->id . '_option[size]">';
			echo '<option value="large" ' . ( $selected == 'large' ? 'selected' : '' ) . '>' . __( 'Large' ) . '</option>';
			echo '<option value="small" ' . ( $selected == 'small' ? 'selected' : '' ) . '>' . __( 'Small' ) . '</option>';
			echo '</select>';

		}

	}

	new Scroll_To_Top_Button();

}