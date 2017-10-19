var gulp = require('gulp')
var livereload = require('gulp-livereload')
var path = require('path')

require('./tasks/sass')
require('./tasks/browserify')
require('./tasks/vendors')
require('./tasks/kraken')

/** Defines the "build" task for Gulp. */
gulp.task('build', [ 'vendors', 'sass', 'browserify' ])

/** Defines the "dev" task for Gulp. */
gulp.task('dev', [ 'vendors', 'sass', 'watchify' ], function () {
  livereload.listen()

  // Watch stylesheets
  gulp.watch([ './**/*.scss' ], [ 'sass' ])

  // Watch handles the scripts
  gulp.watch([ './src/js/**/*.js', './tasks/*.js', './gulpfile.js' ], function (event) {
    if (event.type === 'changed' && path.extname(event.path) === '.js') {
      gulp.src([ event.path ])
    }
  })

  // When compile tasks finish, trigger livereload
  gulp.watch([ './assets/**/*.css', './assets/**/*.js' ], function (event) {
    livereload.changed(event.path)
  })
})

/** Default is "dev". */
gulp.task('default', [ 'dev' ])
