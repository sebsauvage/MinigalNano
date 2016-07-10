var gulp = require('gulp'),
    sourcemaps = require('gulp-sourcemaps'),
    less = require('gulp-less'),
    plumber = require('gulp-plumber'),
    uglify = require('gulp-uglify'),
    cssmin = require('gulp-cssmin'),
    autoprefixer = require('gulp-autoprefixer'),
    rename = require('gulp-rename');

var input = {
        less: 'css/styles.less',
        js: 'js/script.js'
    },
    output = {
        css: 'css/',
        js : 'js/'
    };

gulp.task('css', function() {
    gulp.src(input.less)
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(less())
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(sourcemaps.write())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(output.css));
});

gulp.task('js', function(){
   gulp.src(input.js)
       .pipe(plumber())
       .pipe(sourcemaps.init())
       .pipe(uglify())
       .pipe(sourcemaps.write())
       .pipe(rename({
           suffix: '.min'
       }))
       .pipe(gulp.dest(output.js));
});

gulp.task('default', ['css', 'js'], function() {
    gulp.watch(input.js, ['js']);
    gulp.watch(input.less, ['css']);
});