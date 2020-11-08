<div class="col-md-12">
    <table class="table table-striped" style="margin-top: 10px">
        <tbody>
        <tr>
            <td>Путь к крону</td>
            <td>php -q {$cronPath}</td>
        </tr>
        <tr>
            <td>Интервал запуска</td>
            <td>Желательно каждую минуту</td>
        </tr>
        <tr>
            <td>Последний запуск</td>
            <td>
                {if empty($lastCronEvent)}
                    Никогда
                {else}
                    {$lastCronEvent->created_at->toDateTimeString()}
                {/if}
            </td>
        </tr>
        </tbody>
    </table>
</div>