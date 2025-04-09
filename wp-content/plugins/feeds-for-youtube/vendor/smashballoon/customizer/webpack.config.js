const path = require('path');

module.exports = {
    mode: "production",
    entry: {
        builder: "./assets/js/builder.js",
        main: "./assets/js/main.js"
    },
    output: {
        path: path.resolve(__dirname, 'assets/js'),
        filename: '[name].min.js',
        sourceMapFilename: '[name].js.map'
    },
    devtool: 'inline-source-map',
    devServer: {
        static: './assets/js',
        host: 'localhost',
        hot: true,
        allowedHosts: ['all'],
        port: 8089
    },
    module: {
        rules: [
            {
                test: /\.m?js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            }
        ]
    }
};