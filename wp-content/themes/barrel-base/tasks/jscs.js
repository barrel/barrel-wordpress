var gulp = require('gulp');
var jscs = require('gulp-jscs');
 
gulp.task('jscs', function () {
	return gulp.src(['src/js/**/*.js'])
		.pipe(jscs({fix: true}))
		.pipe(jscs.reporter())
		.pipe(jscs.reporter('fail'))
		.pipe(gulp.dest('src'));
});
