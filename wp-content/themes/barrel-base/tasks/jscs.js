var gulp = require( "gulp" );
var jscs = require( "gulp-jscs" );

gulp.task( "jscs", function() {
  var gutil = require("gulp-util");
  var targets = [ "./gulpfile.js", "./tasks/*.js", "./src/js/**/*.js", "!./src/js/vendor/*.js" ];
  return gulp.src( targets, { base: "./" })
    .pipe( jscs({ fix: true }) )
    .pipe( jscs.reporter() )
    .pipe( jscs.reporter( "fail" ).on( "error", function( e ) {
      new gutil.PluginError({
        plugin: "JSCS",
        message: e.message
      });
    }) )
    .pipe( gulp.dest( "./" ) );
});
