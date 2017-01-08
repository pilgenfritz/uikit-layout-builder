var gulp = require('gulp');
var minify = require('gulp-minify');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var pump = require('pump');
var cleanCSS = require('gulp-clean-css');
var autoprefixer = require('gulp-autoprefixer');


gulp.task('default', function() {
  	console.log('oi')	
});
 
gulp.task('concat-uk-js', function() {
  return gulp.src('./js/uikit/*.js')
    .pipe(concat('uikit-concat.js'))
    .pipe(gulp.dest('./dist/js'));
});

gulp.task('concat-plugins-js', function() {
  return gulp.src('./dist/js/plugins/*.js')
    .pipe(concat('plugins-concat.js'))
    .pipe(gulp.dest('./dist/js'));
});
 
gulp.task('compress-js', function (cb) {
  pump([
        gulp.src('js/plugins/*.js'),
        uglify(),
        gulp.dest('dist/js/plugins/')
    ],
    cb
  );
});



gulp.task('minify-css', function() {
  return gulp.src('css/plugins/*.css')
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(gulp.dest('dist/css/plugins/'));
});

gulp.task('concat-uk-css', function() {
  return gulp.src('./css/uikit/*.css')
    .pipe(concat('uikit-concat.css'))
    .pipe(gulp.dest('./dist/css'));
});

gulp.task('concat-plugins-css', function() {
  return gulp.src('./dist/css/plugins/*.css')
    .pipe(concat('plugins-concat.css'))
    .pipe(gulp.dest('./dist/css/'));
});

gulp.task('autoprefix-css', function() {
  gulp.src('./css/*.css')
      .pipe(autoprefixer({
          browsers: ['last 2 version',
        'ff > 20',
        '> 1%',
        'ie 9',
        'ie 10']
      }))
      .pipe(gulp.dest('./css'))

  gulp.src('./css/pages/**/*.css')
      .pipe(autoprefixer({
          browsers: ['last 2 version',
        'ff > 20',
        '> 1%',
        'ie 9',
        'ie 10']
      }))
      .pipe(gulp.dest('./css/pages'))
});