<?php

/*
Plugin Name: Subpages in Context
Plugin URI: http://www.redletterdesign.net/wp/subpages-in-context
Description: Lists pages and subpages for a given page, or the top ancestor of the current page (default)
Version: 0.1
Author: Brenda Egeland
Author URI: http://www.redletterdesign.net/
License: GPL2
*/

/*
Installation

1. Download and unzip the latest release zip file
2. Upload the entire subpages-in-context directory to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
*/

/*
Using

1. Activate the plugin
2. Drag the widget into the desired locations.
3. Select widget options.
*/

/*
Version History

0.0.1 2012-07-04 Initial development.

/*

/*
Copyright 2012  Brenda Egeland (email: brenda@redletterdesign.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



// Register the widget
function rld_subpages_in_context_register_widget() {
	register_widget( 'Subpages_In_Context' );
}

// Add action to register the widget
add_action( 'widgets_init', 'rld_subpages_in_context_register_widget' );


/**
 * Create the Subpages_In_Context widget
 */
class Subpages_In_Context extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */
	public function __construct() {

		// Widget settings
		$widget_ops = array(
			'classname'   => 'Subpages_In_Context',
			'description' => 'Lists pages and subpages for a given page, or the top ancestor of the current page and all its descendants'
		);

		// Create the widget
		parent::__construct(
		  'subpages-in-context', // Base ID
		  'Subpages in Context', // Name
		  $widget_ops
		);

	} // end function __construct


	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $wpdb, $post;
		extract( $args );

		// We'll be figuring out whether to display the widget at all
		$show_widget = true;

		// Find the top page to be displayed. This is either from the widget
		// settings, or if 'default', the top-most ancestor of the current page
		if ( $instance['top_page'] ) {
			// the user provided the top page to be displayed
			$top_page = $instance['top_page'];
		} else {
		  	// default was selected
			if (is_page()) {
				// We're on a page, so figure out the top-most ancestor
			  	$ancestors = get_post_ancestors( $post->ID );
				if( $ancestors ) {
					// $post->ancestors puts the ids in order such that the highest level
					// ancestor is returned as the last value in the array
					// see http://codex.wordpress.org/Function_Reference/get_post_ancestors
					$top_page = end( $post->ancestors );
				} else {
					// We're on the top-most page already
					$top_page = $post->ID;
				}
			} else {
				// In the default scenario, if we're not on a page, the widget does not display
				$show_widget = false;
			}
		}

		// Show the widget
		if ( $show_widget ) {

			echo $before_widget;

			// Find the title if it is to be shown
			if ( $instance['show_title'] ) {
 	 			if ( $instance['title'] ) {
	  				$title = apply_filters( 'widget_title', $instance['title'] );
	  			} else {
	  				$title = get_the_title( $top_page );
	  			}
	  			if ( $title ) {
	  				echo $before_title . $title . $after_title;
	  			}
			}

			// Determine optional html to surround links in the menu
			$link_addons = '';
			$link_addons .= ( $instance['link_before'] ) ? '&link_before='.$instance['link_before'] : '';
			$link_addons .= ( $instance['link_after']  ) ? '&link_after='.$instance['link_after'] : '';

			// The first element of the list is the top-most page to be displayed...
			$top_page_li = wp_list_pages( 'title_li=&include='.$top_page.'&echo=0'.$link_addons );
			// ...followed by its subpages
			$children = wp_list_pages( 'title_li=&child_of='.$top_page.'&echo=0'.$link_addons );

			// Optional menu class
			$menu_class = ($instance['menu_class']) ? ' class="'.$instance['menu_class'].'"' : '';

			// Output list
			?>
			<ul<?php echo $menu_class;?>>
			<?php
				echo $top_page_li;
				echo $children;
			?>
			</ul>
			<?php

			echo $after_widget;

		}

		return;

	} // end function widget

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']       = strip_tags( $new_instance['title'] );
		$instance['show_title']  = $new_instance['show_title'];
		$instance['top_page']    = ( $new_instance['top_page'] == 'on' );
		$instance['menu_class']  = $new_instance['menu_class'];
		$instance['link_before'] = $new_instance['link_before'];
		$instance['link_after']  = $new_instance['link_after'];

		return $instance;

	} // end function update


	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title'         => '',
			'show_title'    => true,
			'top_page'      => '0',
			'menu_class'    => '',
			'link_before'   => '',
			'link_after'    => ''
		) );
		?>

		<!-- Title -->
		<p><label for="<?php echo $this->get_field_name( 'title' );?>">Title:</label>
		<input class="widefat"
		 	type="text"
		 	id="<?php echo $this->get_field_id( 'title' );?>"
		 	name="<?php echo $this->get_field_name( 'title' );?>"
		 	value="<?php echo $instance['title'];?>" /></p>
		<p class="description">A blank title will be replaced with the top-most ancestor page title</p>

		<!-- Show Title? -->
		<p><label for="<?php echo $this->get_field_name( 'show_title' ); ?>">Show title?
		<input
			type="checkbox"
			id="<?php echo $this->get_field_id( 'show_title' );?>"
			name="<?php echo $this->get_field_name( 'show_title' );?>"
			value="on"
			<?php echo ($instance['show_title']) ? ' checked="checked"' : '';?> /></label></p>

		<!-- Top Page -->
		<p><label for="<?php echo $this->get_field_name( 'top_page' ); ?>">Top Page:</label>
		<?php
		wp_dropdown_pages( array(
			'name'             => $this->get_field_name( 'top_page' ),
			'id'               => $this->get_field_id( 'top_page' ),
			'selected'         => $instance['top_page'],
			'show_option_none' => 'Default'
		) );
		?></p>
		<p class="description">Selecting Default will figure out the top-most ancestor
			of the page currently being displayed, and will display nothing on blog pages.</p>

		<!-- Menu Class -->
		<p><label for="<?php echo $this->get_field_name( 'menu_class' ); ?>">Menu Class:</label>
		<input class="widefat"
			type="text"
			id="<?php echo $this->get_field_id( 'menu_class' );?>"
			name="<?php echo $this->get_field_name( 'menu_class' );?>"
			value="<?php echo $instance['menu_class'];?>" /></p>
		<p class="description">Optional class to add to list</p>

		<!-- Link Before -->
		<p><label for="<?php echo $this->get_field_name( 'link_before' ); ?>">Before Links</label>
		<input class="widefat"
			type="text"
			id="<?php echo $this->get_field_id( 'link_before' );?>"
			name="<?php echo $this->get_field_name( 'link_before' );?>"
			value="<?php echo htmlentities($instance['link_before']);?>" /></p>
		<p class="description">Optional html to add before links</p>

		<!-- Link After -->
		<p><label for="<?php echo $this->get_field_name( 'link_after' ); ?>">After Links</label>
		<input class="widefat"
			type="text"
			id="<?php echo $this->get_field_id( 'link_after' );?>"
			name="<?php echo $this->get_field_name( 'link_after' );?>"
			value="<?php echo htmlentities($instance['link_after']);?>" /></p>
		<p class="description">Optional html to add after links</p>


		<?php

	} // end function form

} // end class Subpages_In_Context
?>