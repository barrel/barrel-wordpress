var gulp = require( "gulp" );
var jshint = require( "gulp-jshint" );

gulp.task( "jshint", function() {
  return gulp.src([ "gulpfile.js", "tasks/*.js", "src/js/**/*.js" ])
    .pipe( jshint() )
    .pipe( jshint.reporter( "jshint-stylish" ) )
    .pipe( jshint.reporter( "fail" ) );
});
