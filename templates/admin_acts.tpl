<div class="col-md-12">
    <div id="tableBackground" class="tablebg">
        <table id="tableActsList" width="100%" class="datatable no-margin">
            <thead>
            <tr>
                <th>
                    id
                </th>
                <th>
                    Клиент
                </th>
                <th>
                    Сумма
                </th>
                <th>
                    Создан
                </th>
                <th style="width: 2%;"></th>
                <th style="width: 2%;"></th>
                <th style="width: 2%;"></th>
            </tr>
            </thead>
            <tbody>
            {foreach $docList as $doc}
                <tr class="product">
                    <td>
                       <a href="invoices.php?action=edit&id={$doc.id}">{$doc.id}</a>
                    </td>
                    <td>
                        <a href="clientssummary.php?userid={$doc.client.id}">{$doc.client.firstname} {$doc.client.lastname}({$doc.client.companyname})</a>
                    </td>
                    <td>
                        {$doc.subtotal}
                    </td>
                    <td>
                        {$doc.datepaid}
                    </td>
                    <td>
                        {if $doc.send_mail eq true}
                            <a href="addonmodules.php?module=LegalEntities&action=edit&id={$doc.id}&mail=0&type=act"
                               title="Изменить статус 'Отправлен по почте'">
                                <i class="fas fa-envelope" style="color: green"></i>
                            </a>
                        {else}
                            <a href="addonmodules.php?module=LegalEntities&action=edit&id={$doc.id}&mail=1&type=act"
                               title="Изменить статус 'Отправлен по почте'">
                                <i class="fas fa-envelope" style="color: #b7bab7;"></i>
                            </a>
                        {/if}
                    </td>
                    <td>
                        {if $doc.send_edf eq true}
                            <a href="addonmodules.php?module=LegalEntities&action=edit&id={$doc.id}&edf=0&type=act"
                               title="Изменить статус 'Отправлен через ЭДО'">
                                <i class="fas fa-share-alt" style="color: green"></i>
                            </a>
                        {else}
                            <a href="addonmodules.php?module=LegalEntities&action=edit&id={$doc.id}&edf=1&type=act"
                               title="Изменить статус 'Отправлен через ЭДО'">
                                <i class="fas fa-share-alt" style="color: #b7bab7;"></i>
                            </a>
                        {/if}
                    </td>
                    <td>
                        <a href="addonmodules.php?module=LegalEntities&action=download&id={$doc.id}&type=act"
                           title="Скачать документ">
                            <i class="fas fa-download"></i>
                        </a>

                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>


