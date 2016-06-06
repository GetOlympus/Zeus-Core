/*!
 * @package olympus-hera
 * @subpackage cssmin.js
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 */

module.exports = {
  src: {
    files: {
      '<%= olympus.paths.tar %>/css/olympus-core.css': [
        '<%= olympus.paths.bow %>/font-awesome/css/font-awesome.css',
        '<%= olympus.paths.src %>/css/olympus-core.css'
      ],

      '<%= olympus.paths.tar %>/css/olympus-login.css': [
        '<%= olympus.paths.bow %>/fontawesome/css/font-awesome.css',
        '<%= olympus.paths.src %>/css/olympus-login.css'
      ]
    }
  }
};
