<form class="form-horizontal" enctype="multipart/form-data" method="post">
    {foreach from=$configField item=$config key=$key}
        <h3>{$key}</h3>
        <div class='row'>
            <div class='col-md-12 '>
                <fieldset>
                    {foreach from=$config item=$item}
                        <div class="form-group">
                            <label for="{$item.name}" class="col-md-3 control-label">
                                {$item.label}
                                {if $item.required eq true}
                                    <span style="color: #ff00008f">*</span>
                                {/if}
                            </label>
                            <div class="col-sm-4">
                                {if $item.type eq 'select'}
                                    <select

                                            id="{$item.name}"
                                            class="{$item.class}"
                                            {if $item.allowMultiple eq true}
                                                name='{$item.name}[]'
                                                multiple
                                            {else}
                                                name='{$item.name}'
                                            {/if}
                                            {if $item.required eq true}
                                    required
                                            {/if}>
                                        {foreach from=$item.selectDisable item=$option }
                                            <option
                                                    disabled
                                                    value=""
                                                    {if $option.value|in_array:$item.selected}
                                                        selected
                                                    {/if}>
                                                {$option.text}
                                            </option>
                                        {/foreach}
                                        {foreach from=$item.selectAllow item=$option }
                                            <option
                                                    {if $option.value|in_array:$item.selected}
                                                        selected
                                                    {/if}
                                                    value="{$option.value}">{$option.text}</option>
                                        {/foreach}
                                    </select>
                                {elseif $item.type eq 'checkbox'}
                                    <input type="hidden"
                                           class="{$item.class}"
                                           name='{$item.name}'
                                           id="{$item.name}"
                                           value='0'
                                    >
                                    <input type="{$item.type}"
                                           class="{$item.class}"
                                           style="margin: 10px 0 0 0;"
                                           name='{$item.name}'
                                           id="{$item.name}"
                                           value='1'
                                            {if $item.required eq true}
                                                required
                                            {/if}
                                            {if $item.val eq 1}
                                                checked
                                            {/if}
                                    >
                                {elseif $item.type eq 'file'}
                                    {if $item.isLoaded eq true}
                                        Файл уже был загружен (
                                        <a href="{$item.fileUrl}">Просмотреть</a>
                                        )
                                        <input type="{$item.type}"
                                               class="{$item.class}"
                                               name='{$item.name}'
                                               id="{$item.name}"
                                        >
                                    {else}
                                        <input type="{$item.type}"
                                               class="{$item.class}"
                                               name='{$item.name}'
                                               id="{$item.name}"
                                               value='{$item.val}'
                                                {if $item.required eq true}
                                                    required
                                                {/if}
                                        >
                                    {/if}
                                {else}
                                    <input type="{$item.type}"
                                           class="{$item.class}"
                                           name='{$item.name}'
                                           id="{$item.name}"
                                           value='{$item.val}'
                                            {if $item.required eq true}
                                                required
                                            {/if}
                                    >
                                {/if}
                            </div>
                            <div class="col-sm-4">
                                <label class="info_text" for="{$item.name}">{$item.description}</label>
                            </div>
                        </div>
                    {/foreach}
                </fieldset>
            </div>
        </div>
        <hr/>
    {/foreach}
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-8">
            <button type="submit" class="btn btn-primary center-block">
                <i class='fa fa-floppy-o'></i>
                Сохранить
            </button>
        </div>
    </div>
</form>