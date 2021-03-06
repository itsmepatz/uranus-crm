$(document).ready(function(){
    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'yyyy-mm-dd'
    });

    var numberer = 1;
    ordersTable = $('#ordersTable').on('preXhr.dt', function ( e, settings, data ){
            numberer = data.start + 1;
            $('.row .white-box').block({
                message: '<h3>Please Wait...</h3>',
                css: {
                    border: '1px solid #fff'
                }
            });

            data.date_start = $('#date-range [name=start]').val();
            data.date_end = $('#date-range [name=end]').val();
            data.filter_cs_id = $('#filterSection [name=filter_cs_id]').val();

        }).on('xhr.dt', function ( e, settings, json, xhr ){
            $('.row .white-box').unblock();
            if(!document.datatable_search_change_event)
            {
                $("div.dataTables_filter input").unbind();
                $("div.dataTables_filter input").keyup( function (e) {
                    if (e.keyCode == 13) {
                        ordersTable.search( this.value ).draw();
                    }
                });
            }
            document.datatable_search_change_event = true;
        }).DataTable({
            serverSide: true,
            ajax: {
                url: document.app.site_url + '/packing_v1/get/index/shipping',
                type: 'POST'
            },
            language: {
                infoFiltered: ""
            },
            columns: [
                {
                    name: 'Number',
                    width: "5%",
                    orderable: false,
                    render: function ( data, type, full, meta ) {
                        return numberer++;
                    }
                },
                {
                    data: "created_at", orderable: false,
                    render: function ( data, type, full, meta ) {
                        var data = data.split(' ');
                        return data[0];
                    }
                },
                {
                    data: "sale_date", orderable: false,
                    render: function ( data, type, full, meta ) {
                        var data = data.split(' ');
                        return data[0];
                    }
                },
                { data: "order_code", orderable: false},
                {
                    data: 'customer_info',
                    orderable: false,
                    render: function ( data, type, full, meta ) {
                        var data = JSON.parse(data);
                        return data.full_name;
                    }
                },
                { data: "package_name", orderable: false },
                {
                    data: 'order_id',
                    orderable: false,
                    render: function ( data, type, full, meta ) {
                        var button = [];
                        button.push(`<span class="label label-warning label-rouded">${full.logistic_name}</span>`)
                        button.push(`<span class="label label-info label-rouded">${full.username}</span>`)
                        return button.join('<br>');
                    }
                },
                {
                    data: 'order_id',
                    orderable: false,
                    render: function ( data, type, full, meta ) {
                        var button = [];
                        //
                        if(document.app.access_list.logistik_orders_detail)
                        {
                            button.push(`<a href="${document.app.site_url}/packing_v1/detail/index/${data}" type="button" class="btn btn-primary btn-outline btn-circle btn-sm m-r-5"><i class="fa fa-eye"></i></a>`);
                        }
                        return button.join('');
                    }
                }
            ]
        });
});
