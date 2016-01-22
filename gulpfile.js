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

// Copy
gulp.task('copy', function(){
  // Copy Ace editor
  return gulp.src('./node_modules/ace-builds/src-min/**/*')
    .pipe(gulp.dest('./assets/lib/ace'));
});

// Build
gulp.task('build', ['sass', 'copy']);

// watch
gulp.task('watch',function(){
  // Make SASS
  gulp.watch('./assets/scss/**/*.scss',['sass']);
  // Check JS syntax
  gulp.watch('./assets/js/**/*.js',['jshint']);
});
