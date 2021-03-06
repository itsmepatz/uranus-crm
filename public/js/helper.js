function precisionRound(number, precision) {
    if(!precision) precision = 0;
    var factor = Math.pow(10, precision);
    return Math.round(number * factor) / factor;
}

function rupiah(angka)
{
	var rupiah = '';
    var angka = precisionRound(angka);
	var angkarev = angka.toString().split('').reverse().join('');
	for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
	return 'Rp. '+rupiah.split('',rupiah.length-1).reverse().join('')+',-';
}

function ucwords(str){
    str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
        return letter.toUpperCase();
    });
    return str;
}

function serialzeForm(selector){
    var formArray = $(selector).serializeArray(),
        dataForm = {};

    formArray.forEach(function(val, index){
        dataForm[val.name] = val.value;
    })
    return dataForm;
}

function formValidator(selector){
    var form = $(selector);
    form.validator('validate');
    var hasErr = form.find(".has-error").length;
    return (hasErr == 0);
}

function formPopulate(selector, data) {
    var form = $(selector);
    $.each(data, function(key, value) {
        var ctrl = $('[name='+key+']', form);
        switch(ctrl.prop("type")) {
            case "radio": case "checkbox":
                var check_val = (ctrl.prop('value') == value);
                if(ctrl.prop("class") == 'js-switch' && check_val != ctrl.is(':checked')){
                    $(ctrl).parent().find('.switchery').trigger('click');
                } else {
                    ctrl.each(function() {
                        if($(this).attr('value') == value) $(this).prop("checked", true);
                    });
                }
                break;

            case 'text':
                if(ctrl.attr('data-role') == 'tagsinput'){
                    ctrl.tagsinput('removeAll');
                    var str = value.split(',');
                    str.forEach(function(val, key){
                        ctrl.tagsinput('add', val);
                    });
                } else {
                    ctrl.val(value);
                }
                break;
            default:
                ctrl.val(value);
        }
        ctrl.trigger('change');
    });
}

$(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});
