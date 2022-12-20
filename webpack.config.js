const path = require('path');

module.exports = {
  entry: path.join(__dirname, "src/js", "main.js"),
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'main.bundle.js',
  },
  mode: "development",
  devtool: false,
  module: {
    rules: [
        { 
            test: /\.(js|jsx)$/, 
            exclude: /node_modules/, 
            use: ["babel-loader"] 
        },
        { 
          test: /\.s[ac]ss$/i, 
          exclude: /node_modules/, 
          use: [
            // Creates `style` nodes from JS strings
          "style-loader",
          // Translates CSS into CommonJS
          "css-loader",
          // Compiles Sass to CSS
          "sass-loader"] 
      },
    ],
},

};