var gulp = require( "gulp" );
var jscs = require( "gulp-jscs" );

/** the jscs and jshint tasks need improvement */
gulp.task( "jscs", function() {
	return gulp.src([ "gulpfile.js", "tasks/*.js", "src/js/**/*.js" ])
		.pipe( jscs({ fix: true }) )
		.pipe( jscs.reporter( "jscs-stylish" ) )
		.pipe( gulp.dest(function( file ) {
			return file.base;
		}) )
    .pipe( jscs.reporter( "fail") );
});
