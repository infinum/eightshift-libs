/* eslint-disable import/no-extraneous-dependencies*/

// Webpack specific imports.
const webpack = require('webpack');

// Plugins.
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const ManifestPlugin = require('webpack-manifest-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');

// Main Webpack build setup.
module.exports = (config) => {

  // All Plugins used in production and development build.
  const plugins = [

    // Provide global variables to window object.
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
    }),

    // Create manifest.json file.
    new ManifestPlugin({
      seed: {},
    }),

    new MiniCssExtractPlugin({
      filename: '[name]-[hash].css',
    }),

    // Copy files to new destination.
    new CopyWebpackPlugin([

      // Find jQuery in node_modules and copy it to public folder
      {
        from: `${config.absolutePath}/node_modules/jquery/dist/jquery.min.js`,
        to: config.output,
      },
    ]),
  ];

  // All Optimizations used in production and development build.
  const optimization = {
    runtimeChunk: false,
  };

  // All Loaders used in production and development build.
  const loaders = {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: 'babel-loader',
      },
      {
        test: /\.(png|svg|jpg|jpeg|gif|ico)$/,
        exclude: [/fonts/, /node_modules/],
        use: 'file-loader?name=[name].[ext]',
      },
      {
        test: /\.(eot|otf|ttf|woff|woff2|svg)$/,
        exclude: [/images/, /node_modules/],
        use: 'file-loader?name=[name].[ext]',
      },
      {
        test: /\.scss$/,
        exclude: /node_modules/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              sourceMap: true,
              url: false,
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
            },
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true,
            },
          },
          {
            loader: 'import-glob-loader',
          },
        ],
      },
    ],
  };

  return {
    optimization,
    plugins,
    module: loaders,
  };
};
