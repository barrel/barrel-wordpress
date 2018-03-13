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

    // Babel
    .pipe(babel({
        presets: ['env']
    }))

    // Webpack
    .pipe(webpackStream({
        resolve: {
            alias: {
                'vue$': 'vue/dist/vue.esm.js'
            }
        },
        devtool: '#cheap-module-eval-source-map',
        module: {
            rules: [
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {}
                }
            ],
            loaders: [
                {
                    test: /\.js$/,
                    loader: 'babel-loader',
                    exclude: /node_modules/,
                    query: {
                        cacheDirectory: true,
                        presets: ['env']
                    }
                }
            ]
        }
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

    // Babel
    .pipe(babel({
        presets: ['env']
    }))

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
        ],
        loaders: [
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
                query: {
                    cacheDirectory: true,
                    presets: ['env']
                }
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
