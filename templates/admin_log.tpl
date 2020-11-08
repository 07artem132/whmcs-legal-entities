<div class="col-md-12">
    <div id="tableBackground" class="tablebg">
        <table id="tableLogsList" width="100%" class="datatable no-margin">
            <thead>
            <tr>
                <th style="width: 2%;"></th>
                <th>
                    Модуль
                </th>
                <th>
                    сообщение
                </th>
                <th>
                    дата события
                </th>
            </tr>
            </thead>
            <tbody>
            {foreach $logs as $log}
                <tr class="product">
                    <td>
                        {if $log.status eq 1}
                            <i class="fa fa-check" style="color:green"></i>
                        {else}
                            <i class="fa fa-times" style="color:red"></i>
                        {/if}
                    </td>
                    <td>
                        {$log.module}
                    </td>
                    <td style="word-break: break-all">
                        {$log.message}
                    </td>
                    <td>
                        {$log->created_at}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>


