const { colors } = require('tailwindcss/defaultTheme');

module.exports = {
    theme: {
        fontFamily: {
            display: ['Nunito', 'sans-serif'],
            body: ['Graphik', 'sans-serif'],
        },
        extend: {
            colors: {
                blue: {
                    ...colors.blue,
                    '800': '#2b6cb0'
                },
                gray: {
                    ...colors.gray,
                    '800': '#2d3748',
                }
            },
        },
    },
    variants: {},
    plugins: [
        require('tailwindcss'),
        require('@tailwindcss/ui'),
        require('autoprefixer'),
    ],
    purge: [
        './resources/views/**/*.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ]
}
