$(function() {
    initConflictsController();
});

function initConflictsController() {
    //Do this only if page has conflicts table
    if($("table.table-conflicts")[0]) {

        /**
         * Disable save buttons
         */
        if($("button[name='update_and_edit']")) {
            $("button[name='update_and_edit']").attr('disabled', 'disabled');
        }

        if($("button[name='update_and_list']")) {
            $("button[name='update_and_list']").attr('disabled', 'disabled');
        }

        /**
         * Make fields readonly
         */
        readOnlyFormFields();


        /**
         * Version choice
         *
         * 1 - Set of value in the field and disable the version selection line
         * 2 - Check if all conflicts are resolved. If true, activate save buttons
         */
        $('button.take_version').click(function(ev) {
            var fieldId = '#'+ $(this).attr('data-id');
            var fieldValue = $(this).attr('data-value');

            if($(fieldId)) {
                if($(fieldId).is("select")) {
                    var fieldValueParts = fieldValue.split('<br>');

                    setMultiselectValue(fieldId, fieldValueParts);

                } else if($(fieldId).is(":checkbox")) {
                    setReadonlyCheckboxValue($(fieldId), fieldValue);
                } else { //text, password, integer fields
                    $(fieldId).val(fieldValue);
                }

                $(this).closest('tr').find('button').each(function(){
                    $(this).removeClass('btn-success').addClass('btn-link').attr('disabled',false);
                    $(this).children('i').removeClass('fa-check-circle').removeClass('fa-circle').addClass('fa-times-circle');
                });

                $(this).children('i').removeClass('fa-times-circle').addClass('fa-check-circle');
                $(this).removeClass('btn-link').addClass('btn-success').attr('disabled','disabled');

                $(this).closest('tr').attr('version-chosen', '1');
            }

            //check that all conflicts are treated to enable buttons
            if($("button.take_version").closest("tr:not([version-chosen='1'])").length == 0) {
                if($("button[name='update_and_edit']")) {
                    $("button[name='update_and_edit']").prop('disabled', false);
                    $("button[name='update_and_edit']").attr('name', 'update_and_edit_and_force');
                }

                if($("button[name='update_and_list']")) {
                    $("button[name='update_and_list']").prop('disabled', false);
                    $("button[name='update_and_list']").attr('name', 'update_and_list_and_force');
                }
            }
        });

        //button to take all actual data
        $("button#keep_my_version").click(function(ev){
            $('button.take_version.mine').each(function(){
                $(this).click();
            });
        });
    }
}

/**
 * Make form fields readOnly
 */
function readOnlyFormFields()
{
    // text fields
    $("form").find("input[type='text']").each(function(){
        $(this).prop('readonly', true);
    });

    //password fields
    $("form").find("input[type='password']").each(function(){
        $(this).prop('readonly', true);
    });

    // email fields
    $("form").find("input[type='email']").each(function(){
        $(this).prop('readonly', true);
    });

    // textarea fields
    $("form").find("textarea").each(function(){
        $(this).prop('readonly', true);
    });

    // multiselect fields with bootstrapMultiselect
    $("form").find("button.multiselect").each(function(){
        $(this).prop('disabled', true);
    });

    // checkbox fields with bootstrapSwitch
    $("form").find("input[type='checkbox']").each(function() {
        readonlyCheckbox(this);
    });
}

function readonlyCheckbox(checkboxSelector) {
    //for checkboxes not in multiselect => those with bootstrapSwitch
    if($(checkboxSelector).parents('ul.multiselect-container').length == 0) {
        $(checkboxSelector).bootstrapSwitch({
            size: "mini",
            onColor: "success",
            offColor: "danger",
            onText: "&nbsp;&nbsp;&nbsp;",
            offText: "&nbsp;&nbsp;&nbsp;"
        });
        $(checkboxSelector).bootstrapSwitch('readonly', true);
    }
}

function setReadonlyCheckboxValue(checkboxSelector, value) {
    //for checkboxes not in multiselect => those with bootstrapSwitch
    if($(checkboxSelector).parents('ul.multiselect-container').length == 0) {
        $(checkboxSelector).bootstrapSwitch('readonly', false);

        if(value == "OUI" || value == true || value == 'true' || value == 1) {
            $(checkboxSelector).val(1); //security set. To be sure field value is changed
            $(checkboxSelector).bootstrapSwitch('state',true);
        } else {
            $(checkboxSelector).val(0);
            $(checkboxSelector).bootstrapSwitch('state',false);
        }

        $(checkboxSelector).bootstrapSwitch('readonly', true);
    }
}

function setMultiselectValue(multiselectId, valuesArray) {
    var selectedValues = [];

    $(multiselectId + ' option').each(function(){
        for(var i=0;i<valuesArray.length;i++) {
            if(valuesArray[i] != '' && $(this).text() == valuesArray[i]) {
                selectedValues[selectedValues.length] = $(this).val();
            }
        }
    });

    /**
     * deselect all options before select chosen ones
     * doc => multiselect('deselectAll', justVisible)
     * if justVisible set to false, all options are deselected.
     * If justVisible is set to true or not provided, all visible options are deselected (?)
     */
    $(multiselectId).multiselect('deselectAll', false);
    $(multiselectId).multiselect('select', selectedValues);
}

