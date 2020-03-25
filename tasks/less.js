/*!
 * @package    olympus-zeus
 * @subpackage less.js
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.1.2
 */

'use strict';

module.exports = function (grunt, configs) {
    return {
        app: {
            options: {
                optimization: 2
            },
            files: {
                [configs.paths.assets + '/css/zeus.css']: [configs.paths.src + '/assets/less/01.global.less'],
                [configs.paths.assets + '/css/zeus-metabox.css']: [configs.paths.src + '/assets/less/10.metabox.less']
            }
        }
    }
};
