var gulp = require( "gulp" );
var jshint = require( "gulp-jshint" );

gulp.task( "jshint", function() {
  var targets = [ "./gulpfile.js", "./tasks/*.js", "./src/js/**/*.js", "!./src/js/vendor/*.js" ];
  return gulp.src( targets, { base: "./" })
    .pipe( jshint().on( "error", function( e ) {
      console.log( e );
    }) )
    .pipe( jshint.reporter() );
});
