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

            $('#fundingfilter-space_id').val('').trigger('change');

            $('#fundingfilter-categories').val('').trigger('change');

            $('#fundingfilter-country').val('').trigger('change');
            $('#fundingfilter-city').val('').trigger('change');
            $('.location-selection .selection-text').html('Select location..')
                .removeClass('placeholder')
                .addClass('placeholder');

            $('#fundingfilter-keywords').val('').trigger('change');
            $('#fundingfilter-keywords').attr('value', '').trigger('change');

        }).on('click', '.reset-location', function () {
            $('#fundingfilter-country').val('').trigger('change');
            $('#fundingfilter-city').val('').trigger('change');
            $('.location-selection .selection-text').html('Select location..')
                .removeClass('placeholder')
                .addClass('placeholder');
        }).on('blur', '#fundingfilter-city', function () {
            var country = $('#fundingfilter-country').val();
            var city = $('#fundingfilter-city').val();

            setLocation(country, city);

        }).on('keydown', '#fundingfilter-city', function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                $(this).blur();
                return false;
            }
        });

    function setLocation(country, city) {
        if (country && city) {
            $('.location-selection .selection-text').html(`${country}, ${city}`).removeClass('placeholder');
        }
        else {
            $('.location-selection .selection-text').html('Select location..')
              .removeClass('placeholder')
              .addClass('placeholder');
        }
        console.log(country, city);
    }

});

