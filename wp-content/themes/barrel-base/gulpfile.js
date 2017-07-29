var gulp = require('gulp')
var livereload = require('gulp-livereload')
var path = require('path')

require('./tasks/sass')
require('./tasks/jscs')
require('./tasks/jshint')
require('./tasks/browserify')
require('./tasks/vendors')
require('./tasks/kraken')

/** Defines the "build" task for Gulp. */
gulp.task('build', [ 'vendors', 'sass', 'browserify' ])

/** Defines the "validate" task for Gulp. */
gulp.task('validate', [ 'jshint', 'jscs' ])

/** Defines the "dev" task for Gulp. */
gulp.task('dev', [ 'vendors', 'sass', 'watchify' ], function () {
  livereload.listen()

  // Watch stylesheets
  gulp.watch([ './**/*.scss' ], [ 'sass' ])

  // Watch handles the scripts
  gulp.watch([ './src/js/**/*.js', './tasks/*.js', './gulpfile.js' ], function (event) {
    if (event.type == 'changed') {
      if (path.extname(event.path) == '.js') {
        var jscs = require('gulp-jscs')
        var jshint = require('gulp-jshint')

        gulp.src([ event.path ])
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'))
        .pipe(jscs({ fix: true }))
        .pipe(jscs.reporter('jscs-stylish'))
      }
    }
  })

  // When compile tasks finish, trigger livereload
  gulp.watch([ './assets/**/*.css', './assets/**/*.js' ], function (event) {
    livereload.changed(event.path)
  })
})

/** Default is "dev". */
gulp.task('default', [ 'dev' ])
