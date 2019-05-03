var gulp = require("gulp");
var webpack = require("webpack");
var babel = require("gulp-babel");
var webpackStream = require("webpack-stream");
var named = require("vinyl-named");
var rename = require("gulp-rename");
var UglifyJsPlugin = require("uglifyjs-webpack-plugin");
var runSequence = require("run-sequence");
var BundleAnalyzerPlugin = require("webpack-bundle-analyzer").BundleAnalyzerPlugin;

// Development version, including Vue devtools
gulp.task("dev", function() {
  return gulp
    .src(["assets/js/src/settings.js"])

    // Retain filename
    .pipe(named())

    // Webpack
    .pipe(webpackStream({
        resolve: {
            alias: {
                'vue$': 'vue/dist/vue.esm.js'
            }
        },
        module: {
            rules: [
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {}
                }
            ]
        }
    }))

    // Babel
    .pipe(babel({
        presets: ['env']
    }))

    // Output
    .pipe(gulp.dest("assets/js/dist/"));
});

// Minified version
gulp.task("build", function() {
  return gulp
    .src(["assets/js/src/settings.js"])

    // Retain filename
    .pipe(named())

    // Webpack
    .pipe(webpackStream({
      resolve: {
        alias: {
          vue$: "vue/dist/vue.min.js"
        }
      },
      module: {
        rules: [
            {
                test: /\.vue$/,
                loader: "vue-loader"
            }
        ]
      },
      plugins: [
        new webpack.DefinePlugin({
            "process.env": {
                NODE_ENV: '"production"'
            }
        }),
        new UglifyJsPlugin({
            sourceMap: false
        })
      ]
    }))

    // Babel
    .pipe(babel({
        presets: ['env']
    }))

    // Rename with .min
    .pipe(rename({ extname: ".min.js" }))

    // Output
    .pipe(gulp.dest("assets/js/dist/"));
});

// Development version, including Vue devtools
gulp.task("devAdvanced", function() {
  return gulp
    .src(["assets/js/src/settings-advanced.js"])

    // Retain filename
    .pipe(named())

    // Webpack
    .pipe(webpackStream({
        resolve: {
            alias: {
                'vue$': 'vue/dist/vue.esm.js'
            }
        },
        module: {
            rules: [
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {}
                }
            ]
        }
    }))

    // Babel
    .pipe(babel({
        presets: ['env']
    }))

    // Output
    .pipe(gulp.dest("assets/js/dist/"));
});

// Minified version
gulp.task("buildAdvanced", function() {
  return gulp
    .src(["assets/js/src/settings-advanced.js"])

    // Retain filename
    .pipe(named())

    // Webpack
    .pipe(webpackStream({
      resolve: {
        alias: {
          vue$: "vue/dist/vue.min.js"
        }
      },
      module: {
        rules: [
            {
                test: /\.vue$/,
                loader: "vue-loader"
            }
        ]
      },
      plugins: [
        new webpack.DefinePlugin({
            "process.env": {
                NODE_ENV: '"production"'
            }
        }),
        new UglifyJsPlugin({
            sourceMap: false
        })
      ]
    }))

    // Babel
    .pipe(babel({
        presets: ['env']
    }))

    // Rename with .min
    .pipe(rename({ extname: ".min.js" }))

    // Output
    .pipe(gulp.dest("assets/js/dist/"));
});

// Development version, including Vue devtools
gulp.task("devStats", function() {
  return gulp
    .src(["assets/js/src/statistics.js"])

    // Retain filename
    .pipe(named())

    // Webpack
    .pipe(webpackStream({
        resolve: {
            alias: {
                'vue$': 'vue/dist/vue.esm.js'
            }
        },
        module: {
            rules: [
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {}
                }
            ]
        }
    }))

    // Babel
    .pipe(babel({
        presets: ['env']
    }))

    // Output
    .pipe(gulp.dest("assets/js/dist/"));
});

// Minified version
gulp.task("buildStats", function() {
  return gulp
    .src(["assets/js/src/statistics.js"])

    // Retain filename
    .pipe(named())

    // Webpack
    .pipe(webpackStream({
      resolve: {
        alias: {
          vue$: "vue/dist/vue.min.js"
        }
      },
      module: {
        rules: [
            {
                test: /\.vue$/,
                loader: "vue-loader"
            }
        ]
      },
      plugins: [
        new webpack.DefinePlugin({
            "process.env": {
                NODE_ENV: '"production"'
            }
        }),
        new UglifyJsPlugin({
            sourceMap: false
        })
      ]
    }))

    // Babel
    .pipe(babel({
        presets: ['env']
    }))

    // Rename with .min
    .pipe(rename({ extname: ".min.js" }))

    // Output
    .pipe(gulp.dest("assets/js/dist/"));
});

// Default task is a watcher that builds both development and production versions
gulp.task("default", function(){
    runSequence("dev", "build");
    gulp.watch([
        "assets/js/src/**/*.js",
        "assets/js/src/**/*.vue"
    ], function(){
        runSequence("dev", "build");
    });
});

// Advanced settings screen
gulp.task("advanced-settings", function(){
    runSequence("devAdvanced", "buildAdvanced");
    gulp.watch([
        "assets/js/src/**/*.js",
        "assets/js/src/**/*.vue"
    ], function(){
        runSequence("devAdvanced", "buildAdvanced");
    });
});

// Advanced settings screen
gulp.task("stats", function(){
    runSequence("devStats", "buildStats");
    gulp.watch([
        "assets/js/src/**/*.js",
        "assets/js/src/**/*.vue"
    ], function(){
        runSequence("devStats", "buildStats");
    });
});
