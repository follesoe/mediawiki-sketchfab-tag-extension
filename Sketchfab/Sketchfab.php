<?php
/**
 * A MediaWiki Tag Extension adding support for embedding Sketchfab 3D models.
 *
 * @author Jonas Follesø <jonas@follesoe.no>
 * @copyright © 2018 Jonas Follesø
 * @license The MIT License
 *
 * Copyright © 2018 Jonas Follesø
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

$wgHooks['ParserFirstCallInit'][] = 'Sketchfab::registerTags';

class Sketchfab {
	/**
	 * Register the new tags with the Parser.
	 */
	public static function registerTags( Parser $parser ) {
		$parser->setHook( 'sketchfab', array( __CLASS__, 'embedSketchfab' ) );
		return true;
	}

	/**
	 * Render the <sketchfab> tag.
	 */
	public static function embedSketchfab( $input, $argv, Parser $parser ) {
		$parser->disableCache();

		$sfid = '';
		$width = $width_max = 425;
		$height = $height_max = 355;
		$urlBase = 'https://sketchfab.com/models/';

		if ( !empty( $argv['sfid'] ) ) {
			$sfid = $argv['sfid'];
		} elseif ( !empty ( $input ) ) {
			$sfid = self::getSketchfabId( $input );
		}

		//  Did we not get an ID at all? If not do not generate any HTML.
		if ( $sfid === false ) {
			return '';
		}

		// Support the pixel unit (px) for height/width parameters in case users
		// provide values like 640px instead of 640.
		if ( !empty( $argv['height'] ) ) {
			$argv['height'] = str_replace( 'px', '', $argv['height'] );

			if ( filter_var( $argv['height'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) && $argv['height'] <= $height_max ) {
				$height = $argv['height'];
			}
		}

		if ( !empty( $argv['width'] ) ) {
			$argv['width'] = str_replace( 'px', '', $argv['width'] );

			if ( filter_var( $argv['width'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 0 ) ) ) && $argv['width'] <= $height_max ) {
				$width = $argv['width'];
			}
		}

		if ( !empty( $sfid ) ) {
			$embed_options = self::getEmbedOptions( $argv );
			$url = $urlBase . $sfid . $embed_options;
			$html = "<div class=\"sketchfab-embed-wrapper\">
						<iframe
							width=\"{$width}\"
							height=\"{$height}\"
							src=\"{$url}\"
							frameborder=\"0\"
							allowvr
							allowfullscreen
							mozallowfullscreen=\"true\"
							webkitallowfullscreen=\"true\"
							onmousewheel=\"\">
						</iframe>
				</div>";
			return array( $html, 'markerType' => 'nowiki' );
		}

		return '';
	}

	/**
	 * Extracts the Sketchfab Object Id from the $url argument.
	 *
	 * @param string $url Sketchfab Url
	 * @return string Sketchfab Object ID on success, boolean false on failure.
	 */
	private static function getSketchfabId( $url ) {
		$pattern = '~[https|http]:\/\/sketchfab\.com\/models\/(.+)*~i';
		$id = false;

		if ( preg_match( $pattern, $url, $preg ) ) {
			$id = $preg[1];
		} elseif ( preg_match( '/([0-9A-Za-z_-]+)/', $url, $preg ) ) {
			$id = $preg[1];
		}
		return $id;
	}

	/**
	 * Generate the embed? query string for Sketchfab based on arguments used
	 * on the <sketchfab> element.
	 */
	private static function getEmbedOptions( $argv ) {
		// White list of user settable attributes that map directly to Sketchfab properties:
		// https://help.sketchfab.com/hc/en-us/articles/203509907-Embed-Models
		$custom_attributes_whitelist = array(
			'annotation',
			'annotation_cycle',
			'annotations_visible',
			'autospin',
			'autostart',
			'cardboard',
			'camera',
			'fps_speed',
			'navigation',
			'preload',
			'scrollwheel',
			'ui_stop',
			'transparent',
			'ui_animations',
			'ui_annotations',
			'ui_controls',
			'ui_fullscreen',
			'ui_general_controls',
			'ui_help',
			'ui_hint',
			'ui_infos',
			'ui_inspector',
			'ui_inspector_open',
			'ui_settings',
			'ui_vr',
			'ui_watermark_link',
			'ui_watermark'
		);

		$embed_params = '/embed?';

		foreach ( $custom_attributes_whitelist as $attr_name ) {
			if ( !empty( $argv[$attr_name] ) ) {
				$attr_value = $argv[$attr_name];
				$embed_params .= "{$attr_name}={$attr_value}&";
			}
		}

		return $embed_params;
	}
}
?>