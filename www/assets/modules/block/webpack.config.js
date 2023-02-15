var path = require('path');
var webpack = require('webpack');

module.exports = {
    context: path.join(__dirname, 'src'), // исходная директория
    entry: './main', // файл для сборки, если несколько - указываем hash (entry name => filename)
    output: {
        path: path.join(__dirname, 'dist'),
        filename: "bundle.js"
    },
    resolve: {
        extensions: ['', '.js', '.jsx']
    },
    plugins: [
       // new webpack.optimize.UglifyJsPlugin(),
        new webpack.optimize.DedupePlugin()
    ],
    module: {
        loaders: [
          //  {test: /\.jsx$/, loader: 'jsx-loader'},
         //   { test: /\.js$/, exclude: /(node_modules|bower_components)/, loader: 'babel' }
        ]
    }
};