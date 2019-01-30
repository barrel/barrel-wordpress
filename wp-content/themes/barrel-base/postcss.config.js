const config = {
  plugins: [
    require('./tasks/postcss-module-import'),
    require('postcss-mixins'),
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
    require('postcss-automath'),
    require('postcss-critical-split')({
      'output': process.env.ENV === 'production' ? 'rest' : 'input',
      'startTag': 'defer:start',
      'endTag': 'defer:end',
      'blockTag': 'defer'
    })
  ]
}

module.exports = config
