$(document).ready(function(){
    var numberer = 1;
    memberTable = $('#MemberTable').on('preXhr.dt', function ( e, settings, data ){
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
                $("#MemberTable_filter input").unbind();
                $("#MemberTable_filter input").keyup( function (e) {
                    if (e.keyCode == 13) {
                        memberTable.search( this.value ).draw();
                    }
                });
            }
            document.datatable_search_change_event = true;
        }).DataTable({
            language: {
                infoFiltered: ""
            },
            serverSide: true,
            bInfo: false,
            ajax: {
                url: document.app.site_url + '/cs_team/member/get/' + cs_team.team_cs_id,
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
                { data: "username" },
                { data: "email" },
                {
                    data: 'team_cs_member_id',
                    width: "12%",
                    orderable: false,
                    render: function ( data, type, full, meta ) {
                        var button = [];

                        if(document.app.access_list.management_cs_team_member_del)
                        {
                            // hapus
                            button.push('<button onclick="delMember('+data+')" type="button" class="btn btn-danger btn-outline btn-circle btn-sm m-r-5"><i class="icon-trash"></i></button>');
                        }

                        return button.join('');
                    }
                }
            ]
        });

    document.datatable_search_cs_window = false;

    var numbererCs = 1;
    lisCsTable = $('#lisCsTable').on('preXhr.dt', function ( e, settings, data ){
            numbererCs = data.start + 1;
            $('#lisCsTable_wrapper').block({
                message: '<h3>Please Wait...</h3>',
                css: {
                    border: '1px solid #fff'
                }
            });
        }).on('xhr.dt', function ( e, settings, json, xhr ){
            $('#lisCsTable_wrapper').unblock();
            if(!document.datatable_search_cs_window)
            {
                $("#lisCsTable_filter input").unbind();
                $("#lisCsTable_filter input").keyup( function (e) {
                    if (e.keyCode == 13) {
                        lisCsTable.search( this.value ).draw();
                    }
                });
            }
            document.datatable_search_cs_window = true;
        }).DataTable({
            language: {
                infoFiltered: ""
            },
            serverSide: true,
            bInfo: false,
            ajax: {
                url: document.app.site_url + '/cs_team/get/cs',
                type: 'POST'
            },
            columns: [
                {
                    name: 'Number',
                    width: "5%",
                    orderable: false,
                    render: function ( data, type, full, meta ) {
                        return numbererCs++;
                    }
                },
                { data: "full_name" },
                { data: "email" },
                {
                    data: 'user_id',
                    width: "12%",
                    orderable: false,
                    render: function ( data, type, full, meta ) {
                        var button = [];
                        if(typeof full.team_cs_id == 'object')
                        {
                            button.push('<button onclick="addMemberToTeam('+data+')" type="button" class="btn btn-success btn-outline btn-circle btn-sm m-r-5"><i class="ti-plus"></i></button>');
                        }

                        return button.join('');
                    }
                }
            ]
        });


    $('#memberModal').on('shown.bs.modal', function () {
      initMemberTim();
    })
});

document.app._leader_tim_init = false;

function initMemberTim(){
    if(!document.app._leader_tim_init){
        document.app._leader_tim_init = true;
        $('#memberSelect').select2({
            ajax: {
                url: document.app.module_url.sso+'/user/get',
                method: 'POST',
                data: function (params) {
                    var query = {
                        search: {
                            value: params.term,
                            regex: false
                        },
                        from: 'management_cs_team_member',
                        type: 'public'
                    }
                    return query;
                },
                processResults: function (data) {
                    var res = [];
                    data.data.forEach(function(val, key){
                        res.push({
                            id: val.user_id,
                            text: val.username
                        });
                    });
                    return {
                        results: res
                    };
                }
            }
        });
    }
}

function addMember(){
    lisCsTable.ajax.reload();
    $('#memberModal').modal({
        backdrop: 'static',
        keyboard: false
    });
}

function addMemberToTeam(user_id){
    $('.preloader').fadeIn();
    $.ajax({
        method: "POST",
        url: document.app.site_url+'/cs_team/member/add',
        data: {
            team_cs_id: $('#fieldTeamCsId').val(),
            user_id: user_id
        }
    })
    .done(function( response ) {
        $('.preloader').fadeOut();
        var title = 'Berhasil!',
            timer = 1000;
            showConfirmButton = false;

        if(!response.status) {
            var timer = 3000;
            title = 'Gagal!';
            showConfirmButton = true;
        } else {
            memberTable.ajax.reload()
            lisCsTable.ajax.reload()
        }

        swal({
            title: title,
            text: response.message,
            timer: timer,
            showConfirmButton: showConfirmButton
        });
    });
}

function delMember(id){
    swal({
        title: "Are you sure?",
        text: "Anda akan menghapus member ini!",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
        closeOnConfirm: false,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {
            $('.preloader').fadeIn();
            $.ajax({
                method: "POST",
                url: document.app.site_url+'/cs_team/member/del/'+id
            })
            .done(function( response ) {
                $('.preloader').fadeOut();
                memberTable.ajax.reload()
                var title = 'Berhasil!';
                if(!response.status) title = 'Gagal!';

                swal({
                    title: title,
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: true
                });
            });
        }
    });
}
