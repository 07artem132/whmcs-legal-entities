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
            width: 70%;
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
            position: absolute;
            left: 300px;
            top: 50px;
            width: 70px;
            z-index: 2;
        }
        .sign-2 {
            position: absolute;
            left: 300px;
            top: 9px;
            width: 70px;
            z-index: 2;
        }
        .printing {
            position: absolute;
            left: 130px;
            top: 0;
            width: 150px;
            z-index: 1;
        }
    </style>
</head>
<body>
<p class="header">
    {$headerVar}
</p>

<table class="details">
    <tbody>
    <tr>
        <td colspan="2" style="border-bottom: none;">{$payeesBank}</td>
        <td>БИК</td>
        <td style="border-bottom: none;">{$bik}</td>
    </tr>
    <tr>
        <td colspan="2" style="border-top: none; font-size: 10px;">Банк получателя</td>
        <td>Сч. №</td>
        <td style="border-top: none;">{$accountNumber1}</td>
    </tr>
    <tr>
        <td width="25%">ИНН {$inn}</td>
        <td width="30%">КПП {$kpp}</td>
        <td width="10%" rowspan="3">Сч. №</td>
        <td width="35%" rowspan="3">{$accountNumber2}</td>
    </tr>
    <tr>
        <td colspan="2" style="border-bottom: none;">{$reciver}</td>
    </tr>
    <tr>
        <td colspan="2" style="border-top: none; font-size: 10px;">Получатель</td>
    </tr>

    </tbody>
</table>
<div style="text-align: center">{$midleVar}</div>
<h1>Счет на оплату № {$invoiceID} от {$invoiceDate}</h1>

<table class="contract">
    <tbody>
    <tr>
        <td width="15%">Поставщик:</td>
        <th width="85%">
            {$provider}
        </th>
    </tr>
    <tr>
        <td>Покупатель:</td>
        <th>
            {$customer}
        </th>
    </tr>
    </tbody>
</table>

<table class="list">
    <thead>
    <tr>
        <th width="5%">№</th>
        <th width="68%">Наименование товара, работ, услуг</th>
        <th width="8%">Коли-<br>чество</th>
        <th width="5%">Ед.<br>изм.</th>
        <th width="14%">Сумма</th>
    </tr>
    </thead>
    <tbody>


    {foreach from=$items item=$item name='itemsIter'}
    <tr>
        <td align="center">{$smarty.foreach.itemsIter.iteration }</td>
        <td align="left">{$item['name']}</td>
        <td align="right">{$item['count']}</td>
        <td align="left">{$item['unit']}</td>
        <td align="right">{$item['price']}</td>
    </tr>
    {/foreach}


    </tbody>
    <tfoot>
    <tr>
        <th colspan="4">Итого:</th>
        <th>{$total}</th>
    </tr>
    <tr>
        <th colspan="4">В том числе НДС:</th>
        <th>{if $nds eq ''}Не облагается{else}{$nds}{/if}</th>
    </tr>
    <tr>
        <th colspan="4">Всего к оплате:</th>
        <th>{$total}</th>
    </tr>

    </tfoot>
</table>

<div class="total">
    <p>Всего наименований {$count}, на сумму {$total} руб.</p>
    <p><strong>{$stringTotal}</strong></p>
</div>
<div>{$footerVar}</div>
<div class="sign">
    <img class="sign-1" src="{$sign1}">
    <img class="sign-2" src="{$sign2}">
    <img class="printing" src="{$printing}">
    <table>
        <tbody>
        <tr>
            <th width="30%">Руководитель</th>
            <td width="70%">{$Leader}</td>
        </tr>
        <tr>
            <th>Бухгалтер</th>
            <td>{$bookkeeper}</td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>