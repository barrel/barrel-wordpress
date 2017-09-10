var gulp = require('gulp')
var kraken = require('gulp-kraken')
var krakenConfig = {}

try {
  krakenConfig = require('./kraken.json')
} catch (exception) {
  console.error('\n**************')
  console.error('Missing config.\n- Copy `kraken-sample.json` to `kraken.json`\n')
}

/** Defines the "kraken" task for Gulp. */
gulp.task('kraken', function () {
  return gulp.src('./assets/img/**/*.*')
    .pipe(kraken(krakenConfig))
})
