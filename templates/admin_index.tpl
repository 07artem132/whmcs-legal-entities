<div class="row pull-right" style="margin-right: 4px;">
    <div class="col-md-3">
        <div class="text-right">
            <!-- Split button -->
            <div class="btn-group">
                <form role="form" method="get" style="display: inline;">
                    <input type="hidden" name="module" value="LegalEntities">
                    <input type="hidden" name="action" value="add_doc">
                    <input type="submit" class="btn btn-success btn-sm" value="Добавить документ">
                </form>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div id="tableBackground" class="tablebg">
        <table id="tableDocsList" width="100%" class="datatable no-margin">
            <thead>
            <tr>
                <th>
                    id
                </th>
                <th>
                    Клиент
                </th>
                <th>
                    услуга
                </th>
                <th>
                    имя документа
                </th>
                <th>
                    тип
                </th>
                <th>
                    Добавлен
                </th>
                <th>
                    Изменен
                </th>
                <th style="width: 2%;"></th>
                <th style="width: 2%;"></th>
                <th style="width: 2%;"></th>
                <th style="width: 2%;"></th>
                <th style="width: 2%;"></th>
            </tr>
            </thead>
            <tbody>
            {foreach $docList as $doc}
                <tr class="product">
                    <td>
                        {$doc.id}
                    </td>
                    <td>
                        <a href="clientssummary.php?userid={$doc.client_id}">{$doc.client_name}({$doc.client_company}
                            )</a>
                    </td>
                    <td>
                        {if !empty($doc.service_url)}
                            <a href="{$doc.service_url}">{$doc.product_name}</a>
                        {else}
                            {$doc.product_name}
                        {/if}
                    </td>
                    <td>
                        {$doc.name}
                    </td>
                    <td>
                        {$doc.type}
                    </td>
                    <td>
                        {$doc.created_at}
                    </td>
                    <td>
                        {$doc.updated_at}
                    </td>
                    <td>
                        <a href="addonmodules.php?module=LegalEntities&action=edit&id={$doc.id}"
                           title="Редактирование документа">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                    <td>
                        {if $doc.send_mail eq true}
                            <a href="addonmodules.php?module=LegalEntities&action=edit&id={$doc.id}&mail=0"
                               title="Изменить статус 'Отправлен по почте'">
                                <i class="fas fa-envelope" style="color: green"></i>
                            </a>
                        {else}
                            <a href="addonmodules.php?module=LegalEntities&action=edit&id={$doc.id}&mail=1"
                               title="Изменить статус 'Отправлен по почте'">
                                <i class="fas fa-envelope" style="color: #b7bab7;"></i>
                            </a>
                        {/if}
                    </td>
                    <td>
                        {if $doc.send_edf eq true}
                            <a href="addonmodules.php?module=LegalEntities&action=edit&id={$doc.id}&edf=0"
                               title="Изменить статус 'Отправлен через ЭДО'">
                                <i class="fas fa-share-alt" style="color: green"></i>
                            </a>
                        {else}
                            <a href="addonmodules.php?module=LegalEntities&action=edit&id={$doc.id}&edf=1"
                               title="Изменить статус 'Отправлен через ЭДО'">
                                <i class="fas fa-share-alt" style="color: #b7bab7;"></i>
                            </a>
                        {/if}
                    </td>
                    <td>
                        {if $doc.file neq ''}
                        <a href="addonmodules.php?module=LegalEntities&action=download&id={$doc.id}"
                           title="Скачать документ">
                            <i class="fas fa-download"></i>
                        </a>
                        {else}
                            <a href="addonmodules.php?module=LegalEntities&action=download&id={$doc.id}"
                               title="Скачать документ">
                                <i class="fas fa-download" style="color: #b7bab7"></i>
                            </a>
                        {/if}
                    </td>
                    <td>
                        <a href="addonmodules.php?module=LegalEntities&action=delete&id={$doc.id}"
                           title="Удалить домен"
                           onClick="return window.confirm('Вы точно хотите удалить документ {$doc.name} ?');"
                        >
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>


