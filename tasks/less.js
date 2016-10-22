/*!
 * @package olympus-hera
 * @subpackage less.js
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 */

var _configs = {
  // normal
  white: '#ffffff',
  black: '#000000',
  blue: '#1297e0',
  red: '#e74c3c',
  green: '#99d537',

  // gray
  graylighter: '#fdfdfd',
  graylight: '#f1f1f1',
  graymedium: '#999999',
  graydark: '#282c37',
  graydarker: '#181a21',

  // fonts
  fontmain: '"Open Sans",sans-serif',
  fonticon: 'FontAwesome',

  // login
  loginerror: '#dd3d36',
  loginmessage: '#2ea2cc',
  loginbutton: '#2c92da',

  // skeleton
  skelsilver: '#cbced3',
  skelmetal: '#67717d',
  skelblack: '#282c38'
};

module.exports = {
  core: {
    options: {
      modifyVars: _configs,
      optimization: 2
    },
    files: {
      '<%= olympus.paths.src %>/css/olympus-hera-core.css': [
        '<%= olympus.paths.src %>/less/core.less',
        '<%= olympus.paths.src %>/less/core/*.less'
      ]
    }
  },

  login: {
    options: {
      modifyVars: _configs,
      optimization: 2
    },
    files: {
      '<%= olympus.paths.src %>/css/olympus-hera-login.css': [
        '<%= olympus.paths.src %>/less/login.less',
        '<%= olympus.paths.src %>/less/login/*.less'
      ]
    }
  }
};
