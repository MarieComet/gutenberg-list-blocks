<?php
/**
 * Plugin Name:       Gutenberg list blocks
 * Description:       Display Gutenberg Blocks list with <code>[list-blocks context="core"]</code> shortcode. Use context argument to filter by context : all, core, custom-plugin (default to 'all').
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Marie Comet
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gutenberg-list-blocks
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_shortcode( 'list-blocks', 'glb_list_blocks' );
if ( ! function_exists( 'glb_list_blocks' ) ) {
    /**
     * Display a list of blocks formatted as PHP
     * 
     * @param  array $atts    Context to query blocks ('all' by default, 'core', or 'custom-plugin')
	 * @return string
     */
    function glb_list_blocks( $atts ) {
        if ( ! function_exists( 'glb_get_blocks' ) ) {
            return;
        }

        $atts = shortcode_atts( [
            'context' => 'all'
        ], $atts, 'list-blocks' );

        $blocks = glb_get_blocks( $atts[ 'context' ] );

        $title = sprintf( 'WordPress %s : ', get_bloginfo( 'version' ) );

        if ( empty( $blocks ) || ! is_array( $blocks ) ) {
            $title .= sprintf( 'no Blocks for "%s" context.<br />', $atts[ 'context' ] );
        } else {
            $title .= sprintf( 'list of blocks for "%s" context:<br />', $atts[ 'context' ] );
            $result = 'array(<br />';
            foreach( $blocks as $block_name ) {
                $result .= "\t'". $block_name . "',<br />";               
            }
            $result .= ');';
        }

        ob_start();
        ?>
        <p><?php echo $title; ?></p>
        <pre><code><?php echo $result; ?></code></pre>
        <?php
        return ob_get_clean();
    }
}

if ( ! function_exists( 'glb_get_blocks' ) ) {
    /**
     * Query all blocks
     * 
     * @param  string $context    Context to query blocks ('all' by default, 'core', or 'custom-plugin')
	 * @return array
     */
    function glb_get_blocks( $context = 'all' ) {
        if ( ! class_exists( 'WP_Block_Type_Registry' ) ) {
            return;
        }

        $block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();

        $blocks = [];
        if ( is_array( $block_types ) && ! empty( $block_types ) ) {
            foreach( $block_types as $block_name => $block ) {
                if ( ! empty( $context ) && 'all' !== $context && ! str_contains( $block_name, $context ) ) {
                    continue;
                }
                $blocks[] .= $block_name;               
            }
        }

        return $blocks;
    }
}