var gulp = require('gulp'),
  concat = require('gulp-concat'),
  minifyCss = require('gulp-minify-css'),
  uglify = require('gulp-uglify');

  //Desktop
  gulp.task('min-js-plugins', function() {
    return gulp.src(['src/public/js/jquery.min.js', 'src/public/js/bootstrap.min.js', 'src/public/js/jquery.validate.min.js', 'src/public/js/slick.js'])
      .pipe(concat('plugins.min.js'))
      .pipe(uglify())
      .pipe(gulp.dest('src/public/js/'));
  });

  gulp.task('min-js-main', function() {
    return gulp.src(['src/public/js/main.js'])
      .pipe(concat('main.min.js'))
      .pipe(uglify())
      .pipe(gulp.dest('src/public/js/'));
  });

  gulp.task('min-css-plugins', function() {
    return gulp.src(['src/public/css/scroll-sidebar.css', 'src/public/css/bootstrap-theme.min.css', 'src/public/css/bootstrap.min.css'])
      .pipe(concat('plugins.min.css'))
      .pipe(minifyCss())
      .pipe(gulp.dest('src/public/css/'));
  });

  gulp.task('min-css-main', function() {
    return gulp.src(['src/public/css/style.css', 'src/public/css/main.css'])
      .pipe(concat('main.min.css'))
      .pipe(minifyCss())
      .pipe(gulp.dest('src/public/css/'));
  });
  //Movil
  gulp.task('min-m-js-plugins', function() {
    return gulp.src(['src/public/js/jquery.min.js', 'src/public/js/bootstrap.min.js', 'src/public/js/jquery.validate.min.js', 'src/public/js/slick.js', 'src/public/js/slideout.min.js'])
      .pipe(concat('plugins.min.js'))
      .pipe(uglify())
      .pipe(gulp.dest('src/public/movil/js/'));
  });
  gulp.task('min-m-js-main', function() {
    return gulp.src(['src/public/movil/js/main.js'])
      .pipe(concat('main.min.js'))
      .pipe(uglify())
      .pipe(gulp.dest('src/public/movil/js/'));
  });

  gulp.task('min-m-css-main', function() {
    return gulp.src(['src/public/css/style.css', 'src/public/movil/css/main.css'])
      .pipe(concat('main.min.css'))
      .pipe(minifyCss())
      .pipe(gulp.dest('src/public/movil/css/'));
  });