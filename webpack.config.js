const CopyPlugin = require('copy-webpack-plugin');
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,
	entry : {
    'admin-style': './assets/styles/admin.scss',
    'result-script': './assets/scripts/result.js',
    'result-style': './assets/styles/result.scss',
    'form': './assets/scripts/form.js',
  },
  plugins : [
    ...defaultConfig.plugins,
    new CopyPlugin({
      patterns: [
        { from: './assets/images/', to: 'images/', noErrorOnMissing: true, globOptions: { dot: false } },
        { from: './assets/fonts/', to: 'fonts/', noErrorOnMissing: true, globOptions: { dot: false } },
      ],
    }),
  ],
};
