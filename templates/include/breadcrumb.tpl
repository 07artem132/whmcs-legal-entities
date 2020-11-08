<ol class="breadcrumb">
    {foreach from=$breadcrumb key=$title item=$url}
        {if $url==''}
            <li style="color: #0e5077;">{$title}</li>
        {else}
            <li><a href="{$url}" style="color: #888;">{$title}</a></li>
        {/if}
    {/foreach}
</ol>