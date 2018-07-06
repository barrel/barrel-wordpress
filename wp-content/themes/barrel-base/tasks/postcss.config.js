// This config file is specifically for critical css
// The ciritical css file is being built using this config file
// as a part of the `npm run build` script using postcss-cli
// Post css, for some reason, requires any config file to be
// named `postcss.config.js`, so we needed to store this one
// in a different directory for now.. Sorry for any confusion :)

const critical = {
  parser: 'postcss-scss',
  plugins: [
    require('./../tasks/postcss-module-import')(),
    require('autoprefixer')({
      browsers: [
        'last 3 versions',
        'iOS >= 8',
        'Safari >= 8',
        'ie 11'
      ]
    }),
    require('postcss-mixins'),
    require('postcss-fontpath')({
      format: [
        {type: 'embedded-opentype', ext: 'eot'},
        {type: 'woff', ext: 'woff'},
        {type: 'truetype', ext: 'ttf'},
        {type: 'svg', ext: 'svg'}
      ],
      checkFiles: true
    }),
    require('precss')(),
    require('postcss-hexrgba')(),
    require('postcss-automath')(),
    require('postcss-critical-split')({
      'output': 'critical'
    }),
    require('cssnano')({
      'preset': 'default'
    })
  ]
}

module.exports = critical
