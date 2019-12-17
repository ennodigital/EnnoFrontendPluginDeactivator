<?php

namespace EnnoFrontendPluginDeactivator\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Template_Manager;
use Enlight_Event_EventManager as EventManager;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Collections\ArrayCollection;

class CoreTemplate implements SubscriberInterface
{
    private $pluginName = 'EnnoFrontendPluginDeactivator';

    private $db;
    private $config;
    private $container;
    private $templateManager;
    private $pluginBaseDirectory;

    private function config($key = null)
    {
        if(! $this->config )
            $this->config = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName($this->pluginName, Shopware()->Shop());
        return $key ? (is_array($this->config)?$this->config[$key]:null) : $this->config;
    }

    public function __construct(Enlight_Template_Manager $templateManager, ContainerInterface $container, EventManager $eventManager)
    {
        $this->container = $container;
        $this->pluginBaseDirectory = $container->get('kernel')->getPlugins()[$this->pluginName]->getPath();
        $this->templateManager     = $templateManager;
        $this->db     = Shopware()->Db();
        $this->eventManager = $eventManager;

        $this->autoCreatorPath = $this->pluginBaseDirectory .'/'.'AutoCreator/';
    }

    private $autoCreatorPath;
    private $eventManager;
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Plugins_ViewRenderer_PreRender' => 'onPluginViewRender',
            'Enlight_Template_Manager_FilterBuildInheritance' => 'onFilterTemplate',
            'EnnoFrontendPluginDeactivator_Filter_Plugins_Default' => 'onFilterPluginsDefault'
        ];
    }
    public function onFilterTemplate(\Enlight_Event_EventArgs $args)
    {
        $folders = $args->getReturn();
        if($this->config('active'))
        {
            $search_key = 'EnnoFrontendPluginDeactivator';

            $plugins = $this->getDeactivedPluginsList();
            array_push($plugins, 'EnnoFrontendPluginDeactivator');
            $search_key = implode ('|', $plugins);

            foreach ($folders as $key => $value)
            {
                if(preg_match('('.$search_key.')', $value) === 1)
                {
                    unset($folders[$key]);
                }
            }
            array_push($folders, $this->autoCreatorPath);
        }
        return $folders;
    }
    public function onPluginViewRender(\Enlight_Event_EventArgs $args)
    {
        $template = $args->get('template');
        $tpl_files = explode('|',$template->template_resource);

        foreach ($tpl_files as $key => $tpl_file)
        {
            if( file_exists($this->autoCreatorPath . $tpl_file) ) continue;
            $splitted = str_split($tpl_file, strrpos ($tpl_file,'/',0));
            if (!is_dir($this->autoCreatorPath . $splitted[0]))
            {
                mkdir($this->autoCreatorPath . $splitted[0], 0755, true);
            }
            $fp = fopen($this->autoCreatorPath . $tpl_file, 'w');
            fwrite($fp, $content);
            fclose($fp);
            chmod($this->autoCreatorPath . $tpl_file, 0644);
        }
    }

    private function getDeactivedPluginsList()
    {
        $list = array();

        $plugins = $this->db->fetchAll('SELECT * FROM `s_plugin_enno_frontend_plugin_deactivator_list` WHERE shopID IN (0,?) ORDER BY `pluginID` ASC, `priority` ASC', [Shopware()->Shop()->getID()]);

        if(is_array($plugins) && !empty($plugins))
        {
            foreach ($plugins as $plugin)
            {
                if(!$plugin['pluginName'] || in_array($plugin['pluginName'], $plugins)) continue;
                if(!$plugin['filterEvent']) $plugin['filterEvent'] = 'EnnoFrontendPluginDeactivator_Filter_Plugins_Default';

                $pluginActive = $this->eventManager->filter($plugin['filterEvent'], true, [ 'plugin' => $plugin ]);
                if(!$pluginActive)
                {
                    array_push($list, $plugin['pluginName']);
                }
            }
        }

        return $list;
    }

    public function onFilterPluginsDefault(\Enlight_Event_EventArgs $args)
    {
        return false;
    }

}
