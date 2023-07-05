'use strict';

import plugins from 'gulp-load-plugins';
import browser from 'browser-sync';
import yargs from 'yargs';
import gulp from 'gulp';
import rimraf from 'rimraf';
import yaml from 'js-yaml';
import fs from 'fs';
import webpackStream from 'webpack-stream';
import webpack2 from 'webpack';
import named from 'vinyl-named';

const packageFile = require('./package.json');

// Load all Gulp plugins into one variable
const $ = plugins();

// Check for --production flag
const PRODUCTION = !!(yargs.argv.production);

// Check for --noserver flag
const SERVER = !(yargs.argv.noserver);

// Load settings from settings.yml
const {COMPATIBILITY, PATHS} = loadConfig();

function loadConfig() {
    let ymlFile = fs.readFileSync('config.yml', 'utf8');
    return yaml.load(ymlFile);
}

// Enter URL of your local server here
// Example: 'http://localhost:8888'
var URL = 'http://src.wordpress-develop.dev';

// Build the "dist" folder by running all of the below tasks
gulp.task('build',
    PRODUCTION ?
        gulp.series(version, SassAdmin, JavaScriptAdmin, copy, packageFiles) :
        gulp.series(SassAdmin, JavaScriptAdmin, copy));

// Build the site, run the server, and watch for file changes
gulp.task('default',
    SERVER ?
        gulp.series('build', server, watch) :
        gulp.series('build', watch));

// Delete the "dist" folder
// This happens every time a build starts
function clean(done) {
    rimraf(PATHS.dist, done);
}

// Copy files out of the assets folder
// This task skips over the "img", "js", and "scss" folders, which are parsed separately
function copy() {
    return gulp.src(PATHS.assets)
        .pipe(gulp.dest(PATHS.dist + '/assets'));
}

// Compile Front Sass into CSS
// In production, the CSS is compressed
function SassAdmin() {
    return gulp.src('assets/src/scss/admin/app.scss')
        .pipe($.sourcemaps.init())
        .pipe($.sass({
            includePaths: PATHS.sass
        })
            .on('error', $.sass.logError))
        .pipe($.autoprefixer({
            browsers: COMPATIBILITY
        }))

        .pipe($.if(PRODUCTION, $.cleanCss({compatibility: 'ie9'})))
        .pipe($.if(!PRODUCTION, $.sourcemaps.write()))
        .pipe($.rename('rbm-field-helpers-admin.min.css'))
        .pipe(gulp.dest(PATHS.dist + '/css'))
        .pipe(browser.reload({stream: true}));
}

let webpackConfig = {
    module: {
        rules: [
            {
                test: /.js$/,
                use: [
                    {
                        loader: 'babel-loader'
                    }
                ]
            }
        ]
    }
}

// Combine JavaScript into one file
// In production, the file is minified
function JavaScriptAdmin() {
    return gulp.src(PATHS.entries.admin)
        .pipe(named())
        .pipe($.sourcemaps.init())
        .pipe(webpackStream(webpackConfig, webpack2))
        .pipe($.if(PRODUCTION, $.uglify()
            .on('error', e => {
                console.log(e);
            })
        ))
        .pipe($.if(!PRODUCTION, $.sourcemaps.write()))
        .pipe($.rename('rbm-field-helpers-admin.min.js'))
        .pipe(gulp.dest(PATHS.dist + '/js'));
}

// Start BrowserSync to preview the site in
function server(done) {
    browser.init({
        proxy: URL,

        ui: {
            port: 8080
        },

    });
    done();
}

// Reload the browser with BrowserSync
function reload(done) {
    browser.reload();
    done();
}

function version() {
    return gulp.src([
        'assets/src/**/*',
        'vendor/**/*',
        'core/**/*',
        'rbm-field-helpers.php'
    ], {base: './'})
    //     Doc block versions
        .pipe($.replace(/\{\{VERSION}}/g, packageFile.version))
        // Version constant
        .pipe($.replace(/(define\( 'RBM_FIELD_HELPERS_VER', ')\d\.\d\.\d/, "$1" + packageFile.version))
        .pipe(gulp.dest('./'));
}

function packageFiles() {
    return gulp.src([
        'assets/dist/**/*',
        'vendor/**/*',
        'core/**/*',
        'rbm-field-helpers.php'
    ], {base: './'})
        .pipe($.zip(`rbm-field-helpers-${packageFile.version}.zip`))
        .pipe(gulp.dest('./'));
}

// Watch for changes to Sass, and JavaScript
function watch() {
    gulp.watch(PATHS.assets, copy);
    gulp.watch('assets/src/scss/admin/**/*.scss').on('all', SassAdmin);
    gulp.watch('assets/src/js/admin/**/*.js').on('all', gulp.series(JavaScriptAdmin, browser.reload));
    gulp.watch('**/*.php').on('all', browser.reload);
}
