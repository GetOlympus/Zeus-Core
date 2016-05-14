<?php

namespace GetOlympus\Hera\Template\Controller;

use GetOlympus\Hera\Template\Model\Template as TemplateModel;
use GetOlympus\Hera\Field\Controller\Field;
use GetOlympus\Hera\Notification\Controller\Notification;
use GetOlympus\Hera\Render\Controller\Render;
use GetOlympus\Hera\Translate\Controller\Translate;

/**
 * Builds asked templates.
 *
 * @package Olympus Hera
 * @subpackage Template\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Template
{
    /**
     * @var TemplateModel
     */
    protected $template;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->template = new TemplateModel();
    }

    /**
     * Initialization.
     *
     * @param string $identifier
     * @param string $currentpage
     * @param string $currentsection
     * @param array $pageDetails
     */
    public function initialize($identifier, $currentpage, $currentsection, $pageDetails)
    {
        $this->template = new TemplateModel();

        // Update vars
        $this->template->setIdentifier($identifier);
        $this->template->setCurrentPage($currentpage);
        $this->template->setCurrentSection($currentsection);
        $this->template->setDetails($pageDetails);

        // Initialize layout
        $this->load();
    }

    /**
     * Build header layout.
     */
    protected function load()
    {
        $details = $this->template->getDetails();

        // Get details
        $slug = $details['slug'];
        $contents = [];

        // Check sections
        if (!empty($details['sections']) && is_array($details['sections'])) {
            $sectionContents = [];

            // Get all datas
            foreach ($details['sections'] as $k => $section) {
                /**
                 * Build section page contents.
                 *
                 * @var string $slug
                 * @var string $section
                 * @param array $sectionContents[$section]
                 * @return array $sectionContents[$section]
                 */
                $sectionContents[$k] = apply_filters('olh_template_'.$slug.'-'.$k.'_contents', []);
            }

            // Update sections' contents
            $contents['sections'] = $stns;
        }
        else {
            /**
             * Build page contents.
             *
             * @var string $slug
             * @param array $contents
             * @return array $contents
             */
            $contents = apply_filters('olh_template_'.$slug.'_contents', []);
        }

        // Check if contents are not empty
        if (empty($contents)) {
            Notification::error(Translate::t('template.errors.page_is_empty'));
        }

        // Build contents relatively to the type
        $fields = $this->templateFields($contents);

        // Get all template variables to inject in template
        $tplvars = $this->templateVars();

        // Merge all
        $vars = array_merge($tplvars, ['fields' => $fields]);

        // Display template
        Render::view('base.html.twig', $vars, 'template');
    }

    /**
     * Build each type content.
     *
     * @param array $contents
     */
    protected function templateFields($contents)
    {
        $identifier = $this->template->getIdentifier();
        $currentpage = $this->template->getCurrentPage();
        $currentsection = $this->template->getCurrentSection();

        // Build special pages
        $enabled = [$identifier];

        /**
         * Get special enabled pages.
         *
         * @param array $enabled
         * @param string $identifier
         * @return array $enabled
         */
        $enabled = apply_filters('olh_template_enabled_pages', $enabled, $identifier);

        // Define if we are in a special page or not
        $specials = in_array($currentpage, $enabled);

        $template = [];
        $usedIds = [];

        // Check contents
        if (empty($contents)) {
            return;
        }

        // Check sections
        $contents = isset($contents['sections']) ? $contents['sections'][$currentsection] : $contents;

        // Iteration on all array
        foreach ($contents as $content) {
            // Get type and id
            $type = isset($content['type']) ? $content['type'] : '';
            $id = isset($content['id']) ? $content['id'] : '';

            // Get field instance
            $field = Field::build($type, $id, $usedIds, $specials);

            // Check error
            if (is_array($field) && $field['error']) {
                $template[] = $field;
                continue;
            }

            // Update ids
            if (!empty($id)) {
                $usedIds[] = $id;
            }

            /**
             * Set current page.
             *
             * @param string $currentpage
             */
            do_action('olh_template_current_page', $currentpage);

            // Get render context
            $context = get_class($field);
            $context = str_replace('GetOlympus\\Field\\', '', $context);
            $context = strtolower($context).'Field';

            // Display field content
            $template[] = $field->render($content, [], false, $context);
        }

        return $template;
    }

    /**
     * Build header layout.
     */
    protected function templateVars()
    {
        $identifier = $this->template->getIdentifier();
        $currentpage = $this->template->getCurrentPage();
        $currentsection = $this->template->getCurrentSection();
        $details = $this->template->getDetails();

        /**
         * Display notification error on screen.
         *
         * @param array $notice
         * @return array $notice
         */
        $notice = apply_filters('olh_template_error', []);

        // Works on title
        $title = $details['title'];

        // Works on subtitle
        $subtitle = $currentsection;

        // Build urls
        $urls = [
            'capabilities' => [
                'url' => current_user_can(OLH_WP_CAP_MAX) 
                    ? admin_url('admin.php?page='.$identifier.'&do=olz-action&from=footer&make=capabilities') : '',
                'label' => Translate::t('template.capabilities'),
            ],
        ];

        /**
         * Display footer usefull urls.
         *
         * @param array $urls
         * @param string $identifier
         * @return array $urls
         */
        $urls = apply_filters('olh_template_footer_urls', $urls, $identifier);

        // Partners
        $partners = [
            [
                'url' => 'http://www.takeatea.com',
                'label' => 'Take a tea',
                'image' => OLH_URI.'img/partners/takeatea.png',
            ],
            [
                'url' => 'http://www.basketsession.com/',
                'label' => 'Éditions REVERSE Magazine',
                'image' => OLH_URI.'img/partners/basketsession.png',
            ],
        ];

        // Current page to check
        $current = empty($currentpage) ? $identifier : $currentpage;

        // Get all pages with link, icon and slug
        $template = [
            'identifier' => $identifier,
            'version' => OLH_VERSION,
            'currentPage' => empty($currentpage) ? $identifier : $currentpage,
            'currentSection' => empty($currentsection) ? '' : $currentsection,

            'title' => empty($title) ? Translate::t('template.olympus') : $title,
            'subtitle' => empty($subtitle) ? '' : $subtitle,
            'description' => isset($details['description']) ? $details['description'] : '',
            'submit' => isset($details['submit']) ? $details['submit'] : false,
            'sections' => isset($details['sections']) ? $details['sections'] : [],
            'notice' => $notice,
            'urls' => $urls,
            'partners' => $partners,

            // texts
            't_title' => Translate::t('template.olympus'),
            't_update' => Translate::t('template.update'),
            't_copyright' => Translate::t('template.copyright'),
            't_quote' => OLH_QUOTE,
        ];

        // Get template
        return $template;
    }
}
