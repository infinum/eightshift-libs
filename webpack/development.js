/* eslint-disable import/no-extraneous-dependencies*/

// Plugins.
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');

// Define developmentConfig setup.
module.exports = (config) => {

  // All Plugins used in development build.
  const plugins = [

    // Use BrowserSync to se live preview of all changes.
    new BrowserSyncPlugin(
      {
        host: 'localhost',
        port: 3000,
        proxy: config.proxyUrl,
        files: [
          {
            match: [
              '**/*.php',
              '**/*.css',
            ],
          },
        ],
        notify: true,
      },
      {
        reload: true,
      },
    ),
  ];

  return {
    plugins,

    devtool: false,
  };
};
