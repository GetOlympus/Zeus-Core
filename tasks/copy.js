/*!
 * @package olympus-hera
 * @subpackage copy.js
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 */

module.exports = {
  app: {
    files: [
      {
        //Fonts
        expand: true,
        flatten: true,
        src: [
          '<%= olympus.paths.bow %>/font-awesome/fonts/*'
        ],
        dest: '<%= olympus.paths.tar %>/fonts/'
      },

      {
        //Images
        cwd: '<%= olympus.paths.src %>/img/',
        expand: true,
        flatten: false,
        src: [
          '**/*'
        ],
        dest: '<%= olympus.paths.tar %>/img/'
      }
    ]
  },
};
