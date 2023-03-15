var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.styles(
        ['reset.css','popup.css','base.css','app.css','sample.css'],
        'public/css/rel/main-app.css',
        'public/css/v11'
    );
    mix.styles(
        ['slick.css','slick-theme.css','boomTvCustom.css'],
        'public/css/slick/main.css',
        'public/slick'
    );
	mix.styles(
		['ex1.css','ex-theme.css','ex-custom.css'],
		'public/css/ex/main.css',
		'public/ex'
	);
    mix.scripts(
        ['js/player/common.js','js/common.js'],
        'public/js/rel/main-header.js',
        'public'
    );
    mix.scripts(
        ['js/v1/navigation.js','js/v1/dropdown.js','js/v1/stats-options.js','js/v1/popout.js','js/timezone.js','js/popup.js','js/auth/navigation.js', 'js/v1/boom-meter.js'],
        'public/js/rel/main-footer.js',
        'public'
    );
    mix.version([
        'public/css/rel/main-app.css',
        'public/css/slick/main.css',
        'public/js/rel/main-header.js',
        'public/js/rel/main-footer.js'
    ]);
    mix.copy('public/fonts','public/build/fonts');
    mix.copy('public/assets','public/build/assets');
    mix.copy('public/slick/fonts','public/build/css/slick/fonts');
});
