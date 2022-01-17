<?php

/**
 * The EscapedView specific functionality.
 *
 * @package EightshiftLibs\View
 */

namespace EightshiftLibs\View;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Class EscapedView
 */
abstract class AbstractEscapedView implements ServiceInterface
{
	/**
	 * Tags that are allowed to be rendered for SVG.
	 */
	public const SVG = [
		'svg' => [
			'viewbox' => true,
			'xmlns' => true,
			'xmlns:xlink' => true,
			'height' => true,
			'width' => true,
			'class' => true,
			'fill' => true,
		],
		'defs' => true,
		'path' => [
			'class' => true,
			'd' => true,
			'fill' => true,
			'fill-rule' => true,
			'clip-rule' => true,
			'id' => true,
			'transform' => true,
			'mask' => true,
			'stroke' => true,
			'stroke-width' => true,
			'stroke-linecap' => true,
			'stroke-linejoin' => true,
		],
		'circle' => [
			'id' => true,
			'cx' => true,
			'cy' => true,
			'rx' => true,
			'ry' => true,
			'r' => true,
			'opacity' => true,
			'fill' => true,
			'stroke' => true,
			'transform' => true,
			'mask' => true,
		],
		'ellipse' => [
			'id' => true,
			'cx' => true,
			'cy' => true,
			'rx' => true,
			'ry' => true,
			'opacity' => true,
			'fill' => true,
			'transform' => true,
			'mask' => true,
			'stroke' => true,
		],
		'line' => [
			'x1' => true,
			'y1' => true,
			'x2' => true,
			'y2' => true,
			'id' => true,
			'stroke' => true,
		],
		'g' => [
			'fill' => true,
			'fill-rule' => true,
			'style' => true,
			'stroke' => true,
			'stroke-linecap' => true,
			'transform' => true,
		],
		'filter' => [
			'id' => true,
			'width' => true,
			'height' => true,
			'x' => true,
			'y' => true,
			'filterUnits' => true,
		],
		'feOffset' => [
			'dy' => true,
			'in' => true,
			'result' => true,
		],
		'feGaussianBlur' => [
			'stdDeviation' => true,
			'in' => true,
			'result' => true,
		],
		'feColorMatrix' => [
			'values' => true,
			'in' => true,
		],
		'use' => [
			'fill' => true,
			'filter' => true,
			'xlink:href' => true,
		],
		'mask' => [
			'id' => true,
			'fill' => true,
		],
	];

	/**
	 * Tags that are allowed to be rendered for forms.
	 */
	public const FORM = [
		'input' => [
			'name' => true,
			'value' => true,
			'type' => true,
			'placeholder' => true,
			'class' => true,
			'id' => true,
			'readonly' => true,
			'checked' => true,
			'disabled' => true,
		],
		'select' => [
			'name' => true,
			'id' => true,
			'class' => true,
		],
		'option' => [
			'value' => true,
			'selected' => true,
		],
		'form' => [
			'class' => true,
			'id' => true,
			'method' => true,
			'action' => true,
		],
		'iframe' => [
			'class' => true,
			'src' => true,
			'id' => true,
			'frameborder' => true,
			'allow' => true,
			'allowfullscreen' => true,
		],
		'button' => [
			'class' => true,
			'id' => true,
			'type' => true,
			'onClick' => true,
		],
	];

	/**
	 * Tags that are allowed to be rendered for iframe.
	 */
	public const IFRAME = [
		'iframe' => [
			'class' => true,
			'src' => true,
			'id' => true,
			'frameborder' => true,
			'allow' => true,
			'allowfullscreen' => true,
		],
	];

	/**
	 * Tags that are allowed to be rendered in head.
	 */
	public const HEAD = [
		'meta' => [
			'content' => true,
			'name' => true,
			'charset' => true,
			'http-equiv' => true,
		],
		'link' => [
			'rel' => true,
			'href' => true,
		],
	];
}
