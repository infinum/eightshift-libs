const path = require('path');

// Create Theme/Plugin config variable.
// Define path to the project from the WordPress root. This is used to output the correct path to the manifest.json.
const configData = getConfig(
  'wp-content/themes/eightshift-boilerplate',
  'src/blocks/assets',
  'public'
); // eslint-disable-line no-use-before-define

// Export config to use in other Webpack files.
module.exports = {
  proxyUrl: 'dev.boilerplate.com',
  absolutePath: configData.absolutePath,
  outputPath: configData.outputPath,
  publicPath: configData.publicPath,
  assetsEntry: configData.assetsEntry,
  assetsAdminEntry: configData.assetsAdminEntry,
  blocksEntry: configData.blocksEntry,
  blocksEditorEntry: configData.blocksEditorEntry,
};

// Generate all paths required for Webpack build to work.
function getConfig(projectPathConfig, assetsPathConfig, outputPathConfig) {

  // Clear all slashes from user config.
  const projectPathConfigClean = projectPathConfig.replace(/^\/|\/$/g, '');
  const assetsPathConfigClean = assetsPathConfig.replace(/^\/|\/$/g, '');
  const outputPathConfigClean = outputPathConfig.replace(/^\/|\/$/g, '');

  // Create absolute path from the projects relative path.
  const absolutePath = path.resolve(`/${__dirname}`, '..');

  return {
    absolutePath,

    // Output files absolute location.
    outputPath: path.resolve(absolutePath, outputPathConfigClean),

    // Output files relative location, added before every output file in manifes.json. Should start and end with "/".
    publicPath: path.resolve('/', projectPathConfigClean, outputPathConfigClean, '/'),

    // Source files entries absolute locations.
    assetsEntry: path.resolve(absolutePath, assetsPathConfigClean, 'application.js'),
    assetsAdminEntry: path.resolve(absolutePath, assetsPathConfigClean, 'application-admin.js'),
    blocksEntry: path.resolve(absolutePath, assetsPathConfigClean, 'application-blocks.js'),
    blocksEditorEntry: path.resolve(absolutePath, assetsPathConfigClean, 'application-blocks-editor.js'),
  };
}
