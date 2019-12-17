<?php

use Doctrine\DBAL\Driver\PDOStatement;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Shopware\Components\CSRFWhitelistAware;
use Shopware\Models\Article\Repository as ArticleRepo;
use Shopware\Models\Article\SupplierRepository;
use Shopware\Models\Emotion\Repository as EmotionRepo;
use Shopware\Models\Form\Repository as FormRepo;

class Shopware_Controllers_Backend_EnnoFrontendPluginDeactivator extends Enlight_Controller_Action implements CSRFWhitelistAware
{

    private $table_structure = array(
        'id'                     => 'int',
        'active'                 => 'boolean',
        'shopID'                 => 'int',
        'name'                   => 'string',
        'position'               => 'int',
        'internalComment'        => 'string',
        'headline'               => 'string',
        'content'                => 'string',
        'attributes'             => 'string',
        'attributesAsTable'      => 'boolean',
        'tplFile'                => 'string',
        'showDescription'        => 'boolean',
        'showProperties'         => 'boolean',
        'showLinks'              => 'boolean',
        'showDownloads'          => 'boolean',
        'showRating'             => 'boolean',
        'showRatingCount'        => 'boolean',
        'cmsSupportID'           => 'int',
        'categoriesWhitelist'    => 'boolean',
        'articlesWhitelist'      => 'boolean',
        'customergroupWhitelist' => 'boolean',
    );


    public function getWhitelistedCSRFActions()
    {
        return ['index'];
    }

    public function preDispatch()
    {
        $this->get('template')->addTemplateDir(__DIR__ . '/../../Resources/views/');
    }

    public function postDispatch()
    {
        $csrfToken = $this->container->get('BackendSession')->offsetGet('X-CSRF-Token');
        $this->View()->assign([ 'csrfToken' => $csrfToken ]);
    }

    public function helpAction()
    {
    }

    public function indexAction()
    {
        $pluginName = $this->Request()->getParam('pluginName');
        $pluginName = $pluginName?$pluginName:'EnnoFrontendPluginDeactivator';

        $plugins = [];
        $shops = $this->getShops();
        foreach ($shops as $shopID => $shop)
        {

            $sql = "SELECT scp.id, scp.namespace, scp.name, scp.label, scp.active, spefpd.id as rule_active FROM s_core_plugins scp LEFT JOIN s_plugin_enno_frontend_plugin_deactivator_list spefpd ON scp.id = spefpd.pluginID AND shopID IN (0,?) ANd ruleOf = (SELECT scp2.id FROM s_core_plugins scp2 WHERE scp2.name = ?) WHERE scp.source != 'Default' AND scp.name != 'EnnoFrontendPluginDeactivator' ORDER BY rule_active DESC, label ASC";

            $plugins[$shopID] = Shopware()->Db()->fetchAll($sql, [$shopID, $pluginName]);
        }

        $this->View()->assign('plugins', $plugins);
        $this->View()->assign('shops', $shops);
    }



    public function saveAction()
    {
        $name = $this->Request()->getParam('name');
        $plugins = $this->Request()->getParam('plugins');
        $shopID = $this->Request()->getParam('shopID');
        $event = $this->Request()->getParam('event');
        $priority = $this->Request()->getParam('priority');

        $priority = $priority?$priority:0;

        if(!$name || !$shopID)
        {
            $this->View()->assign(['success' => false]);
            return;
        }
        $id = Shopware()->Db()->fetchOne("SELECT id FROM s_core_plugins WHERE name = '{$name}'");
        if(!$id)
        {
            $this->View()->assign(['success' => false]);
            return;
        }

        Shopware()->Db()->query("DELETE FROM s_plugin_enno_frontend_plugin_deactivator_list WHERE shopID = {$shopID} AND ruleOf = {$id} ");

        foreach ($plugins as $pluginName => $plugin)
        {
            if($plugin['rule_active'])
            {
                Shopware()->Db()->query("INSERT INTO s_plugin_enno_frontend_plugin_deactivator_list (pluginID, pluginName, ruleOf, priority, filterEvent, shopID) VALUES ((SELECT id FROM s_core_plugins WHERE name = '{$pluginName}'), '{$pluginName}', {$id}, {$priority}, '{$event}', {$shopID})");
            }
        }

        $this->forward('index');

    }
    public function changeActiveAction()
    {
        $id = $this->Request()->getParam('id');
        $pluginName = $this->Request()->getParam('pluginName');
        $shopID = $this->Request()->getParam('shopID');
        $active = $this->Request()->getParam('active');
        $priority = $this->Request()->getParam('priority');
        $event = $this->Request()->getParam('event');

        if(!$id || !$pluginName || !$shopID)
        {
            $this->View()->assign(['success' => false]);
            return;
        }
        try
        {
            if($active)
            {
                $ruleExist = Shopware()->Db()->fetchOne(
                    "SELECT * FROM s_plugin_enno_frontend_plugin_deactivator_list WHERE pluginName = ? AND ruleOf = ?",
                    array($pluginName, $id)
                );
                if(!$ruleExist)
                {
                    $priority = $priority?$priority:0;
                    Shopware()->Db()->query("INSERT INTO s_plugin_enno_frontend_plugin_deactivator_list (pluginID, pluginName, ruleOf, priority, filterEvent) VALUES ((SELECT id FROM s_core_plugins WHERE name = '{$pluginName}'), '{$pluginName}', {$id}, {$priority}, '{$event}')");
                }
            }
            else
            {
                Shopware()->Db()->query("DELETE FROM s_plugin_enno_frontend_plugin_deactivator_list WHERE pluginName = '{$pluginName}' AND ruleOf = {$id}");
            }

        }
        catch (\Exception $e)
        {
            $this->View()->assign(['success' => false]);
        }
    }


    private function getShops()
    {
        $shops = [];
        $db_shops = Shopware()->Db()->fetchAll( "SELECT * FROM `s_core_shops`");
        if(is_array($db_shops))
        {
            foreach ($db_shops as $db_shop)
            {
                $shops[$db_shop['id']] = $db_shop['name'];
            }
        }
        return $shops;
    }
}
