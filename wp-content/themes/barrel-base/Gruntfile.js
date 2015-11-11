var path = require('path');

module.exports = function(grunt) {

	// Load grunt tasks

	require('load-grunt-tasks')(grunt, {pattern: ['grunt-*', 'assemble']});
	
	// Configure Grunt tasks

	grunt.initConfig({
		
		browserify: {
			options: {
				browserifyOptions:{
					debug:true
				},
				preBundleCB: function(b) {
					b.plugin('minifyify', {
					 map: 'main.js.map',
					 output: 'assets/js/main.min.js.map',
				 });
				}
			},
			client: {
				src: ['src/js/app.js'],
				dest: 'assets/js/main.min.js',
			}
		},
		
		uglify: {
			compress: {
				files: {
					'assets/js/vendor.min.js': [
						'src/js/vendor/jquery/dist/jquery.js',
						'src/js/vendor/underscore/underscore.js',
						'src/js/vendor/bezier-easing/bezier-easing.js',
						'src/js/vendor/fancybox/source/jquery.fancybox.js',
						'src/js/vendor/jquery-form/jquery.form.js',
						'src/js/vendor/jquery.easing/js/jquery.easing.js',
						'src/js/vendor/jquery.transit/jquery.transit.js',
						'src/js/vendor/jquery.stellar/jquery.stellar.js',
						'src/js/vendor/simpleStorage/simpleStorage.js',
						'src/js/vendor/waypoints/waypoints.js',
						'src/js/vendor/slick-carousel/slick/slick.min.js',
						'src/js/vendor/jquery.scrollTo/jquery.scrollTo.min.js',
						'src/js/vendor--non-bower/rAF.js',
						'src/js/vendor--non-bower/CF7_scripts.js'
					]
				},
				options: {
					mangle: true
				}
			}
		},
		
		sass: { 
			 theme: {			 
				 options: {
					 style: 'compressed'
				 },
				 files: {
					 'assets/css/styles.min.css':'src/sass/styles.scss'
				}
			},
			admin: {
				 options: {
					 style: 'compressed'
				 },
				 files: {
					 'assets/css/admin.min.css':'src/sass/admin.scss'
				}
			}
		},
		
		autoprefixer: {
			options: {
				map: true,
				browsers: ['> 1%', 'last 4 versions', 'Firefox ESR', 'ie 8', 'ie 9']
			},
			dist: {
				src: 'assets/css/styles.min.css',
				dest: 'assets/css/styles.min.css'
			}
		},
		
		image_resize: {
			 options: {
				width: '50%'
			},
			resized: {
				files: [
					{'assets/img/sprite-sm.png': 'assets/img/sprite.png'}
				]
			},
		},
		
		sprite:{
			ui: {
				engine: 'pngsmith',
				algorithm: 'binary-tree',
				src: 'src/sprite/*.png',
				destImg: 'assets/img/sprite.png',
				imgPath: '../../../img/sprite.png',
				cssTemplate: 'src/template/sprite.less.mustache',
				destCSS: 'src/sass/_theme/global/sprite.scss',
				padding: 5
			}
		},

		watch: {
			 sass_theme: {
				options:{
					livereload: true,
					reload: true,
				 	spawn: false
				},
				files: ['src/sass/**/*.scss'],
				tasks: ['sass:theme', 'autoprefixer']
			 },
			sprite: {
				options: {
					livereload: true
				},
				files: ['src/sprite/*.png', 'src/template/sprite.less.mustache'],
				tasks: ['sprite']
			},
			image_resize: {
				options: {
					livereload: true
				},
				files: ['assets/img/sprite.png'],
				tasks: ['image_resize']
			},
			browserify: {
				options: {
					livereload:true,
					spawn: false
				},
				files: ['src/js/**/*.js'],
				tasks: ['browserify']
			}
		}
	});

	grunt.registerTask('build', [
		'sprite',
		'image_resize',
		'sass',
		'autoprefixer',
		'browserify'
	]);

	grunt.registerTask('dev', [
	 	'build',
		'watch',
	]);

	grunt.registerTask('default', ['dev']);
};