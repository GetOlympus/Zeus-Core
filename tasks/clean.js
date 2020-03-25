/*!
 * @package    olympus-zeus
 * @subpackage clean.js
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 */

'use strict';

module.exports = function (grunt, configs) {
    return {
        assets: [
            configs.paths.assets + '/css/*.css',
            configs.paths.assets + '/js/*.js'
        ],
        mo: [
            configs.paths.i18n + '/*.mo'
        ]
    }
};
