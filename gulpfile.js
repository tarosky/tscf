const gulp = require('gulp');
const $ = require('gulp-load-plugins')();
const mergeStream = require( 'merge-stream' );

let plumber = true;

// Sass
gulp.task('sass', function () {

  var filter = $.filter('**/*.css');

  return gulp.src(['./assets/scss/**/*.scss'])
    .pipe($.plumber({
      errorHandler: $.notify.onError('<%= error.message %>')
    }))
    .pipe($.sourcemaps.init())
    .pipe($.sass({
      errLogToConsole: true,
      outputStyle    : 'compressed',
      sourceComments : 'normal',
      sourcemap      : true
    }))
    .pipe($.sourcemaps.write('./map'))
    .pipe(gulp.dest('./assets/css'));
});


// JS Hint
gulp.task('lint:js', function () {
  return gulp.src(['./assets/js/src/**/*.js'])
    .pipe($.plumber({
      errorHandler: $.notify.onError('<%= error.message %>')
    }))
    .pipe($.eslint());
});

gulp.task('jsBundle', function(){
  return gulp.src('./assets/js/src/*.js')
    .pipe($.plumber({
      errorHandler: $.notify.onError('<%= error.message %>')
    }))
    .pipe($.sourcemaps.init())
    .pipe($.include({
      extensions: "js"
    }))
    .pipe($.uglify())
    .pipe($.sourcemaps.write('./map'))
    .pipe(gulp.dest('./assets/js/dist'));

});

// Copy
gulp.task('copy', function () {
  return mergeStream(
    // Angular
    gulp.src([
      './node_modules/angular/angular.min.js',
      './node_modules/angular/angular.min.js.map',
      './node_modules/angular-ui-sortable/dist/sortable.min.js'
    ]).pipe(gulp.dest('./assets/lib/angular')),

    // MP6
    gulp.src([
      './node_modules/jquery-ui-mp6/src/**/*',
      '!./node_modules/jquery-ui-mp6/src/scss/**/*',
      '!./node_modules/jquery-ui-mp6/src/js/**/*',
      '!./node_modules/jquery-ui-mp6/src/config.rb'
    ])
      .pipe(gulp.dest('./assets/lib/jquery-ui-mp6')),

    // LivePreview
    gulp.src([
      './node_modules/jquery-live-preview/js/jquery-live-preview.min.js'
    ])
      .pipe(gulp.dest('./assets/lib/jquery-live-preview')),
    gulp.src([
      './node_modules/jquery-live-preview/images/icon_loading.gif'
    ])
      .pipe(gulp.dest('./assets/lib/jquery-live-preview/images')),
    // Time picker
    gulp.src([
      './node_modules/jquery-ui-timepicker-addon/dist/**/*',
      '!./node_modules/jquery-ui-timepicker-addon/dist/index.html',
      '!./node_modules/jquery-ui-timepicker-addon/dist/jquery-ui-sliderAccess.js',
      '!./node_modules/jquery-ui-timepicker-addon/dist/jquery-ui-timepicker-addon.css',
      '!./node_modules/jquery-ui-timepicker-addon/dist/jquery-ui-timepicker-addon.js'
    ])
      .pipe(gulp.dest('./assets/lib/jquery-ui-timepicker-addon')),
    // Select2
    gulp.src([
      './node_modules/select2/dist/js/**/*.js',
      '!./node_modules/select2/dist/js/select2.js',
      '!./node_modules/select2/dist/js/select2.full.js',
      '!./node_modules/select2/dist/js/select2.full.min.js'
    ])
      .pipe(gulp.dest('./assets/lib/select2/js')),
    gulp.src([
      './node_modules/select2/dist/css/select2.min.css'
    ]).pipe(gulp.dest('./assets/lib/select2/css')),
    // Ace editor
    gulp.src([
      './node_modules/ace-builds/src-min-noconflict/**/*'
    ]).pipe(gulp.dest('./assets/lib/ace'))
  );
});

// Build
gulp.task('build', gulp.parallel( 'sass', 'copy', 'jsBundle' ) );

// watch
gulp.task('watch', function () {
  // Make SASS
  gulp.watch('assets/scss/**/*.scss', gulp.task( 'sass' ) );
  // Check JS syntax and bundle them
  gulp.watch('assets/js/src/**/*.js', gulp.parallel( 'jshint', 'jsBundle' ) );
});

// Toggle plumber.
gulp.task( 'noplumber', ( done ) => {
	plumber = false;
	done();
} );

