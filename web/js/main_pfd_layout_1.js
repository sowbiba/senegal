;(function ($) {
    "use strict";

    $(document).ready(function () {
        initCheckboxSwitch();
        initConfirm();
        initDatepicker();
        initMultiselect();
        initPagination();
        initSubmit();
        initToggleFilter();
        initConfirmForm();
        initPreventLosingFormData();
        initCheckboxesWithSelectAction();
        initFieldsEmpty();
        initDisableAfterClick();
        initNumberChooser();
        initTypePagesCollection();
    });
})(jQuery);

    function initTypePagesCollection()
    {
        // Récupère le div qui contient la collection de modèle de pages
        var collectionHolder = $('ul.type-pages');

        // ajoute un lien de suppression à tous les éléments li de
        // formulaires de modèle de page existants
        collectionHolder.children('li').not('#type-page-add-link-li').each(function() {
            addTypePageFormDeleteLink($(this));
        });

        // ajoute un lien « add a modèle de page »
        var $addTypePageLink = $('#type-page-add-link');
        var $newLinkLi = $addTypePageLink.parent('li');
            //var $addTypePageLink = $('<div class="margin-top-30 margin-left-20" id="type-page-add-link"><a href="#" class="pull-left tip-top" title="Ajouter un modèle de page"><i class="fa fa-2x fa-plus-circle"></i></a><span class="add_link_label pull-left margin-left-10">Ajouter un modèle de page</span></div>');
        //var $newLinkLi = $('<li></li>').append($addTypePageLink);

        // ajoute l'ancre « ajouter un modèle de page » et li à la balise ul
        //collectionHolder.append($newLinkLi);

        $addTypePageLink.on('click', function(e) {
            // empêche le lien de créer un « # » dans l'URL
            e.preventDefault();

            // ajoute un nouveau formulaire modèle de page (voir le prochain bloc de code)
            addTypePageForm(collectionHolder, $newLinkLi);
        });

        updateTypePageCount();
    }

    function addTypePageForm(collectionHolder, $newLinkLi) {
        // Récupère l'élément ayant l'attribut data-prototype comme expliqué plus tôt
        var prototype = collectionHolder.attr('data-prototype');

        // Remplace '__name__' dans le HTML du prototype par un nombre basé sur
        // la longueur de la collection courante
        var newForm = prototype.replace(/__name__/g, collectionHolder.children().length);

        // Affiche le formulaire dans la page dans un li, avant le lien "ajouter un modèle de page"
        var $newFormLi = $('<li></li>').append(newForm);

        collectionHolder.children('li').each(function() {
            var siblingSelectedValue = $(this).find('select').find('option:selected').val();

            $newFormLi.find('select').find('option').each(function() {
                if ($(this).val() === siblingSelectedValue) {
                    $(this).remove();
                }
            });

            //$('#typePagesAvailable').html($newFormLi.find('select').find('option').length + " modèle(s) de page disponible(s)");
        });

        $newLinkLi.before($newFormLi);

        // ajoute un lien de suppression au nouveau formulaire
        addTypePageFormDeleteLink($newFormLi);

        initNumberChooser();
        multiselectLauncher($newFormLi.find('.multiselect'));
    }

    function addTypePageFormDeleteLink($typePageFormLi) {
        var $removeFormA = $('<div class="col-xs-1 type-page-action"><a href="#" title="Supprimer ce modèle de page" class="pull-right tip-top"><i class="fa fa-2x fa-minus-circle"></i></a></div>');
        $typePageFormLi.append($removeFormA);

        $removeFormA.on('click', function(e) {
            // empêche le lien de créer un « # » dans l'URL
            e.preventDefault();

            // supprime l'élément li pour le formulaire de modèle de page
            $typePageFormLi.remove();
        });
    }

    function updateTypePageCount() {
        var collectionHolder = $('ul.type-pages');

        var selectInitialCount = collectionHolder.children('li').first().find('select').find('option').length;

        var selectedValues = [];
        collectionHolder.children('li').each(function() {
            var siblingSelectedValue = $(this).find('select').find('option:selected').val();

            $(this).find('select').find('option').each(function() {
                if ($.inArray($(this).val(), selectedValues) >= 0) {
                    $parent = $(this).parent('select');
                    $(this).remove();
                    $parent.multiselect('rebuild');
                }
            });

            selectedValues.push(siblingSelectedValue);
            if (null !== siblingSelectedValue) {
                selectInitialCount = selectInitialCount - 1;
            }

            //$('#typePagesAvailable').html((selectInitialCount - 1) + " modèle(s) de page disponible(s)");
        });
    }

    function initNumberChooser()
    {
        $('.spinner .btn.btn-up').on('click', function() {
            var $input = $(this).parent('.input-group').parent('.spinner').find('input');
            $input.val( parseInt($input.val(), 10) + 1);
        });
        $('.spinner .btn.btn-down').on('click', function() {
            var $input = $(this).parent('.input-group').parent('.spinner').find('input');
            var $newCount = parseInt($input.val(), 10) - 1;
            $input.val( ($newCount >= 0 ) ? $newCount : 0);
        });
    }
    function initFieldsEmpty()
    {
        $('span.input-group-addon').next('input, textarea').keyup(function() {
            if($(this).val() == '') {
                $(this).prev('span.input-group-addon').find('.fa-pencil').addClass('color-red');
                $(this).prev('span.input-group-addon').find('.fa-pencil').removeClass('color-blue');
            } else {
                $(this).prev('span.input-group-addon').find('.fa-pencil').addClass('color-blue');
                $(this).prev('span.input-group-addon').find('.fa-pencil').removeClass('color-red');
            }
        });
    }

    function initConfirm()
    {
        $('a[data-confirm]').click(function() {
            var href = $(this).attr('href') + '?modal-confirm=1';

            $('#popin-confirm').find('.modal-body').html($(this).attr('data-confirm'));
            $('#dataConfirmOK').attr('href', href);
            $('#popin-confirm').modal({
                show: true,
                backdrop: 'static'
            });

            return false;
        });
    }

    function initSubmit()
    {
        $('[data-submit]').click(function() {
            $('#'+ $(this).data('submit')).submit();
        });
    }

    function initCheckboxesWithSelectAction() {
        initSelectConfirm();
        initRangeCheckboxes();
    }

    function initSelectConfirm()
    {
        $('select[data-confirm]').change(function(e) {
            if($(this).val() == -1) {
                return false;
            }
            var href = $(this).attr('href') + '?modal-confirm=1';

            $('#popin-confirm').find('.modal-body').html($(this).attr('data-confirm'));

            if($(this).attr('data-url') && $(this).attr('data-elements')) {
                if($(this).attr('data-elements') && $("input[type='checkbox'][name='" +$(this).attr('data-elements')+ "']:checked").length == 0) {
                    $('#popin-confirm').find('.modal-body').html("Vous devez au moins sélectionner un élément.");
                    $('#popin-confirm').find('.modal-footer').hide();
                    $('#popin-confirm').modal({
                        show: true,
                        backdrop: 'static'
                    });

                    return false;
                    }
                    $('#popin-confirm').find('.modal-footer').show();

                    var dataElements = $(this).attr('data-elements');
                    var dataStatus = $(this).val();
                    var element = $(this);
                    $('#dataConfirmOK').on('click', function(ev) {
                        selectAction(element, dataElements, dataStatus);
                    });
            }

            $('#popin-confirm').modal({
                show: true,
                backdrop: 'static'
            });

            return false;
        });
    }

    function initRangeCheckboxes()
    {
        $('input[type="checkbox"][id="checkboxes-toggle-checkbox-all"], input[type="checkbox"][id="checkboxes-toggle-checkbox-page"]').change(function(e) {
            var checkboxesParent = $(this).attr('data-id');

            //toggle the checkbox
            if($(this).is(':checked')) {
                //if previous checked, uncheck them all
                $('#' + checkboxesParent).checkboxes('uncheck');

                //toggler uncheck
                $(this).prop('checked',false);
            } else {
                //if previous checked, check them all
                $('#' + checkboxesParent).checkboxes('check');

                //toggler check
                $(this).prop('checked',true);
            }

            //toggle
            $('#' + checkboxesParent).checkboxes('toggle');
        });
    }

    function initConfirmForm()
    {
        var modals  = $('.modal.form-confirm');

        modals.each(function() {
            var modal = $(this),
                inputs = modal.find('input[required]'),
                submit = modal.find('button[type="submit"]');

            modals.on('hide.bs.modal', function() {
                $(this).find('form')[0].reset();
            })

            if (!allElementsAreNotEmpty(inputs)) {
                submit.attr('disabled', 'disabled');
            }

            inputs.on('keyup change', function() {
                if (!allElementsAreNotEmpty(inputs)) {
                    submit.attr('disabled', 'disabled');
                } else {
                    submit.removeAttr('disabled');
                }
            });
        });
    }

    function initPreventLosingFormData()
    {
        var forms = $('[data-form-notification-exit="true"]');

        forms.each(function() {
            var form = $(this);
            var initialForm = form.serialize();

            $('input, textarea, select', form).blur(function() {
                var currentForm = form.serialize();
                window.onbeforeunload = initialForm !== currentForm ? function() {
                    return 'Des données ont été saisies.';
                } : null;
            });

            form.submit(function(){
                window.onbeforeunload = null;
            });
        });
    }

    function allElementsAreNotEmpty(elements)
    {
        var counter = 0;

        elements.each(function(event) {

            if ("" == $(this).val()) {
                return;
            }

            counter++;
        });

        if (counter !== elements.length) {
            return false;
        }

        return true;
    }

    function initPagination()
    {
        $('.pagination a').on('click', function(e) {
            if ($('.list-filter')[0]) {
                if ($('.list-filter').is(':hidden')) {
                    window.location = $(this).attr('href') + '&filter_close';
                } else {
                    window.location = $(this).attr('href').replace('filter_close', '');
                }

                return false;
            }
        });
    }

    function initDatepicker()
    {
        $('.date_picker').datepicker({
            format: 'dd/mm/yyyy',
            language: 'fr-FR',
            startDate: '-3d'
        });
    }

    function initMultiselect()
    {
        $.each($('.multiselect'), function() {
            multiselectLauncher($(this));
        });
    }

    function multiselectLauncher(element)
    {
        var multiselectFiltering = element.attr('data-multiselect-no-filtering') ? false : true;

        element.multiselect({
            maxHeight: 200,
            buttonClass: 'btn tip-top',
            enableFiltering: multiselectFiltering,
            enableCaseInsensitiveFiltering: multiselectFiltering,
            filterPlaceholder: 'Rechercher...',
            allSelectedText: $(this).attr('data-multiselect-all-selected-text') ? $(this).attr('data-multiselect-all-selected-text') : '',
            nonSelectedText: $(this).attr('data-multiselect-non-selected-text') ? $(this).attr('data-multiselect-non-selected-text') : '',
            onChange: function(option, checked, select) {
                $(this.$button).tooltip('fixTitle');
            }
        });
    }

    function initToggleFilter() {

        var filter_button = '.filter-button',
            filter_block = '.list-filter';

        if($(filter_block).length > 0) {
            $(filter_button).on('click', function(evt) {
                var $toggler = $(this);

                if ($toggler.hasClass('btn-default')) {
                    $(filter_block).slideUp(250, function(){
                        $toggler.removeClass('btn-default').addClass('btn-primary');
                        window.scrollTo(0, 0);
                    });
                } else {
                    $(filter_block).slideDown(250, function(){
                        $toggler.removeClass('btn-primary').addClass('btn-default');
                        window.scrollTo(0, 0);
                    });
                }
            });
        }
    }



    function initCheckboxSwitch() {
        $("[data-action='checkbox-switch']").each(function() {
            var checkboxSwitch = $(this),
                callback = $(this).data('callback'),
                readOnly = undefined !== $(this).data('read-only') ? $(this).data('read-only') : false;

            checkboxSwitch.bootstrapSwitch({
                size: "mini",
                onColor: "success",
                offColor: "danger",
                onText: "&nbsp;&nbsp;&nbsp;",
                offText: "&nbsp;&nbsp;&nbsp;",
                readonly: readOnly,
                onSwitchChange: function(event, state) {
                    if (undefined !== callback) {
                        event.preventDefault();
                        window[callback](checkboxSwitch, state);
                    }
                },
                onInit: function(event, state) {
                    checkboxSwitch.closest('.bootstrap-switch')
                        .attr('title', checkboxSwitch.attr('title'))
                        .tooltip({'placement': 'bottom'})
                        .parents('label').css('padding-left', '0px')
                    ;
                }
            });
        });
    }

    function initDisableAfterClick()
    {
        var els = $('[data-disable-after-click="true"]');
    
        els.each(function() {
            $(this).on('click', function() {
                $(this).addClass('disabled').siblings('[data-disable-after-click="true"]').addClass('disabled');
            });
        });
    }

    /**
     * Forms tools
     */

    function strip(html)
    {
        var tmp = document.createElement("DIV");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
    }

    function confirmLoadToggle(block)
    {
        if (block) {
            $('#confirm-overlay').fadeIn();
        } else {
            if ($('.modal-dialog')) {
                $('#confirm-overlay').fadeOut();
            }

            if($('#popin-confirm')) {
                $('#popin-confirm').modal('hide');
            }
        }
    }

    function ajaxActivationAction(elem, callParams, messages, status) {
        $('body').css('cursor', 'wait');
        $.ajax({
            method : 'POST',
            url    : elem.attr('data-url'),
            data   : callParams
        }).done(function (data) {
            $('body').css('cursor', 'auto');
            confirmLoadToggle(false);
            if (data.success) {
                if(data.callback) {
                    window.location.href=data.callback;
                } else {
                    var success_message = "";

                    if(status) {
                        success_message = messages['activated']
                    } else {
                        success_message = messages['disabled']
                    }

                    $('#message-wrapper .alert-success').remove();
                    $('#message-wrapper .alert-danger').remove();
                    $('#message-wrapper').prepend('<div class="alert alert-success"></div>');
                    $('#message-wrapper .alert-success').html(success_message);
                }
            } else {
                if(data.locked) {
                    $('#message-wrapper .alert-success').remove();
                    $('#message-wrapper .alert-danger').remove();
                    $('#message-wrapper').prepend('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><span class="message-box"></span></div>');
                    $('#message-wrapper .alert-danger .message-box').html(messages['locked']);
                } else {
                    $('#message-wrapper .alert-success').remove();
                    $('#message-wrapper .alert-danger').remove();
                    $('#message-wrapper').prepend('<div class="alert alert-danger"></div>');
                    $('#message-wrapper .alert-danger').html(messages['no-action']);
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            confirmLoadToggle(false);
            $('body').css('cursor', 'auto');
            $('#message-wrapper .alert-success').remove();
            $('#message-wrapper .alert-danger').remove();
            $('#message-wrapper').prepend('<div class="alert alert-danger">La requête a échoué.</div>');
            //$('#message-wrapper .alert-danger').html(data);
            console.log('Ajax erreur : ' + jqXHR + ' ' + textStatus + ' ' + errorThrown);
        });
    }

    function selectAction(elem, checkboxesName, status) {
        confirmLoadToggle(true);

        var messages = {
            'activated': Translator.trans('messages.change_status_success', {}, 'back_contracts'),
            'disabled': Translator.trans('messages.change_status_failed', {}, 'back_contracts')
        }

        if($("#checkboxes-toggle-checkbox-all").is(':checked')) {
            var callParams = {'contracts': 'all', 'status': status}
        } else {
            var checked = [];
            $("input[type='checkbox'][name='" +checkboxesName+ "']:checked").each(function() {
                checked.push(parseInt($(this).val()))
            });

            var callParams = {'contracts': checked, 'status': status}
        }

        if(callParams['contracts'].length > 0) {
            ajaxActivationAction(elem, callParams, messages, status);
        } else {
            window.location.reload(true);
        }
    }




    /**
     * Actions for users list
     */


    function changeUserActivationStatus(elem, status) {
        var callParams = {'active': status}
        var messages = {
            'activated': Translator.trans('messages.user_activated', {}, 'back_users'),
            'disabled': Translator.trans('messages.user_disabled', {}, 'back_users'),
            'no-action': Translator.trans('messages.no_action', {}, 'back'),
            'locked': Translator.trans('messages.locked', {}, 'back')
        }

        ajaxActivationAction(elem, callParams, messages, status);
    }

    function changeUserReceivemailStatus(elem, status) {
        var callParams = {'receiveMail': status}
        var messages = {
            'activated': Translator.trans('messages.user_receiveMail_activated', {}, 'back_users'),
            'disabled': Translator.trans('messages.user_receiveMail_disabled', {}, 'back_users'),
            'no-action': Translator.trans('messages.no_action', {}, 'back'),
            'locked': Translator.trans('messages.locked', {}, 'back')
        }

        ajaxActivationAction(elem, callParams, messages, status);
    }



    /**
     * Actions for contract-sets list
     */

    function changeContractSetZoneActivationStatus(elem, status) {
        var data_zone = ""
        if(elem.attr('data-zone-slug')) {
            if("draft" == elem.attr('data-zone-slug')) {
                data_zone = "Brouillon";
            }
            else if("demo" == elem.attr('data-zone-slug')) {
                data_zone = "Démo";
            }
            else if("publish" == elem.attr('data-zone-slug')) {
                data_zone = "Publié";
            }
        }

        var callParams = {'status': status}
        var messages = {
            'activated': Translator.trans('messages.contract_set_zone_activated', {'zone_name':data_zone}, 'back_contracts_sets'),
            'disabled': Translator.trans('messages.contract_set_zone_disabled', {'zone_name':data_zone}, 'back_contracts_sets'),
            'no-action': Translator.trans('messages.no_action', {}, 'back'),
            'locked': Translator.trans('messages.locked', {}, 'back')
        }

        ajaxActivationAction(elem, callParams, messages, status);
    }
