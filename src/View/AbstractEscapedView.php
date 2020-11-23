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
		],
		'defs' => true,
		'path' => [
			'class' => true,
			'd' => true,
			'fill' => true,
			'fill-rule' => true,
			'id' => true,
			'transform' => true,
			'mask' => true,
			'stroke' => true,
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
		],
		'select' => [
			'name' => true,
			'id' => true,
			'class' => true,
		],
		'option' => [
			'value' => true,
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
}
