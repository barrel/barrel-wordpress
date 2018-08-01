// This config file is specifically for critical css
// The ciritical css file is being built using this config file
// as a part of the `npm run build` script using postcss-cli
// Post css, for some reason, requires any config file to be
// named `postcss.config.js`, so we needed to store this one
// in a different directory for now.. Sorry for any confusion :)

// This config is based on the main postcss.config.js file in the theme root.
// Any changes in that file will be reflected in this file.
// You sohuld not need to edit this file unless specifically changing the critical css build flow.
const append = [
  require('postcss-critical-split')({
    'output': 'critical'
  }),
  require('cssnano')({
    preset: ['default', {
      discardComments: {
        removeAll: true
      }
    }]
  })
]
const path = require('path')
let config = require(path.join(process.cwd(), 'postcss.config.js'))

config.plugins.pop()

for (var i = 0; i < append.length; i++) {
  config.plugins.push(append[i])
}

module.exports = config
