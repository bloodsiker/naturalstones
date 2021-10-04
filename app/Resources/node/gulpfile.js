'use strict';

const gulp = require('gulp');
// const stylus = require('gulp-stylus');
const concat = require('gulp-concat');
const sourcemaps = require('gulp-sourcemaps');
const minify = require('gulp-minify');
const cleanCSS = require('gulp-clean-css');
const del = require('del');
const newer = require('gulp-newer');
const autoprefixer = require('gulp-autoprefixer');
const remember = require('gulp-remember');
const path = require('path');
const cached = require('gulp-cached');
const browserSync = require('browser-sync').create();
const debug = require('gulp-debug');

const directory = '../../../src/AppBundle/Resources/public/';
const frontend = '../../../web/public/';

gulp.task('styles', function () {
    return gulp.src(directory+'css/**/*.css')
    // return gulp.src('frontend/css/**/*.css')
        .pipe(cached('styles'))
        .pipe(sourcemaps.init())
        .pipe(autoprefixer())
        .pipe(remember('styles'))
        .pipe(sourcemaps.write())
        .pipe(cleanCSS())
        .pipe(concat('all.css'))
        .pipe(gulp.dest(frontend+'css'));
});

gulp.task('assets', function () {
    return gulp.src(directory+'assets/**', {since: gulp.lastRun('assets')})
        .pipe(newer(frontend+'assets'))
        // .pipe(debug())
        .pipe(gulp.dest(frontend+'assets'))
});

gulp.task('clean', function () {
    return del('../../../web/public', {force: true});
});

gulp.task('build', gulp.series('clean', gulp.parallel('styles', 'assets')));

gulp.task('watch', function () {
    gulp.watch(directory+'css/**/*.*', gulp.series('styles')).on('unlink', function (filepath) {
        remember.forget('styles', path.resolve(filepath));
        delete cached.caches.styles[path.resolve(filepath)];
    });

    gulp.watch(directory+'assets/**/*.*', gulp.series('assets'));
});

gulp.task('server', function () {
    browserSync.init({
        server: '../../../web/public'
    });

    browserSync.watch('../../../web/public/**/*.*').on('change', browserSync.reload);
});

gulp.task('dev', gulp.series('build', gulp.parallel('watch', 'server')));