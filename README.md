# server

Css & javascript edit
   Update new css & javascript
    
    - npm install 
    - npm install --global gulp-cli
    - gulp --production
    
    
   Debug css javascript
    
    - gulp
    
Current gulpfile

    elixir(function(mix) {
        mix.styles(
            ['reset.css','popup.css','base.css','app.css'],
            'public/css/rel/main-app.css',
            'public/css/v11'
        );
    
        mix.styles(
            ['slick.css','slick-theme.css','boomTvCustom.css'],
            'public/css/slick/main.css',
            'public/slick'
        );
    
        mix.scripts(
            ['js/player/common.js','js/common.js'],
            'public/js/rel/main-header.js',
            'public'
        );
    
        mix.scripts(
            ['js/v1/navigation.js','js/v1/dropdown.js','js/v1/stats-options.js','js/v1/popout.js','js/timezone.js','js/popup.js','js/auth/navigation.js'],
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
    