/*
 * @link https://www.coinsence.org/
 * @copyright Copyright (c) 2019 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 */
humhub.module('xcoin', function (module, require, $) {

    var client = require('client');

    $('body')
        .on('click', '#ether-enable-btn' ,function () {
        $(this).fadeOut();

        var $loader = $('#ethereum-loader');
        $loader.show();

        client.ajax($(this).data('target-url'), {type: 'GET'}).catch(function (e) {module.log.error(e, true);});
    })
        .on('change', '#spacemodulemanualsettings-selectallmembers',function () {
        if (this.checked)
            $('.field-spacemodulemanualsettings-selectedmembers').hide();
        else
            $('.field-spacemodulemanualsettings-selectedmembers').show();
    });

    // Crowd-funding

    $('body')
        .on('click', '.crowd-funding', function (e) {
            if ($(e.target).closest('#location-field').length > 0) {
                if ($(e.target).closest('#location-field .location-selection').length > 0)
                    $('#location-field').toggleClass('selected');
            } else {
                $('#location-field').removeClass('selected');
            }
        }).on('click', '.crowd-funding button[type="reset"]', function () {

            // TODO fix reset for fundingfilter-space_id, fundingfilter-challenge_id, fundingfilter-country when filter is already filled and loaded
            $('#fundingfilter-space_id').val('').trigger('change');
            $('#fundingfilter-challenge_id').val('').trigger('change');
            $('#fundingfilter-country').val('').trigger('change');
            // $('#fundingfilter-country').select2().val("").trigger("change");

            $('#fundingfilter-city').val('').trigger('change');
            $('#fundingfilter-city').attr('value', '').trigger('change');

            $('#fundingfilter-keywords').val('').trigger('change');
            $('#fundingfilter-keywords').attr('value', '').trigger('change');
        }).on('click', '.reset-location', function () {
            $('#fundingfilter-country').val('').trigger('change');
            $('#fundingfilter-city').val('').trigger('change');
            $('.location-selection .selection-text').html('Select location..')
                .removeClass('placeholder')
                .addClass('placeholder');
        });

});

