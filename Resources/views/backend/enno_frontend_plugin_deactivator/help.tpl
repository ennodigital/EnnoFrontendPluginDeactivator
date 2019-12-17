{extends file="parent:backend/_base/enno_frontend_plugin_deactivator.tpl"}

{block name="content/main"}
    <div class="page-header">
        <h1>Hilfe</h1>
    </div>
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        Titel
                    </a>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                    Text
                </div>
            </div>
        </div>
    </div>
    <div class="enno-help-contact" >
        Sollten Sie Fragen, Änderungswünsche, Kritik, Anregungen oder wider erwarten Probleme haben, <br>dann kontaktieren Sie uns bitte unter <a href="mailto:>service@enno.digital">service@enno.digital</a>
    </div>
{/block}
