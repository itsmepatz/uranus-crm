$(document).ready(function(){

    var numberer = 1;
    ordersTable = $('#ordersTable').on('preXhr.dt', function ( e, settings, data ){
            numberer = data.start + 1;
            $('.row .white-box').block({
                message: '<h3>Please Wait...</h3>',
                css: {
                    border: '1px solid #fff'
                }
            });
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
            columns: [
                {
                    name: 'Number',
                    width: "5%",
                    orderable: false,
                    render: function ( data, type, full, meta ) {
                        return numberer++;
                    }
                },
                { data: "created_at", orderable: false},
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
                        return button.join('');
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