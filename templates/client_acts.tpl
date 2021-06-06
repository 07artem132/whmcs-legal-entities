<link rel="stylesheet" type="text/css" href="{$BASE_PATH_CSS}/dataTables.bootstrap.css">
<link rel="stylesheet" type="text/css" href="{$BASE_PATH_CSS}/dataTables.responsive.css">
<script type="text/javascript" charset="utf8" src="{$BASE_PATH_JS}/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="{$BASE_PATH_JS}/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" charset="utf8" src="{$BASE_PATH_JS}/dataTables.responsive.min.js"></script>
<script type="text/javascript">
    var alreadyReady = false; // The ready function is being called twice on page load.
    jQuery(document).ready(function () {ldelim}
        var table = jQuery("#tableInvoicesList").DataTable({ldelim}
            "dom": '<"listtable"fit>pl',{if isset($noPagination) && $noPagination}
            "paging": false,{/if}{if isset($noInfo) && $noInfo}
            "info": false,{/if}{if isset($noSearch) && $noSearch}
            "filter": false,{/if}{if isset($noOrdering) && $noOrdering}
            "ordering": false,{/if}
            "responsive": true,
            "oLanguage": {ldelim}
                "sEmptyTable": "{$LANG.norecordsfound}",
                "sInfo": "{$LANG.tableshowing}",
                "sInfoEmpty": "{$LANG.tableempty}",
                "sInfoFiltered": "{$LANG.tablefiltered}",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "{$LANG.tablelength}",
                "sLoadingRecords": "{$LANG.tableloading}",
                "sProcessing": "{$LANG.tableprocessing}",
                "sSearch": "",
                "sZeroRecords": "{$LANG.norecordsfound}",
                "oPaginate": {ldelim}
                    "sFirst": "{$LANG.tablepagesfirst}",
                    "sLast": "{$LANG.tablepageslast}",
                    "sNext": "{$LANG.tablepagesnext}",
                    "sPrevious": "{$LANG.tablepagesprevious}"
                    {rdelim}
                {rdelim},
            "pageLength": 50,
            "order": [
                [{if isset($startOrderCol) && $startOrderCol}{$startOrderCol}{else}0{/if}, "asc"]
            ],
            "lengthMenu": [
                [50, 100, 200, -1],
                [50, 100, 200, "{$LANG.tableviewall}"]
            ],
            "aoColumnDefs": [
                {
                    "targets": 3,
                    "orderable": false
                },
                {
                    "targets": 4,
                    "orderable": false
                },
                {
                    "targets": 5,
                    "orderable": false
                },
            ],
            "stateSave": true
            {rdelim});

        {if isset($filterColumn) && $filterColumn}
        // highlight remembered filter on page re-load
        var rememberedFilterTerm = table.state().columns[{$filterColumn}].search.search;
        if (rememberedFilterTerm && !alreadyReady) {
            // This should only run on the first "ready" event.
            jQuery(".view-filter-btns a span").each(function (index) {
                if (buildFilterRegex(jQuery(this).text().trim()) == rememberedFilterTerm) {
                    jQuery(this).parent('a').addClass('active');
                    jQuery(this).parent('a').find('i').removeClass('fa-circle').addClass('fa-dot-circle');
                }
            });
        }
        {/if}
        alreadyReady = true;
        {rdelim});
</script>
<script type="text/javascript">
    jQuery(document).ready(function () {
        var table = jQuery('#tableInvoicesList').removeClass('hidden').DataTable();
        table.draw();
        jQuery('#tableLoading').addClass('hidden');
    });
</script>


<div class="table-container clearfix">
    <table id="tableInvoicesList" class="table table-list hidden">
        <thead>
        <tr>
            <th>#</th>
            <th>Дата создания акта</th>
            <th>Сумма</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        {foreach key=num item=doc from=$docList}
            <tr>
                <td>{$doc.id}</td>
                <td>{$doc.datepaid}</td>
                <td>{$doc.total}</td>
                <td>
                    {if $doc.send_mail eq true}
                        <a href="#"
                           title="Документ  отправлен по почте">
                            <i class="fas fa-envelope" style="color: green"></i>
                        </a>
                    {else}
                        <a href="/?m=LegalEntities&rqs=page&id={$doc.down_id}&tps=почта"
                           title="Документ не отправлен по почте,кликните для запроса отправки.">
                            <i class="fas fa-envelope" style="color: #b7bab7;"></i>
                        </a>
                    {/if}
                </td>
                <td>
                    {if $doc.send_edf eq true}
                        <a href="#"
                           title="Документ  отправлен через ЭДО">
                            <i class="fas fa-share-alt" style="color: green"></i>
                        </a>
                    {else}
                        <a href="/?m=LegalEntities&rqs=page&id={$doc.down_id}&tps=ЭДО"
                           title="Документ не отправлен через ЭДО,кликните для запроса отправки.">
                            <i class="fas fa-share-alt" style="color: #b7bab7;"></i>
                        </a>
                    {/if}
                </td>
                <td>
                        <a href="/?m=LegalEntities&fid={$doc.down_id}&type=act"
                           title="Скачать документ">
                            <i class="fas fa-download"></i>
                        </a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    <div class="text-center" id="tableLoading">
        <p><i class="fas fa-spinner fa-spin"></i> {$LANG.loading}</p>
    </div>
</div>