var gulp = require('gulp')
var browserify = require('browserify')
var watchify = require('watchify')
var shim = require('browserify-shim')
var fs = require('fs')
var path = require('path')
var glob = require('glob')
var gutil = require('gulp-util')
var prettyHrtime = require('pretty-hrtime')

/** Defines the "browserify" task for Gulp. */
gulp.task('browserify', function (callback) {
  return browserifyTask(false, callback)
})

/** Defines the "watchify" task for Gulp. */
gulp.task('watchify', function () {
  return browserifyTask(true)
})

/**
 * Runs the Browserify or Watchify bundler.
 * @param {boolean} dev - "True" to configure the task for development.
 * @param {function} cb - Async callback function.
 */
function browserifyTask (dev, cb) {
  var bundleOpts = {
    entries: './src/js/main.js',
    output: './assets/js/main.min.js',
    transform: [
      [ shim, {
        global: true
      } ]
    ],
    debug: true
  }

  var b = browserify(bundleOpts)

  var outputFile = path.basename(bundleOpts.output)

  // Bundle all scripts from the modules folder
  b.require(getThemeModules())

  // Add minify plugin w/ source map options
  b.plugin('minifyify', {
    map: outputFile + '.map',
    output: bundleOpts.output + '.map',
    compressPath: function (p) {
      // Start relative paths from root
      return path.join('../../', p)
    }
  })

  // Re-usable function to start the bundler instance
  function bundle () {
    bundleLogger.start('main.min.js')

    var bundle = b.bundle()
      .on('error', function (err) { console.error(err.message) })
      .on('end', function () {
        bundleLogger.end('main.min.js')

        if (typeof cb !== 'undefined') {
          cb()
        }
      })
      .pipe(fs.createWriteStream(bundleOpts.output))
  }

  // Use watchify for development mode
  if (dev) {
    b = watchify(b)
    b.on('update', bundle)
  }

  return bundle()
}

/**
 * Provides an array of module scripts for browserify to include.
 * Makes module scripts available to require as "modules/[name]".
 */
function getThemeModules () {
  var moduleFiles = glob.sync('./modules/**/*.js')
  var modules = []

  for (var i = 0; i < moduleFiles.length; i++) {
    var name = path.basename(moduleFiles[ i ], '.js')
    var dirname = path.basename(path.dirname(moduleFiles[ i ]))

    if (name === dirname) {
      modules.push({
        file: './' + moduleFiles[ i ],
        expose: 'modules/' + name
      })
    }
  }

  return modules
}

var startTime

/** Some logging functions for Browserify, originally from gulp-starter.
 */
var bundleLogger = {
  start: function (filepath) {
    startTime = process.hrtime()
    gutil.log('Bundling', gutil.colors.green(filepath), '...')
  },

  end: function (filepath) {
    var taskTime = process.hrtime(startTime)
    var prettyTime = prettyHrtime(taskTime)
    gutil.log('Bundled', gutil.colors.green(filepath), 'in', gutil.colors.magenta(prettyTime))
  }
}
