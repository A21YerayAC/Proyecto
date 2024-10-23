const Encore = require('@symfony/webpack-encore');

Encore
    // La carpeta donde se generarán los archivos compilados
    .setOutputPath('public/build/')
    
    // La ruta pública para acceder a los archivos compilados
    .setPublicPath('/build')
    
    // Agrega tu archivo de entrada JavaScript
    .addEntry('app', './assets/js/app.js')
    
    // Activa el "runtime chunk"
    .enableSingleRuntimeChunk()  // O .disableSingleRuntimeChunk() si prefieres la opción desactivada
    
    // Activa el soporte de Sass/SCSS si lo necesitas
    .enableSassLoader()
    .autoProvidejQuery()
    
    // Activa el soporte de TypeScript si lo necesitas
    // .enableTypeScriptLoader()
    
    // Activa el soporte de React si lo necesitas
    // .enableReactPreset()
;

module.exports = Encore.getWebpackConfig();
