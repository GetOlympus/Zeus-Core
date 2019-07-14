/*!
 * @package olympus-zeus
 * @subpackage clean.js
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 */

'use strict';

module.exports = function (grunt, configs) {
  return {
    mo: [
      configs.paths.i18n + '/*.mo'
    ]
  }
};
