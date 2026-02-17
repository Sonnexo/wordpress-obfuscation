<?php
/**
 * Obfuscation for sensitive data
 *
 * @package           FabiChessObfuscation
 * @author            FabiChess
 * @copyright         2022-2024 Neotrendy s.r.o., 2026 FabiChess
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Data Obfuscation
 * Description:       Data Obfuscation prevents data harvesting by hiding sensitive data appearing in your pages, while remaining visible to your site visitors.
 * Version:           1.3.0
 * Requires at least: 2.5
 * Requires PHP:      5.6
 * Author:            Fabian Czappa
 * Text Domain:       fabi-chess-obfuscation
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

function obfuscate_with_html_comments(string $input): string {
    $chars = preg_split('//u', $input, -1, PREG_SPLIT_NO_EMPTY);

    if ($chars === false) {
        return $input;
    }

    return implode('<!-- -->', $chars);
}

/**
 * Adds shortcode to obfuscate email address.
 *
 * @param array $atts Associative array of attribute name and value pairs.
 *
 * @return string Converted email address or HTML anchor element.
 */
function fabichess_email_address_obfuscation_shortcode( $atts ) {
		$atts = shortcode_atts( array(
				'email' => '',
				'link'  => false,
				'class' => '',
				'htmlobfuscation' => '',
		), $atts, 'obfuscate_email' );

		$email_parts      = explode( '?', $atts['email'], 2 );
		$query_parameters = isset( $email_parts[1] ) ? '?' . esc_attr( $email_parts[1] ) : '';
		$email            = sanitize_email( $email_parts[0] ) . $query_parameters;
	
		$html_obfuscation = isset( $atts['htmlobfuscation'] ) ? True : False;

		if ( empty( $email ) ) {
				return '';
		}
	
		if ( filter_var( $atts['link'], FILTER_VALIDATE_BOOLEAN ) ) {
				$obfuscated_email = antispambot( $email );
				$class = ! empty( $atts['class'] ) ? ' class="' . esc_attr( $atts['class'] ) . '"' : '';

				return sprintf( '<a href="mailto:%1$s" title="%1$s"%2$s>%1$s</a>', $obfuscated_email, $class );
		}

		if ( $html_obfuscation ) {
				$obfuscated_email = obfuscate_with_html_comments( $email );
		} else {			
				$obfuscated_email = antispambot( $email );
		}
	
		return $obfuscated_email;
}

/**
 * Adds shortcode to obfuscate various data.
 *
 * @param array $atts Associative array of attribute name and value pairs.
 *
 * @return string Converted data or HTML anchor element.
 */
function fabichess_data_obfuscation_shortcode( $atts ) {
		$atts = shortcode_atts( array(
				'data' => '',
		), $atts, 'obfuscate_data' );

		$obfuscated = obfuscate_with_html_comments( $atts['data'] );
		return $obfuscated;
}

add_shortcode( 'obfuscate_email', 'fabichess_email_address_obfuscation_shortcode' );
add_shortcode( 'obfuscate_data', 'fabichess_data_obfuscation_shortcode' );
