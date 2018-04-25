const config = {
  parser: 'postcss-scss',
  plugins: [
    require('./tasks/postcss-module-import'),
    require('postcss-sassy-mixins'),
    require('postcss-fontpath')({
      format: [
        { type: 'embedded-opentype', ext: 'eot' },
        { type: 'woff', ext: 'woff' },
        { type: 'truetype', ext: 'ttf' },
        { type: 'svg', ext: 'svg'}
      ],
      checkFiles: true
    }),
    require('autoprefixer')({
      browsers: [
        'last 3 versions',
        'iOS >= 8',
        'Safari >= 8',
        'ie 11'
      ]
    }),
    require('precss'),
    require('postcss-hexrgba'),
    require('postcss-automath')
  ]
}

module.exports = config
