<?php
/**
 * Next Up plugin for Craft CMS 3.x
 *
 * Get the next upcoming event date from the matrix
 *
 * @link      https://percipio.london/
 * @copyright Copyright (c) 2022 Percipio.london
 */

namespace percipiolondon\nextup;

use craft\base\Element;
use craft\elements\Entry;
use craft\events\ModelEvent;
use percipiolondon\nextup\fields\NextUpField as NextUpFieldField;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;

use percipiolondon\nextup\services\NextUpService;
use yii\base\Event;

/**
 * Class NextUp
 *
 * @author    Percipio.london
 * @package   NextUp
 * @since     1.0.0
 *
 */
class NextUp extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var NextUp
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public $hasCpSettings = false;

    /**
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'percipiolondon\nextup\console\controllers';
        }

        $this->setComponents([
            'nextup' => NextUpService::class,
        ]);

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = NextUpFieldField::class;
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Event::on(
            Entry::class,
            Element::EVENT_BEFORE_SAVE,
            function(ModelEvent $e) {
                $entry = $e->sender;

                if(
                    $entry->type->handle == 'locationEvent' ||
                    $entry->type->handle == 'hybridEvent' ||
                    $entry->type->handle == 'onlineEvent'
                ){
                    $entry->setFieldValue('nextUpcomingEvent', NextUp::getInstance()->nextup->saveLatestEvent($entry,true));
                }
            }
        );

        Craft::info(
            Craft::t(
                'next-up',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
