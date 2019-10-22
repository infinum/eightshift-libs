/**
 * Project Gutenberg config used for Gutenberg specific build.
 *
 * @since 2.0.0
 */

const path = require('path');

/**
 * Return all global objects from window object.
 * Add all Gutenberg external libs so you can use it like @wordpress/lib_name.
 *
 * @since 2.0.0
 */
function getExternals() {
  const ext = {};
  const wplib = [
    'components',
    'compose',
    'dispatch',
    'blocks',
    'element',
    'editor',
    'date',
    'data',
    'i18n',
    'keycodes',
    'viewport',
    'blob',
    'url',
    'apiFetch',
  ];
  wplib.forEach((name) => {
    ext[`@wp/${name}`] = `wp.${name}`;
    ext[`@wordpress/${name}`] = `wp.${name}`;
  });
  ext.ga = 'ga';
  ext.gtag = 'gtag';
  ext.jquery = 'jQuery';
  ext.react = 'React';
  ext['react-dom'] = 'ReactDOM';
  ext.backbone = 'Backbone';
  ext.lodash = 'lodash';
  ext.moment = 'moment';
  ext.tinyMCE = 'tinyMCE';
  ext.tinymce = 'tinymce';

  return ext;
}

module.exports = (options) => {

  // Define libs assets path.
  const blocksHelpers = path.resolve(__dirname, '..', 'assets');

  return {
    externals: getExternals(),
    resolve: {
      alias: {
        EighshiftBlocksDynamicImport: `${blocksHelpers}/scripts/dynamic-import`,
        EighshiftBlocksRegisterBlocks: `${blocksHelpers}/scripts/register-blocks`,
        EighshiftBlocksUcfirst: `${blocksHelpers}/scripts/ucfirst`,
        EighshiftBlocksGetActions: `${blocksHelpers}/scripts/get-actions`,
        EighshiftEditorStyleOverride: `${blocksHelpers}/styles/override-editor.scss`,
        EighshiftComponentColorPalette: `${blocksHelpers}/toolbars/color-palette-custom.js`,
        EighshiftComponentHeadingLevel: `${blocksHelpers}/toolbars/heading-level.js`,
      },
    },
  }
};
