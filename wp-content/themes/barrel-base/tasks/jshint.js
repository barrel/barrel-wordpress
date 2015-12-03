var gulp = require('gulp');
var jshint = require('gulp-jshint');
 
gulp.task('jshint', function() {
  return gulp.src(['src/js/**/*.js'])
    .pipe(jshint())
    .pipe(jshint.reporter());
});