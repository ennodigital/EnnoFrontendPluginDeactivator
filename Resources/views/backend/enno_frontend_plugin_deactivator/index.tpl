{extends file="parent:backend/_base/enno_frontend_plugin_deactivator.tpl"}

{block name="content/main"}
    <div class="page-header">
        <h1>Übersicht</h1>
    </div>
    {* <pre>{$plugins|@print_r}</pre> *}
    {* <pre>{$shops|@print_r}</pre> *}

    <div>
        <ul class="nav nav-tabs" role="tablist">
            {foreach name=shops key=shopID item=shop from=$shops}
                <li role="presentation" {if $smarty.foreach.shops.first}class="active"{/if}><a href="#tab{$shopID}" aria-controls="tab{$shopID}" role="tab" data-toggle="tab">{$shop}</a></li>
            {/foreach}
        </ul>

        <!-- Tab panes -->
        <div class="tab-content" style="padding: 20px 0;">
            {foreach name=shops key=shopID item=shop from=$shops}
            <div role="tabpanel" class="tab-pane {if $smarty.foreach.shops.first}active{/if}" id="tab{$shopID}">
                <form class="form-horizontal" action="{url controller="EnnoFrontendPluginDeactivator" action="save" __csrf_token=$csrfToken}" style="margin-bottom:40px;">
                    <input type="hidden" name="name" value="EnnoFrontendPluginDeactivator">
                    <input type="hidden" name="shopID" value="{$shopID}">
                    <h2>Shop: {$shop}</h2>
                    <div class="table-responsive enno-discount-levels-table" style="margin-top:20px;">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>PluginName</th>
                                    <th></th>
                                    <th>Aktiv</th>
                                    <th>Frontend Deaktivieren</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach $plugins.$shopID as $plugin}
                                    <tr>
                                        <td>{$plugin.label}</td>
                                        <td><i>({$plugin.name})</i></td>
                                        <td>{if $plugin.active}Ja{else}Nein{/if}</td>
                                        <td><input type="hidden" name="plugins[{$plugin.name}][id]" value="{$plugin.id}"><input type="checkbox" name="plugins[{$plugin.name}][rule_active]" {if $plugin.rule_active}checked{/if}></td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-success" style="float:right; margin-bottom:50px;">Speichern</button>
                </form>
            </div>
            {/foreach}
        </div>
    </div>


{/block}
