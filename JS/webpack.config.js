const path = require('path')

module.exports = () => {
    return {
        mode: 'production',
        entry: {
            index: path.resolve(__dirname, 'index.js')
        },
        output: {
            path: path.resolve(__dirname, 'Dist'),
            filename: `[name].js`
        },
        module: {
            rules: [
                {
                    test: /\.(js)$/,
                    exclude: /node_modules/
                }
            ]
        },
        resolve: {
            extensions: ['*', '.js']
        }
    }
}