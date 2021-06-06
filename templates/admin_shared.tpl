<div class="row pull-right" style="margin-right: 4px;">
    <div class="col-md-3">
        <div class="text-right">
            <!-- Split button -->
            <div class="btn-group">
                <form role="form" method="get" style="display: inline;">
                    <input type="hidden" name="module" value="LegalEntities">
                    <input type="hidden" name="action" value="add_doc_shared">
                    <input type="submit" class="btn btn-success btn-sm" value="Добавить документ">
                </form>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div id="tableBackground" class="tablebg">
        <table id="tableDocsSharedList" width="100%" class="datatable no-margin">
            <thead>
            <tr>
                <th>
                    id
                </th>

                <th>
                    имя документа
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
            </tr>
            </thead>
            <tbody>
            {foreach $docList as $doc}
                <tr class="product">
                    <td>
                        {$doc.id}
                    </td>
                    <td>
                        {$doc.name}
                    </td>
                    <td>
                        {$doc.created_at}
                    </td>
                    <td>
                        {$doc.updated_at}
                    </td>
                    <td>
                        <a href="addonmodules.php?module=LegalEntities&action=edit&id={$doc.id}&type=shared"
                           title="Редактирование документа">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>


                    <td>
                        {if $doc.file neq ''}
                        <a href="addonmodules.php?module=LegalEntities&action=download&id={$doc.id}&type=shared"
                           title="Скачать документ">
                            <i class="fas fa-download"></i>
                        </a>
                        {else}
                            <a href="addonmodules.php?module=LegalEntities&action=download&id={$doc.id}&type=shared"
                               title="Скачать документ" disabled="">
                                <i class="fas fa-download" style="color: #b7bab7"></i>
                            </a>
                        {/if}
                    </td>
                    <td>
                        <a href="addonmodules.php?module=LegalEntities&action=delete&id={$doc.id}&type=shared"
                           title="Удалить документ"
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


