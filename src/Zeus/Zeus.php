<?php declare(strict_types=1);

namespace GetOlympus\Zeus;

use GetOlympus\Zeus\Application\Application;

/**
 * Package constants.
 */

// Directory separator
defined('S') or define('S', DIRECTORY_SEPARATOR);
// The path
define('OL_ZEUS_PATH', rtrim(dirname(dirname(dirname(__FILE__))), S).S);

// Defining if we are in admin panel or not
define('OL_ZEUS_ISADMIN', defined('OL_ISADMIN') ? OL_ISADMIN : is_admin());
// Defining if debug is enabled or not
define('OL_ZEUS_DEBUG', defined('WP_DEBUG') ? WP_DEBUG : false);
// Blog home url
define('OL_ZEUS_HOME', defined('OL_BLOG_HOME') ? OL_BLOG_HOME : get_option('home'));
// URI
define('OL_ZEUS_URI', defined('DISTPATH') ? str_replace(WEBPATH, '/../', DISTPATH) : OL_ZEUS_HOME.'/app/assets/');

// Zeus Assets folder
define('OL_ZEUS_ASSETSPATH', OL_ZEUS_PATH.'app'.S.'assets'.S);
// Assets folder
define('OL_ZEUS_DISTPATH', defined('DISTPATH') ? DISTPATH : OL_ZEUS_PATH.'app'.S.'assets'.S);


/**
 * Olympus Zeus Core.
 *
 * @package    OlympusZeusCore
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

abstract class Zeus extends Application
{
    /**
     * @var array
     */
    protected $defaultcontrols = [
        // Dionysos field components
        //'GetOlympus\\Dionysos\\Control\\ImageSelect',
        //'GetOlympus\\Dionysos\\Control\\SimpleNotice',
    ];

    /**
     * @var array
     */
    protected $defaultfields = [
        // Dionysos field components
        'GetOlympus\\Dionysos\\Field\\Code',
        'GetOlympus\\Dionysos\\Field\\Color',
        'GetOlympus\\Dionysos\\Field\\Content',
        'GetOlympus\\Dionysos\\Field\\Link',
        'GetOlympus\\Dionysos\\Field\\Radio',
        'GetOlympus\\Dionysos\\Field\\Select',
        'GetOlympus\\Dionysos\\Field\\Text',
        'GetOlympus\\Dionysos\\Field\\Textarea',
        'GetOlympus\\Dionysos\\Field\\Toggle',
        'GetOlympus\\Dionysos\\Field\\Upload',
        'GetOlympus\\Dionysos\\Field\\Wordpress',
    ];
}
