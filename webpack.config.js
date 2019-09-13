const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const path = require("path");

module.exports = {
  entry: {
    admin: "./assets/js/admin.js",
    "admin.customersearch": "./assets/js/admin.customersearch.js",
    "admin.invoice.view": "./assets/js/admin.invoice.view.js",
    "admin.invoicesearch": "./assets/js/admin.invoicesearch.js",
    "invoice.pay": "./assets/js/invoice.pay.js",
    "invoice.edit": "./assets/js/invoice.edit.js"
  },
  output: {
    filename: "[name].min.js",
    path: path.resolve(__dirname, "assets/js/")
  },
  module: {
    rules: [
      {
        test: /\.(css|scss|sass)$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: "css-loader",
            options: {
              url: false
            }
          },
          {
            loader: "postcss-loader",
            options: {
              plugins: () => [require("autoprefixer")]
            }
          },
          "sass-loader"
        ]
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "../css/[name].min.css",
      allChunks: true
    })
  ],
  mode: "production"
};
