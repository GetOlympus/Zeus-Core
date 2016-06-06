/*!
 * @package olympus-hera
 * @subpackage less.js
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 */

var grunt = require('grunt'),
  _ = grunt.util._,
  _configs = {
    //normal
    white: '#ffffff',
    black: '#000000',
    blue: '#2ea2cc',
    danger: '#dd3d36',
    orange: '#ffba00',
    red: '#ff0000',
    //gray
    graylighter: '#fbfbfb',
    graylight: '#f1f1f1',
    gray: '#aaaaaa',
    graymedium: '#999999',
    graydark: '#3b3d3c',
    graydarker: '#303231',
    grayblack: '#111111',
    //fonts
    fontmain: '"Open Sans",sans-serif',
    fontsecond: 'Verdana,arial,sans-serif',
    fonticon: 'FontAwesome'
  };

module.exports = {
  core: {
    options: {
      modifyVars: _.extend({}, {
        primary: '#75cd45',
        second: '#e5f7e5',
        main: '#55bb3a'
      }, _configs),
      optimization: 2
    },
    files: {
      '<%= olympus.paths.src %>/css/olympus-core.css': [
        '<%= olympus.paths.src %>/less/core.less',
        '<%= olympus.paths.src %>/less/core/*.less'
      ]
    }
  },

  login: {
    options: {
      modifyVars: _.extend({}, {
        primary: '#75cd45',
        main: '#55bb3a'
      }, _configs),
      optimization: 2
    },
    files: {
      '<%= olympus.paths.src %>/css/olympus-login.css': [
        '<%= olympus.paths.src %>/less/login.less',
        '<%= olympus.paths.src %>/less/login/*.less'
      ]
    }
  }
};
