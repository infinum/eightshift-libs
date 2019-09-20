module.exports = {
  "presets": [
    [
      "@babel/preset-env",
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
      "@babel/plugin-proposal-decorators",
      {
        "legacy": true
      }
    ],
    [
      "@babel/plugin-proposal-class-properties",
      {
        "loose": true
      }
    ],
    "@babel/plugin-syntax-dynamic-import",
    [
      "@babel/plugin-proposal-object-rest-spread",
      {
        "useBuiltIns": true
      }
    ],
		[ "@wordpress/babel-plugin-import-jsx-pragma", {
			"scopeVariable": "createElement",
			"source": "@wordpress/element",
			"isDefault": false
		} ],
		[ "@babel/transform-react-jsx", {
			"pragma": "createElement"
		} ],
    [
      "@babel/plugin-transform-runtime",
      {
        "helpers": true,
        "regenerator": false
      }
    ]
  ]
}
