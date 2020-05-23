module.exports = {
  env: {
    browser: true,
    es6: true
  },
  extends: [
    "plugin:vue/recommended",
    "eslint:recommended",
    "prettier/vue",
    "plugin:prettier/recommended"
  ],
  globals: {
    Atomics: 'readonly',
    SharedArrayBuffer: 'readonly'
  },
  parserOptions: {
    ecmaVersion: 11
  },
  plugins: [
    'vue'
  ],
  rules: {
    "no-undef": 1,
    "vue/no-unused-vars": 1,
    "no-unused-vars": 1,
  },
  
}
