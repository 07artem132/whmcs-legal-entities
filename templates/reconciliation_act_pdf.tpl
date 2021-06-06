<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        * {
            font-family: arial;
            font-size: 14px;
            line-height: 14px;
        }
        table {
            margin: 0 0 15px 0;
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }
        table td {
            padding: 5px;
        }
        table th {
            padding: 5px;
            font-weight: bold;
        }

        .header {
            margin: 0 0 0 0;
            padding: 0 0 15px 0;
            font-size: 12px;
            line-height: 12px;
            text-align: center;
        }

        /* Реквизиты банка */
        .details td {
            padding: 3px 2px;
            border: 1px solid #000000;
            font-size: 12px;
            line-height: 12px;
            vertical-align: top;
        }

        h1 {
            margin: 0 0 10px 0;
            padding: 10px 0 10px 0;
            border-bottom: 2px solid #000;
            font-weight: bold;
            font-size: 20px;
        }

        /* Поставщик/Покупатель */
        .contract th {
            padding: 3px 0;
            vertical-align: top;
            text-align: left;
            font-size: 13px;
            line-height: 15px;
        }
        .contract td {
            padding: 3px 0;
        }

        /* Наименование товара, работ, услуг */
        .list thead, .list tbody  {
            border: 2px solid #000;
        }
        .list thead th {
            padding: 4px 0;
            border: 1px solid #000;
            vertical-align: middle;
            text-align: center;
        }
        .list tbody td {
            padding: 0 2px;
            border: 1px solid #000;
            vertical-align: middle;
            font-size: 11px;
            line-height: 13px;
        }
        .list tfoot th {
            padding: 3px 2px;
            border: none;
            text-align: right;
        }

        /* Сумма */
        .total {
            margin: 0 0 10px 0;
            padding: 0 0 10px 0;
            border-bottom: 2px solid #000;
        }
        .total p {
            margin: 0;
            padding: 0;
        }

        /* Руководитель, бухгалтер */
        .sign {
            position: relative;
        }
        .sign table {
            width: 80%;
        }
        .sign th {
            padding: 10px 0 0 0;
            text-align: left;
        }
        .sign td {
            padding: 20px 0 0 0;
            border-bottom: 0px solid #000;
            text-align: right;
            font-size: 12px;
        }

        .sign-1 {
            /*position: absolute;
           left: 300px;
           top: 50px;*/
           width: 70px;
           z-index: 2;
       }
       .sign-2 {
           /*position: absolute;
           left: 300px;
           top: 9px;*/
            width: 70px;
            z-index: 2;
        }
        .printing {
            position: absolute;
            left: 120px;
            top: 10px;
            width: 150px;
            z-index: 0;
        }

        .printing2{
            position: absolute;
            left: -20px;
            top: 50px;
            width: 150px;
            z-index: 0;
        }
    </style>
</head>
<body>
<h1>Акт № {$invoiceID} от {$invoicePaidDate}</h1>

<table class="contract">
    <tbody>
    <tr>
        <td width="15%">Исполнитель:</td>
        <th width="85%">
            {$provider}
        </th>
    </tr>
    <tr>
        <td>Заказчик:</td>
        <th>
            {$customer}
        </th>
    </tr>
    <tr>
        <td>Основание:</td>
        <th>
            Договор-оферты № {$client_id} от {$create_date}
        </th>
    </tr>
    </tbody>
</table>

<table class="list">
    <thead>
    <tr>
        <th width="5%">№</th>
        <th width="54%">Наименование товара, работ, услуг</th>
        <th width="8%">Кол-во</th>
        <th width="5%">Ед.</th>
        <th width="14%">Цена</th>
        <th width="14%">Сумма</th>
    </tr>
    </thead>
    <tbody>


    {foreach from=$items item=$item name='itemsIter'}
    <tr>
        <td align="center">{$smarty.foreach.itemsIter.iteration }</td>
        <td align="left">{$item['name']}</td>
        <td align="center">{$item['count']}</td>
        <td align="center">{$item['unit']}</td>
        <td align="right">{$item['price']}</td>
        <td align="right">{$item['price_total']}</td>
    </tr>
    {/foreach}


    </tbody>
    <tfoot>
    <tr>
        <th colspan="5">Итого:</th>
        <th>{$total}</th>
    </tr>
    <tr>
        <th colspan="5">В т.ч. НДС{if $nds_raw eq 0}(Без НДС){/if}:</th>
        <th>{if $nds_raw eq 0}-{else}{$nds}{/if}</th>
    </tr>
    <tr>
        <th colspan="5">Всего к оплате:</th>
        <th>{$total}</th>
    </tr>

    </tfoot>
</table>

<div class="total">
    <p>Всего наименований {$count}, на сумму {$total} руб.</p>
    <p><strong>{$stringTotal}</strong></p><br/>
    <p>Вышеперечисленные услуги выполнены полностью и в срок. Заказчик претензий по объему, качеству и
        срокам оказания услуг не имеет</p>
</div>
<div class="sign">
    <img class="printing" src="{$printing}">
    <img class="printing2" src="{$sign1}">
    <table style="z-index: 999999">
        <tbody>
        <tr>
            <th style="width: 350px">ИСПОЛНИТЕЛЬ</th>
            <th style="width: 350px">ЗАКАЗЧИК</th>
        </tr>

        </tbody>
        <tr>
            <th style="width: 350px;font-weight: normal">Генеральный директор ООО "ЗТВ КОРП"</th>
            <th style="width: 350px;font-weight: normal">{$client_companyname}</th>
        </tr>
        <tr>
            <th style="height: 10px "> </th>
            <th style="height: 10px">    </th>
        </tr>
        <tr>
            <th style="">__________________________________</th>
            <th style="">__________________________________</th>
        </tr>
        <tr>
            <th style="text-align: center;"><span style="margin-left: -80px;font-weight: normal">Третьякова В. Н</span></th>
            <th style=""></th>
        </tr>
    </table>
</div>
</body>
</html>