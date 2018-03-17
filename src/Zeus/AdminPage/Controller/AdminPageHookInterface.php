<?php

namespace GetOlympus\Zeus\AdminPage\Controller;

/**
 * AdminPage hook interface.
 *
 * @package Olympus Zeus-Core
 * @subpackage AdminPage\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.7
 *
 */

interface AdminPageHookInterface
{
    /**
     * Initialize menu pages.
     */
    public function init();

    /**
     * Get section fields.
     */
    public function renderFields();

    /**
     * Set section fields.
     */
    public function saveFields();

    /**
     * Save files.
     *
     * @param array $ids
     */
    public function saveFiles($ids);

    /**
     * Save request.
     *
     * @param array $ids
     */
    public function saveRequest($ids);
}
