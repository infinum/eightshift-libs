/* eslint-disable import/no-extraneous-dependencies*/

// Plugins.
const TerserPlugin = require('terser-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

// Define productionConfig setup.
module.exports = () => {

  // All Plugins used in production build.
  const plugins = [

    // Clean public files before next build.
    new CleanWebpackPlugin(),
  ];

  // All Optimizations used in production build.
  const optimization = {
    minimizer: [
      new TerserPlugin({
        cache: true,
        parallel: true,
        terserOptions: {
          output: {
            comments: false,
          },
        },
      }),
    ],
  };

  return {
    plugins,
    optimization,
  
    devtool: 'inline-cheap-module-source-map',
  };
};
