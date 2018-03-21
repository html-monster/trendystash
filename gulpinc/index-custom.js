'use strict';

var OPTIONS = require('./pathes');

var $ = require('gulp-load-plugins')();
var gulp = require('gulp');


module.exports = {
    def: function (options) {
        return function ()
        {
            return gulp.src('src/scss/*.scss')
                .pipe($.plumber({
                    errorHandler: $.notify.onError(function (err) {
                        return {
                            title: 'Styles',
                            message: err.message
                        }
                    })
                }))
                .pipe($.if(options.isDevelopment, $.sourcemaps.init()))
                .pipe($.sass({outputStyle: "compact"}))
                .pipe($.autoprefixer({
                    browsers: ['last 4 versions']
                }))
                .pipe($.if(options.isDevelopment, $.sourcemaps.write()))
                .pipe($.notify(function (file) {
                    var options = {hour: 'numeric', minute: 'numeric', second: 'numeric'};
                    return "Compiled " + file.relative + ' ' + (new Date()).toLocaleString("ru", options);
                }))
                .pipe(gulp.dest(OPTIONS.path.destDirCSS))
                ;
/*            return gulp.src('./jslib/!**!/{' + tmpl + '}')
                .pipe(concat(options.allfn))
                .pipe($.notify(function (file) {
                    var options = {hour: 'numeric', minute: 'numeric', second: 'numeric'};
                    return "Compiled " + file.relative + ' ' + (new Date()).toLocaleString("ru", options);
                }))
                .pipe(gulp.dest(options.dst));*/
        }
    }
};