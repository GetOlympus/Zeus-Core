<?php

namespace GetOlympus\Hera\Template\Controller;

/**
 * Template interface.
 *
 * @package Olympus Hera
 * @subpackage Template\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface TemplateInterface
{
    /**
     * Initialization.
     *
     * @param string $identifier
     * @param string $currentpage
     * @param string $currentsection
     * @param array $pageDetails
     */
    public function init($identifier, $currentpage, $currentsection, $pageDetails);

    /**
     * Build header layout.
     */
    public function load();

    /**
     * Build each type content.
     *
     * @param array $contents
     */
    public function templateFields($contents);

    /**
     * Build header layout.
     */
    public function templateVars();
}
