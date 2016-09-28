/*!
 * @package olympus-hera
 * @subpackage uglify.js
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 */

module.exports = {
  src: {
    files: {
      //main package contains all scripts
      '<%= olympus.paths.tar %>/js/olympus-core.js': [
        //jQuery
        '<%= olympus.paths.bow %>/jquery/dist/jquery.js',
        //HandlebarsJS
        '<%= olympus.paths.bow %>/handlebars/handlebars.js',
        //Codemirror
        '<%= olympus.paths.bow %>/codemirror/lib/codemirror.js',
        '<%= olympus.paths.bow %>/codemirror/mode/clike/clike.js',
        '<%= olympus.paths.bow %>/codemirror/mode/css/css.js',
        '<%= olympus.paths.bow %>/codemirror/mode/diff/diff.js',
        '<%= olympus.paths.bow %>/codemirror/mode/htmlmixed/htmlmixed.js',
        '<%= olympus.paths.bow %>/codemirror/mode/javascript/javascript.js',
        '<%= olympus.paths.bow %>/codemirror/mode/markdown/markdown.js',
        '<%= olympus.paths.bow %>/codemirror/mode/php/php.js',
        '<%= olympus.paths.bow %>/codemirror/mode/python/python.js',
        '<%= olympus.paths.bow %>/codemirror/mode/ruby/ruby.js',
        '<%= olympus.paths.bow %>/codemirror/mode/shell/shell.js',
        '<%= olympus.paths.bow %>/codemirror/mode/sql/sql.js',
        '<%= olympus.paths.bow %>/codemirror/mode/xml/xml.js',
        '<%= olympus.paths.bow %>/codemirror/mode/yaml/yaml.js',
        //Leaflet
        '<%= olympus.paths.bow %>/leaflet/dist/leaflet-src.js',
        //Pickadate
        '<%= olympus.paths.bow %>/pickadate/lib/picker.js',
        '<%= olympus.paths.bow %>/pickadate/lib/picker.date.js',
        '<%= olympus.paths.bow %>/pickadate/lib/picker.time.js',
        '<%= olympus.paths.bow %>/pickadate/lib/legacy.js',
        //Selectize
        '<%= olympus.paths.bow %>/selectize/dist/js/standalone/selectize.js',
        //Zeus packages
        '<%= olympus.paths.src %>/js/**/*.js',
        '!<%= olympus.paths.src %>/js/olympus-core.js',
        '<%= olympus.paths.src %>/js/olympus-core.js'
      ]
    }
  },

  options: {
    preserveComments: 'some'
  }
};
