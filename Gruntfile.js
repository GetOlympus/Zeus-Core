/*!
 * @package olympus-zeus
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 */

'use strict';

module.exports = function (grunt) {
    // measures the time each task takes
    require('time-grunt')(grunt);

    // load grunt config
    require('load-grunt-config')(grunt, {
        configPath: require('path').join(__dirname, 'tasks'),
        config: grunt.file.readJSON('tasks/options.json')
    });
};
