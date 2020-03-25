<?php

namespace GetOlympus\Zeus\Utils;

use GetOlympus\Hera\Hera;

/**
 * Render HTML entities.
 *
 * @package    OlympusZeusCore
 * @subpackage Utils
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class Render extends Hera
{
    /**
     * @var string
     */
    protected $distpath = OL_ZEUS_DISTPATH;

    /**
     * @var array
     */
    protected $paths = [
        'core' => OL_ZEUS_PATH.'src'.S.'Zeus'.S.'Resources'.S.'views',
    ];

    /**
     * @var array
     */
    /*protected $scripts = [
        'zeus-tabs'   => OL_ZEUS_ASSETSPATH.'js'.S.'zeus-tabs.js',
        'zeus-upload' => OL_ZEUS_ASSETSPATH.'js'.S.'zeus-upload.js',
    ];*/

    /**
     * @var array
     */
    protected $styles = [
        'zeus-css' => OL_ZEUS_ASSETSPATH.'css'.S.'zeus.css',
    ];

    /**
     * @var string
     */
    protected $uri = OL_ZEUS_URI;

    /**
     * @var bool
     */
    protected $usecache = OL_ZEUS_DEBUG;
}
