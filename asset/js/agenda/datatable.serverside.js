//delay from keyup event
function delay(callback, ms) {
    var timer = 0;
    return function () {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}

function datatableServerside(el, url, column, filter = []) {
    let params = [];

    let otable =
        $(el).DataTable({
            language: {
                infoFiltered: ""
            },
            'sDom': 'lrtip',
            'processing': true,
            'serverSide': true,
            'order': [],
            'destroy': true,
            'ajax': {
                'url': config.route + url,
                'type': 'POST',
                'data': function (d) {
                    if (filter.length > 0) {
                        filter.forEach(function (item, index) {
                            params[index] = { [item.param]: $(item.id).val() };
                        });
                        d.filter = params;
                    } else {
                        d.filter = filter;
                    }

                    return d;
                }
            },
            'columns': column
        });

    return otable;
}

function generateDataTable(el, url, column, filter = []) {

    let table = datatableServerside(el, url, column, filter);

    //set header search box to datatable
    $(document).on('keyup', '.datatable-searchable', delay(function (e) {
        table.search($(this).val()).draw();
    }, 500));

    filter.forEach(function (item, index) {
        $(document).on('change', item.id, function () {
            table.ajax.reload();
        });
    });

    return table;
}