<?php
/**
 * File that displays the custom meta box for the breaking custom post type.
 *
 * PHP version 7.3
 *
 * @link       https://jacobmartella.com
 * @since      2.0.0
 *
 * @package    Read_More_About
 * @subpackage Read_More_About/admin/partials
 */

global $post;
global $posts_array;
global $in_ex_array;
global $color_array;

$args        = array( 'numberposts' => -1 );
$posts_array = [];
$more_posts  = get_posts( $args );
foreach ( $more_posts as $the_post ) {
	setup_postdata( $the_post );
	$the_id                 = get_the_ID();
	$name                   = get_the_title();
	$posts_array[ $the_id ] = $name;
}

$color_array             = [];
$color_array['light']    = 'Light';
$color_array['dark']     = 'Dark';
$in_ex_array['external'] = __( 'External', 'read-more-about' );
$in_ex_array['internal'] = __( 'Internal', 'read-more-about' );

$links        = get_post_meta( $post->ID, 'read_more_links', true );
$color_scheme = get_post_meta( $post->ID, 'read_more_color_scheme', true );
wp_nonce_field( 'read_more_about_meta_box_nonce', 'read_more_about_meta_box_nonce' );

echo '<div id="read-more-repeatable-fieldset-one" width="100%">';

echo '<table class="read-more-link-field"><tr>';
echo '<td><label for="read_more_color_scheme">' . esc_html__( 'Color Scheme', 'read-more-about' ) . '</label></td>';
echo '<td><select class="read_more_color_scheme" name="read_more_color_scheme">';
foreach ( $color_array as $key => $name ) {
	if ( $key === $color_scheme ) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $name ) . '</option>';
}
echo '</select></td>';
echo '</tr></table>';

// Check for fields already filled out.
if ( $links ) {

	// Loop through each link the user has already entered.
	foreach ( $links as $more_link ) {
		echo '<table class="read-more-link-fields">';
		echo '<tr>';
		echo '<td><label for="read_more_about_in_ex">' . esc_html__( 'External/Internal Link', 'read-more-about' ) . '</label></td>';
		echo '<td><select class="read_more_about_in_ex" name="read_more_about_in_ex[]">';
		foreach ( $in_ex_array as $key => $name ) {
			if ( $key === $more_link['read_more_about_in_ex'] ) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			echo '<option value="' . $key . '" ' . $selected . '>' . $name . '</option>';
		}
		echo '</select></td>';
		echo '</tr>';

		if ( 'internal' === $more_link['read_more_about_in_ex'] ) {
			$style = 'style="display:none;"';
		} else {
			$style = '';
		}
		echo '<tr class="external-link"' . $style .  '>';
		echo '<td><label for="read_more_about_link">' . esc_html__( 'External URL', 'read-more-about' ) . '</label></td>';
		echo '<td><input type="text" name="read_more_about_link[]" id="read_more_about_link" value="' . esc_attr( $more_link['read_more_about_link'] ) . '" /></td>';
		echo '</tr>';

		if ( 'internal' === $more_link['read_more_about_in_ex'] ) {
			$style = 'style="display:none;"';
		} else {
			$style = '';
		}
		echo '<tr class="external-title"' . $style . '>';
		echo '<td><label for="read_more_about_external_title">' . esc_html__( 'External URL Title', 'read-more-about' ) . '</label></td>';
		echo '<td><input type="text" name="read_more_about_external_title[]" id="read_more_about_external_title" value="' . esc_attr( $more_link['read_more_about_external_title'] ) . '" /></td>';
		echo '</tr>';

		if ( 'external' === $more_link['read_more_about_in_ex'] ) {
			$style = 'style="display:none;"';
		} else {
			$style = 'style="display:table-row;';
		}
		echo '<tr class="internal-link" ' . esc_attr( $style ) . '">';
		echo '<td><label for="read_more_about_internal_link">' . esc_html__( 'Internal Post', 'read-more-about' ) . '</label></td>';
		echo '<td><select id="read_more_about_internal_link" name="read_more_about_internal_link[]">';
		foreach ( $posts_array as $key => $name ) {
			if ( $key === $more_link['read_more_about_internal_link'] ) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $name ) . '</option>';
		}
		echo '</select></td>';
		echo '</tr>';

		if ( isset( $more_link['read_more_about_description'] ) ) {
			$description = $more_link['read_more_about_description'];
		} else {
			$description = '';
		}
		echo '<tr class="read-more-description">';
		echo '<td><label for="read_more_about_description">' . esc_html__( 'Link Description', 'read-more-about' ) . '</label></td>';
		echo '<td><input type="text" name="read_more_about_description[]" id="read_more_about_description" value="' . esc_attr( $description ) . '" /></td>';
		echo '</tr>';

		echo '<tr><td><a class="button read-more-remove-row" href="#">' . esc_html__( 'Remove Link', 'read-more-about' ) . '</a></td></tr>';
		echo '</table>';

	} //* End foreach

} else {
	// Show a blank set of fields if there are no fields filled in.
	echo '<table class="read-more-link-fields">';
	echo '<tr>';
	echo '<td><label for="read_more_about_in_ex">' . esc_html__( 'External/Internal Link', 'read-more-about' ) . '</label></td>';
	echo '<td><select class="read_more_about_in_ex" name="read_more_about_in_ex[]">';
	foreach ( $in_ex_array as $key => $name ) {
		echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $name ) . '</option>';
	}
	echo '</select></td>';
	echo '</tr>';

	echo '<tr class="external-link">';
	echo '<td><label for="read_more_about_link">' . esc_html__( 'External URL', 'read-more-about' ) . '</label></td>';
	echo '<td><input type="text" name="read_more_about_link[]" id="read_more_about_link" value="" /></td>';
	echo '</tr>';

	echo '<tr class="external-title">';
	echo '<td><label for="read_more_about_external_title">' . esc_html__( 'External URL Title', 'read-more-about' ) . '</label></td>';
	echo '<td><input type="text" name="read_more_about_external_title[]" id="read_more_about_external_title" value="" /></td>';
	echo '</tr>';

	echo '<tr class="internal-link">';
	echo '<td><label for="read_more_about_internal_link">' . esc_html__( 'Internal Post', 'read-more-about' ) . '</label></td>';
	echo '<td><select id="read_more_about_internal_link" name="read_more_about_internal_link[]">';
	foreach ( $posts_array as $key => $name ) {
		echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_attr( $name ) . '</option>';
	}
	echo '</select></td>';
	echo '</tr>';

	echo '<tr class="read-more-description">';
	echo '<td><label for="read_more_about_description">' . esc_html__( 'Link Description', 'read-more-about' ) . '</label></td>';
	echo '<td><input type="text" name="read_more_about_description[]" id="read_more_about_description" value="" /></td>';
	echo '</tr>';

	echo '<tr><td><a class="button read-more-remove-row" href="#">' . esc_html__( 'Remove Link', 'read-more-about' ) . '</a></td></tr>';

	echo '</table>';
}

// Set up a hidden group of fields for the jQuery to grab.
echo '<table class="read-more-empty-row screen-reader-text">';
echo '<tr>';
echo '<td><label for="read_more_about_in_ex">' . esc_html__( 'External/Internal Link', 'read-more-about' ) . '</label></td>';
echo '<td><select class="new-field read_more_about_in_ex"  name="read_more_about_in_ex[]" disabled="disabled">';
foreach ( $in_ex_array as $key => $name ) {
	if ( $key === $link['read_more_about_in_ex'] ) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_attr( $name ) . '</option>';
}
echo '</select></td>';
echo '</tr>';

echo '<tr class="external-link" >';
echo '<td><label for="read_more_about_link">' .  esc_html__( 'External URL', 'read-more-about' ) . '</label></td>';
echo '<td><input class="new-field" type="text" name="read_more_about_link[]" id="read_more_about_link" value="" disabled="disabled" /></td>';
echo '</tr>';

echo '<tr class="external-title">';
echo '<td><label for="read_more_about_external_title">' . esc_html__( 'External URL Title', 'read-more-about' ) . '</label></td>';
echo '<td><input class="new-field" type="text" name="read_more_about_external_title[]" id="read_more_about_external_title" value="" disabled="disabled" /></td>';
echo '</tr>';

echo '<tr class="internal-link">';
echo '<td><label for="read_more_about_internal_link">' . esc_html__( 'Internal Post', 'read-more-about' ) . '</label>';
echo '<td><select class="new-field" id="read_more_about_internal_link" name="read_more_about_internal_link[]" disabled="disabled">';
foreach ( $posts_array as $key => $name ) {
	if ( $key === $link['read_more_about_in_ex'] ) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_attr( $name ) . '</option>';
}
echo '</select></td>';
echo '</tr>';

echo '<tr class="read-more-description">';
echo '<td><label for="read_more_about_description">' . esc_html__( 'Link Description', 'read-more-about' ) . '</label></td>';
echo '<td><input class="new-field" type="text" name="read_more_about_description[]" id="read_more_about_description" value="" disabled="disabled" /></td>';
echo '</tr>';

echo '<tr><td><a class="button read-more-remove-row" href="#">' . esc_html__( 'Remove Link', 'read-more-about' ) . '</a></td></tr>';
echo '</table>';

echo '</div>';
echo '<p><a id="read-more-add-row" class="button" href="#">' . esc_html__( 'Add Link', 'read-more-about' ) . '</a></p>';
