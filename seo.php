<?php
/**
 * SEO v0.9.2
 *
 * This plugin adds an SEO Tab to every pages for managing SEO data.
 *
 * Licensed under the MIT license, see LICENSE.
 *
 * @package     SEO
 * @version     0.9.2
 * @link        <https://github.com/paulmassen/grav-plugin-seo>
 * @author      Paul Massendari <paul@massendari.com>
 * @copyright   2017, Paul Massendari
 * @license     <http://opensource.org/licenses/MIT>        MIT
 */

namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Page\Page;
use Grav\Common\Data\Blueprints;
use Grav\Common\Page\Pages;
use RocketTheme\Toolbox\Event\Event;

/**
 * SEO Plugin
 *
 * This plugin adds an user-friendly SEO tab for your user to manage metadata tags
 * and appearance on Search Engine Results and Social Networks.
 */
class seoPlugin extends Plugin
{

    /** -------------
     * Public methods
     * --------------
     */

    /**
     * Return a list of subscribed events.
     *
     * @return array    The list of events of the plugin of the form
     *                      'name' => ['method_name', priority].
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
          //  'onBlueprintCreated' => ['onBlueprintCreated',  0]
        ];
    }

    /**
     * Initialize configuration
     */
    public function onPluginsInitialized()
    {

        // Set default events
        $events = [
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
        ];

        // Set admin specific events
        if ($this->isAdmin()) {
            $this->active = false;
            $events = [
                'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
                'onBlueprintCreated' => ['onBlueprintCreated', 0]
            ];
        }

        // Register events
        $this->enable($events);
    }

    /**
     * Extend page blueprints with SEO configuration options.
     *
     * @param Event $event
     */
    public function onBlueprintCreated(Event $event)
 {
     $newtype = $event['type'];
     if (0 === strpos($newtype, 'modular/')) {
        } else {
                    $blueprint = $event['blueprint'];
        if ($blueprint->get('form/fields/tabs', null, '/')) {
            $blueprints = new Blueprints(__DIR__ . '/blueprints/');
            $extends = $blueprints->get($this->name);
            $blueprint->extend($extends, true);
        
        }
        }
        
    }

    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }
}
