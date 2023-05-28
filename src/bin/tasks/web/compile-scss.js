const Gulp = require('gulp');
const PostCSS = require('gulp-postcss');
const SourceMaps = require('gulp-sourcemaps');

const CSSNext = require('postcss-cssnext');
const CSSO = require('gulp-csso');
const SASS = require('gulp-sass')(require('node-sass'));

const stylesheetsSources = [
    './frontend/assets/sources/**/*.scss',
    './common/modules/Client/**/View/Assets/**/sources/**/*.scss',
    './common/modules/Client/**/Common/Assets/sources/**/*.scss',
    './common/modules/Attendance/View/Assets/**/sources/**/*.scss',
    './common/modules/Management/**/View/Assets/**/sources/**/*.scss',
    './common/modules/Management/Common/Assets/**/sources/**/*.scss',
    './common/modules/Merge/Assets/sources/**/*.scss',
    './common/modules/Referral/**/View/Assets/**/sources/**/*.scss',
    './common/modules/Report/View/Assets/sources/**/*.scss',
    './common/modules/Setup/Assets/sources/**/*.scss',
    './common/modules/Setup/**/View/Assets/**/sources/**/*.scss',
    './common/modules/**/View/Assets/**/sources/**/*.scss',
    './common/modules/Merge/Assets/**/*.scss',
    './frontend/modules/manage/assets/sources/**/scss/*.scss',
    './frontend/modules/Caseload/View/Assets/sources/**/scss/*.scss',
    './frontend/views/widgets/ckeditor/sources/**/*.scss',
    './frontend/views/widgets/location/sources/**/*.scss',
    './frontend/views/widgets/AutoComplete/sources/**/*.scss',
    './frontend/views/widgets/direction/sources/**/*.scss',
    './vendor/bitfocus-dev/data-import-tool/src/Asset/Source/**/*.scss'
];
const sassOptions = {
    errLogToConsole: true,
    outputStyle: 'expanded'
};

let CompileSCSS = () => {
    let processors = [
        CSSNext({
            // AutoPrefixer configuration
            warnForDuplicates: false,
            remove: false, // force CSSNext to not remove hardcoded prefixes
            browsers: [
                'last 10 version',
                'IE 11',
            ],
        }),
    ];

    return Gulp.src(stylesheetsSources)
               .pipe(SourceMaps.init())
               .pipe(SASS(sassOptions).on('error', SASS.logError))
               .pipe(PostCSS(processors))
               .pipe(CSSO({
                   restructure: false,
                   sourceMap: true,
               }))
               .pipe(SourceMaps.write('.'))
               .pipe(Gulp.dest(function (file) {
                   return file.base;
               }))
};

module.exports = CompileSCSS;
