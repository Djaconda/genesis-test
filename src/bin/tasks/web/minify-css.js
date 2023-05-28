const Gulp = require('gulp');
const CSSO = require('gulp-csso');

const stylesheetsSources = './frontend/web/assets/**/*.css';

let MinifyCSS = () => {
    return Gulp.src(stylesheetsSources)
               .pipe(CSSO({
                   restructure: false,
                   sourceMap: true,
               }))
               .pipe(Gulp.dest(function (file) {
                   return file.base;
               }))
};

module.exports = MinifyCSS;
