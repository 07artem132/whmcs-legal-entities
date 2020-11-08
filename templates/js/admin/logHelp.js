window.onload = function () {
    const table3 = jQuery("#tableLogsList").DataTable({
        "ordering": true,
        "dom": '<"listtable"fit>pl',
        "responsive": true,
        "oLanguage": {
            "sEmptyTable": "Записей не найдено",
            "sInfo": "Показано с _START_ по _END_ из _TOTAL_",
            "sInfoEmpty": "Показано с 0 по 0 из 0",
            "sInfoFiltered": "(отфильтровано из _MAX_ записей)",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "Показать _MENU_ записей",
            "sLoadingRecords": "Загрузка...",
            "sProcessing": "Обработка...",
            "sSearch": "",
            "sZeroRecords": "Записей не найдено",
            "oPaginate": {
                "sFirst": "Первая",
                "sLast": "Последняя",
                "sNext": "Вперед",
                "sPrevious": "Назад"
            }
        },
        "pageLength": 100,
        "lengthMenu": [
            [10, 50, 100, 500, -1],
            [10, 50, 100, 500, "Все"]
        ],
        "columnDefs": [
            {
                "targets": 0,
                "orderable": false
            },
        ],
        "stateSave": true
    });
    jQuery(".dataTables_filter input").attr("placeholder", "Условие для поиска...");
};