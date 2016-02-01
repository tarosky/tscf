var gulp        = require('gulp'),
    $           = require('gulp-load-plugins')(),
    browserSync = require('browser-sync');

// Sass
gulp.task('sass',function(){

  var filter = $.filter('**/*.css');

  return gulp.src(['./assets/scss/**/*.scss'])
    .pipe($.plumber())
    .pipe($.sourcemaps.init())
    .pipe($.sass({
      errLogToConsole: true,
      outputStyle: 'compressed',
      sourceComments: 'normal',
      sourcemap: true
    }))
    .pipe($.sourcemaps.write('./map'))
    .pipe(gulp.dest('./assets/css'));
});



// JS Hint
gulp.task('jshint', function(){
  return gulp.src(['./assets/js/**/*.js'])
    .pipe($.jshint('./assets/.jshintrc'))
    .pipe($.jshint.reporter('jshint-stylish'));
});
// Copy Ace editor
gulp.task('ace', function(){
  // Copy Ace editor
  return gulp.src('./node_modules/ace-builds/src-min/**/*')
    .pipe(gulp.dest('./assets/lib/ace'));
});
// Copy jQuery UI MP6
gulp.task('mp6', function(){
  return gulp.src([
      './node_modules/jquery-ui-mp6/src/**/*',
      '!./node_modules/jquery-ui-mp6/src/scss/**/*',
      '!./node_modules/jquery-ui-mp6/src/js/**/*',
      '!./node_modules/jquery-ui-mp6/src/config.rb'
  ])
    .pipe(gulp.dest('./assets/lib/jquery-ui-mp6'))
});
// timepicker
gulp.task('timepickerAddon', function(){
  return gulp.src([
    './node_modules/jquery-ui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.css',
    './node_modules/jquery-ui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.js',
    './node_modules/jquery-ui-timepicker-addon/dist/i18n/**/*'
  ])
    .pipe(gulp.dest('./assets/lib/jquery-ui-timepicker-addon'));
});
// Copy
gulp.task('copy', ['ace', 'mp6', 'timepickerAddon']);

// Build
gulp.task('build', ['sass', 'copy']);

// watch
gulp.task('watch',function(){
  // Make SASS
  gulp.watch('./assets/scss/**/*.scss',['sass']);
  // Check JS syntax
  gulp.watch('./assets/js/**/*.js',['jshint']);
});
