/*!
 * @package olympus-hera
 * @subpackage clean.js
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 */

module.exports = {
  app: [
    '<%= olympus.paths.tar %>/css/*',
    '<%= olympus.paths.tar %>/fonts/*',
    '<%= olympus.paths.tar %>/img/*',
    '<%= olympus.paths.tar %>/js/*'
  ],

  src: [
    '<%= olympus.paths.src %>/css'
  ]
};
