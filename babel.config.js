const path = require('path');

module.exports = {
  "presets": [
    [
      path.join(__dirname, 'node_modules/@babel/preset-env'),
      {
        "modules": false,
        "useBuiltIns": "entry",
        "corejs": "2",
        "targets": {
          "browsers": [
            "android >= 4.2",
            "last 2 versions",
            "Safari >= 8",
            "not ie < 11"
          ]
        }
      }
    ]
  ],
  "plugins": [
    [
      path.join(__dirname, 'node_modules/@babel/plugin-proposal-decorators'),
      {
        "legacy": true
      }
    ],
    [
      path.join(__dirname, 'node_modules/@babel/plugin-proposal-class-properties'),
      {
        "loose": true
      }
    ],
    path.join(__dirname, 'node_modules/@babel/plugin-syntax-dynamic-import'),
    [
      path.join(__dirname, 'node_modules/@babel/plugin-proposal-object-rest-spread'),
      {
        "useBuiltIns": true
      }
    ],
    [ 
      path.join(__dirname, 'node_modules/@wordpress/babel-plugin-import-jsx-pragma'),
       {
      "scopeVariable": "createElement",
      "source": "@wordpress/element",
      "isDefault": false
    } ],
    [
      path.join(__dirname, 'node_modules/@babel/plugin-transform-react-jsx'),
      {
      "pragma": "createElement"
    } ],
    [
      path.join(__dirname, 'node_modules/@babel/plugin-transform-runtime'),
      {
        "absoluteRuntime": true,
      }
    ]
  ]
}
