<?php

namespace EnnoFrontendPluginDeactivator;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;

use Doctrine\DBAL\Connection;

class EnnoFrontendPluginDeactivator extends Plugin
{

    public function install(InstallContext $installContext)
    {
        $this->createDatabaseTables();
    }

    public function uninstall(UninstallContext $uninstallContext)
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $db = $this->container->get('dbal_connection');
        $db->executeQuery("DROP TABLE s_plugin_enno_frontend_plugin_deactivator_list");

        $uninstallContext->scheduleClearCache(UninstallContext::CACHE_LIST_ALL);
    }


    public function update(UpdateContext $updateContext)
    {
        $updateContext->scheduleClearCache(UpdateContext::CACHE_LIST_ALL);
    }

    public function activate(ActivateContext $activateContext)
    {
        // on plugin activation clear the cache
        $activateContext->scheduleClearCache(ActivateContext::CACHE_LIST_ALL);
    }

    public function deactivate(DeactivateContext $deactivateContext)
    {
        // on plugin deactivation clear the cache
        $deactivateContext->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);
    }
    /**
     * creates  database table
     */
    private function createDatabaseTables()
    {

        $db = $this->container->get('dbal_connection');

        $db->executeQuery(
            'CREATE TABLE IF NOT EXISTS `s_plugin_enno_frontend_plugin_deactivator_list` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `pluginID` INT(11) NOT NULL,
                `pluginName` VARCHAR(255) NOT NULL,
                `ruleOf`VARCHAR(255) NOT NULL,
                `priority` INT(11) NULL,
                `filterEvent` VARCHAR(255) NULL,
                `shopID` INT(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;'
        );
    }
}
