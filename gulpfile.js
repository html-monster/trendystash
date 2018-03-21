'use strict';

const OPTIONS = require('./gulpinc/pathes');
const $pathDestServer = OPTIONS.path.dest_server;

// const path = require('path');
const gulp = require('gulp');
// const ts = require('gulp-typescript');
// const babel = require('gulp-babel');
// const combine = require('stream-combiner2').obj;
const sourcemaps = require('gulp-sourcemaps');
const sass = require("gulp-sass");
const autoprefixer = require("gulp-autoprefixer");
// const browserSync = require('browser-sync').create();
const gulpIf = require('gulp-if');
// const cssnano = require('gulp-cssnano');
// const rev = require('gulp-rev');
// const revReplace = require('gulp-rev-replace');
const plumber = require('gulp-plumber');
const notify = require('gulp-notify');
// const uglify = require('gulp-uglify');
const $ = require('gulp-load-plugins')();

// Gulp + Webpack = ♡

// const named = require('vinyl-named');

const isDevelopment = !process.env.NODE_ENV || process.env.NODE_ENV == 'development';


function lazyRequire(taskName, inTaskName, path, options)
{
    options = options || {};
    options.isDevelopment = isDevelopment;
    options.taskName = taskName;
    gulp.task(taskName, function (callback) {
        var task = require(path)[inTaskName].call(this, options);

        return task(callback);
    });
}

// BMS: --- MORDA TASKS ------------------------------------------------------------------------------------------------

// BM: ========================================================================================== MORDA CSS REVISION ===
lazyRequire('index-custom', 'def', './gulpinc/index-custom', {
    // src: $pathDestServer + '/Content/dist',
    // dst: $pathDestServer + '/Content/css-assets',
});


/*
gulp.task('index-custom', function() {

    return gulp.src('src/scss/!**!/!*.scss')
        .pipe(plumber({
            errorHandler: notify.onError(function (err) {
                return {
                    title: 'Styles',
                    message: err.message
                }
            })
        }))
        .pipe(gulpIf(isDevelopment, sourcemaps.init()))
        .pipe(sass({outputStyle: "compact"}))
        .pipe(autoprefixer({
            browsers: ['last 4 versions']
        }))
        .pipe(gulpIf(isDevelopment, sourcemaps.write()))
        .pipe($.notify(function (file) {
            var options = {hour: 'numeric', minute: 'numeric', second: 'numeric'};
            return "Compiled " + file.relative + ' ' + (new Date()).toLocaleString("ru", options);
        }))
        .pipe(gulp.dest(OPTIONS.path.destDirCSS))
        ;
});
*/

// skin/frontend/rentabag/default/scss/rentabag-style.scss


// TODEL is used anymore?
/*gulp.task('assets', function() {
  return gulp.src('frontend/assets/!**!/!*.html', {since: gulp.lastRun('assets')})
      //.pipe(jade())
      // .pipe(gulpIf(!isDevelopment, revReplace({
      //   manifest: gulp.src('manifest/css.json', {allowEmpty: true})
      // })))
      .pipe(gulp.dest('public'));
});*/


// gulp.task('ts:process', function () {
//   return gulp.src('test/theme/.ts/**/*.ts')
//              .pipe(plumber())
//              .pipe(sourcemaps.init())
//              .pipe(ts({
//                noImplicitAny: false,
//                removeComments: true,
//                // suppressImplicitAnyIndexErrors: true,
//                module: 'umd',
//                target: 'ES5',
//                out: 'index.js'
//              }))
//              // .pipe(sourcemaps.write('.'))
//              .pipe(notify("Compiled: <%= file.relative %>!"))
//              .pipe(gulp.dest('test/theme/js'));
// });



gulp.task('js',function(){
    return gulp.src(['frontend/js/nonReact/**/*.js',
        '!frontend/js/nonReact/browserCheck.js',
        '!frontend/js/nonReact/test.js',
        '!frontend/js/nonReact/access.js',
        '!frontend/js/nonReact/pageFirst.js',
        '!frontend/js/react/localization/**',
        ])
    .pipe(sourcemaps.init())
    .pipe(babel({
      presets: ['es2015', 'stage-0'],
      plugins: [['transform-class-properties', { "spec": true }], ["remove-comments"]],
    }))
    .pipe($.concat('all.js'))
    // $.uglify(),
    .pipe(sourcemaps.write())
    .pipe($.notify(function (file) {
        var options = {hour: 'numeric', minute: 'numeric', second: 'numeric'};
        return "Compiled " + file.relative + ' ' + (new Date()).toLocaleString("ru", options);
    }))
    // .pipe(gulp.dest('./public/js'))
    .pipe(gulp.dest($pathDestServer + '/Scripts/dist'));
});



// BM: ============================================================================================== ONE TIME BUILD ===
gulp.task('build', gulp.series(gulp.parallel('index-custom'/*, 'js', 'vendor'*/)));



// BMS: --- WATCHES ----------------------------------------------------------------------------------------------------
// BM: ========================================================================================== FRONT DEV BUILDING ===
gulp.task('watch-front-js-styles', function () {
    gulp.watch('src/scss/**/*.scss', gulp.series('index-custom'));
    // gulp.watch('frontend/js/nonReact/**/*.js', gulp.series('js'));
});


