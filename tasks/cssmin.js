/*!
 * @package    olympus-zeus
 * @subpackage cssmin.js
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.1.2
 */

'use strict';

module.exports = function (grunt, configs) {
    return {
        app: {
            files: [{
                cwd: configs.paths.assets + '/css',
                dest: configs.paths.assets + '/css',
                expand: true,
                ext: '.css',
                src: '*.css'
            }]
        }
    }
};
