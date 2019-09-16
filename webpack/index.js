const path = require('path');

// Define libs assets path.
const blocksHelpers = path.resolve(__dirname, '..', 'assets');

// Define all external components used in Gutenberg.
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

// Add all Gutenberg external libs so you can use it like @wordpress/lib_name.
const externals = (function() {
  const ext = {};
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
})();

// Export everything so it can be used in project webpack config.
module.exports = {
  externals,
  resolve: {
    alias: {
      EighshiftBlocksDynamicImport: `${blocksHelpers}/scripts/dynamic-import`,
      EighshiftBlocksRegisterBlocks: `${blocksHelpers}/scripts/register-blocks`,
      EighshiftBlocksUcfirst: `${blocksHelpers}/scripts/ucfirst`,
      EighshiftBlocksGetActions: `${blocksHelpers}/scripts/get-actions`,
      EighshiftEditorStyleOverride: `${blocksHelpers}/styles/override-editor.scss`,
    },
  },
};
