var path = require('path');

module.exports = function(grunt) {

	// Load grunt tasks

	require('load-grunt-tasks')(grunt, {pattern: ['grunt-*']});

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

		copy: {
			all: {
				files: [
					{
						expand: true,
						flatten: true,
						cwd: 'node_modules/',
						src: [
							'isotope-layout/dist/isotope.pkgd.min.js',
							'slick-carousel/slick/slick.min.js',
							'waypoints/lib/jquery.waypoints.min.js'
						],
						dest: 'src/js/vendors/'
					}
				]
			}
		},

		webfont: {
			icons: {
				src: "src/icons/*.svg",
				dest: "assets/fonts/site-icons",
				destCss: "src/sass/_inc/fonts",
				options: {
					stylesheet: "scss",
					relativeFontPath: "../fonts/site-icons",
					types: "eot,woff,ttf,svg",
					templateOptions: {
						baseClass: "icon",
						classPrefix: "icon-"
					}
				}
			}
		},

		uglify: {
			compress: {
				files: {
					'assets/js/vendor.min.js': ['src/js/vendors/**/*.js']
				},
				options: {
					mangle: true
				}
			}
		},

        jshint: {
			// define the files to lint
			files: ['Gruntfile.js', 'src/js/**/*.js', '!src/js/**/*.min.js'],
			// configure JSHint (documented at http://www.jshint.com/docs/)
			options: {
				// more options here if you want to override JSHint defaults
				globals: {
					jQuery: true,
					console: true,
					module: true
				}
			}
		},

		jscs: {
			src: [ "Gruntfile.js", "src/js/**/*.js", "!src/js/**/*.min.js" ],
			options: {
				config: ".jscsrc",
				esnext: false, // If you use ES6 http://jscs.info/overview.html#esnext
				verbose: true, // If you need output with rule names http://jscs.info/overview.html#verbose
				fix: false // Autofix code style violations when possible.
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
				browsers: ['> 1%', 'last 4 versions', 'ie 9']
			},
			dist: {
				src: 'assets/css/styles.min.css',
				dest: 'assets/css/styles.min.css'
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
			browserify: {
				options: {
					livereload:true,
					spawn: false
				},
				files: ['<%= jshint.files %>'],
				tasks: ['jshint', 'browserify']
			}
		}
	});

	grunt.registerTask('build', [
		'webfont',
		'sass',
		'autoprefixer',
		'copy',
		'browserify',
		'uglify'
	]);

	grunt.registerTask('dev', [
		'build',
		'watch',
	]);

	grunt.registerTask('default', ['dev']);
};