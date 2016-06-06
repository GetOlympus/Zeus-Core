/*!
 * @package olympus-hera
 * @subpackage imagemin.js
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 */

module.exports = {
  app: {
    files: [{
      expand: true,
      cwd: '<%= olympus.paths.tar %>/img/',
      src: [
        '**/*.{png,gif,jpg,jpeg}'
      ],
      dest: '<%= olympus.paths.tar %>/img/'
    }],

    options: {
      cache: false
    }
  }
};
