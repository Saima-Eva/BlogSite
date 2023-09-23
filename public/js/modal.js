function fillModalInputFields(modal_id, result){
    formReset(modal_id);

    let inputs = $(`${modal_id} .modal-body :input`);

    inputs.each(function(field){
        if(this.name in result){
            let value = result[this.name];
            if($(this).is('select') && value){
                $(this).val(value['id']);
                if ($(this).val()==null){
                    selectOption(this, value);
                }
            } else if($(this).is(':checkbox')){
                $(this).prop("checked", value)
            } else {
                $(this).val(value);
            }
        }
    })
}

function formReset(modal_id){
    $(`${modal_id} input:hidden[name='id']`).val('');
    $(`${modal_id} form`).trigger("reset");
}

function selectOption(select, value) {
    let dd = document.getElementsByName(select.name)[0];
    for (let i = 0; i < dd.options.length; i++) {
        if (dd.options[i].text === value) {
            dd.selectedIndex = i;
            break;
        }
    }
}


function openModal(elem) {
    let modal = $(modalID);
    modal.find('form')[0].reset();
    let row = elem.parentElement.parentElement.children;
    let rowInfo = {};

    for (let i=0; i<modalKeys.length; i++){
        rowInfo[modalKeys[i][0]] = row[modalKeys[i][1]].innerText;
    }
    fillModalInputFields(modalID, rowInfo);
    modal.modal("show");
}


function openModalAdd(elem) {
    let modal = $(modalID);
    modal.find('form')[0].reset();
    modal.modal("show");


}
