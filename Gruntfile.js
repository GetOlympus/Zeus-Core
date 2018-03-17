/*!
 * @package olympus-zeus-core
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 */

module.exports = function(grunt) {
  var path = require('path'),
    olympus = {
      paths: {
        bow: 'bower_components',
        src: 'src/Zeus/Resources/assets',
        tar: 'app/assets'
      }
    };

  // measures the time each task takes
  require('time-grunt')(grunt);

  // load grunt config
  require('load-grunt-config')(grunt, {
    configPath: path.join(__dirname, 'tasks'),
    config: {
      olympus: olympus
    }
  });
};
