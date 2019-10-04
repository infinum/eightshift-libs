/* eslint-disable import/no-extraneous-dependencies, global-require*/

// Webpack specific imports.
const merge = require('webpack-merge');
const path = require('path');

// Generate all paths required for Webpack build to work.
function getConfig(projectDir, proxyUrl, projectPathConfig, assetsPathConfig, outputPathConfig) {

  // Clear all slashes from user config.
  const projectPathConfigClean = projectPathConfig.replace(/^\/|\/$/g, '');
  const assetsPathConfigClean = assetsPathConfig.replace(/^\/|\/$/g, '');
  const outputPathConfigClean = outputPathConfig.replace(/^\/|\/$/g, '');

  // Create absolute path from the projects relative path.
  const absolutePath = `${projectDir}`;

  return {
    proxyUrl,
    absolutePath,

    // Output files absolute location.
    outputPath: path.resolve(absolutePath, outputPathConfigClean),

    // Output files relative location, added before every output file in manifes.json. Should start and end with "/".
    publicPath: path.join('/', projectPathConfigClean, outputPathConfigClean, '/'),

    // Source files entries absolute locations.
    assetsEntry: path.resolve(absolutePath, assetsPathConfigClean, 'application.js'),
    assetsAdminEntry: path.resolve(absolutePath, assetsPathConfigClean, 'application-admin.js'),
    blocksEntry: path.resolve(absolutePath, assetsPathConfigClean, 'application-blocks.js'),
    blocksEditorEntry: path.resolve(absolutePath, assetsPathConfigClean, 'application-blocks-editor.js'),
  };
}

// Export project specific configs.
// IF you have multiple builds a flag can be added to the package.json config and use switch case to determin the build config.
module.exports = (mode, configData) => {

  // Create Theme/Plugin config variable.
  // Define path to the project from the WordPress root. This is used to output the correct path to the manifest.json.
  const config = getConfig(
    configData.projectDir,
    configData.projectUrl,
    configData.projectPath,
    configData.assetsPath,
    configData.outputPath
  ); // eslint-disable-line no-use-before-define

  // Other build files.
  
  const base = require('./base')(config);
  const project = require('./project')(config);
  const development = require('./development')(config);
  const production = require('./production')(config);
  const gutenberg = require('./gutenberg')(config);

  // Default output that is going to be merged in any env.
  const outputDefault = merge(base, gutenberg, project);

  // Output development setup by default.
  let output = merge(outputDefault, development);

  // Output production setup if mode is set inside package.json.
  if (mode === 'production') {
    output = merge(outputDefault, production);
  }

  return output;
};
