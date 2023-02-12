/*
 * @link https://www.coinsence.org/
 * @copyright Copyright (c) 2019 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 */
humhub.module('xcoin', function (module, require, $) {

    var client = require('client');
    var event = require('event');

    $('body')
        .on('click', '#ether-enable-btn', function () {
            $(this).fadeOut();

            var $loader = $('#ethereum-loader');
            $loader.show();

            client.ajax($(this).data('target-url'), {type: 'GET'}).catch(function (e) {
                module.log.error(e, true);
            });
        })
        .on('change', '#spacemodulemanualsettings-selectallmembers', function () {
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
            } else if ($(e.target).closest('.crowd-funding button[type="reset"]').length === 0) {
                $('#location-field').removeClass('selected');
            }
        }).on('click', '.crowd-funding .reset', function () {

            $('#fundingfilter-asset_id').val(null).trigger('change');

            $('#fundingfilter-categories').val(null).trigger('change');

            $('#fundingfilter-country').val(null).trigger('change');
            $('#fundingfilter-city').val(null).trigger('change');
            $('.location-selection .selection-text').html('Select location..')
                .removeClass('placeholder')
                .addClass('placeholder');

            $('#fundingfilter-keywords').val(null).trigger('change');

        }).on('click', '.reset-location', function () {
            $('#fundingfilter-country').val('').trigger('change');
            $('#fundingfilter-city').val('').trigger('change');
            $('.location-selection .selection-text').html('Select location..')
                .removeClass('placeholder')
                .addClass('placeholder');
            console.log('changed');
        }).on('blur', '#fundingfilter-city', function () {
            var countryISO = $('#fundingfilter-country').val();
            var city = $('#fundingfilter-city').val();
            setFundingLocation(countryISO, city);
        }).on('change', '#fundingfilter-country', function () {
            var countryISO = $('#fundingfilter-country').val();
            var city = $('#fundingfilter-city').val();
            setFundingLocation(countryISO, city);
        }).on('keydown', '#fundingfilter-city', function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                $(this).blur();
                return false;
            }
        });
    
    // Space-layout-container

    $('body')
        .on('click', '.space-layout-container', function (e) {
            if ($(e.target).closest('#location-field').length > 0) {
                if ($(e.target).closest('#location-field .location-selection').length > 0)
                    $('#location-field').toggleClass('selected');
            } else if ($(e.target).closest('.space-layout-container button[type="reset"]').length === 0) {
                $('#location-field').removeClass('selected');
            }
        }).on('click', '.space-layout-container .reset', function () {

            $('#challengefundingfilter-asset_id').val(null).trigger('change');

            $('#challengefundingfilter-category').val(null).trigger('change');

            $('#challengefundingfilter-country').val(null).trigger('change');
            $('#challengefundingfilter-city').val(null).trigger('change');
            $('.location-selection .selection-text').html('Select location..')
                .removeClass('placeholder')
                .addClass('placeholder');

            $('#challengefundingfilter-keywords').val(null).trigger('change');

        }).on('click', '.reset-location', function () {
            $('#challengefundingfilter-country').val('').trigger('change');
            $('#challengefundingfilter-city').val('').trigger('change');
            $('.location-selection .selection-text').html('Select location..')
                .removeClass('placeholder')
                .addClass('placeholder');
            console.log('changed');
        }).on('blur', '#challengefundingfilter-city', function () {
            var countryISO = $('#challengefundingfilter-country').val();
            var city = $('#challengefundingfilter-city').val();
            setChallengeFundingLocation(countryISO, city);
        }).on('change', '#challengefundingfilter-country', function () {
            var countryISO = $('#challengefundingfilter-country').val();
            var city = $('#challengefundingfilter-city').val();
            setChallengeFundingLocation(countryISO, city);
        }).on('keydown', '#challengefundingfilter-city', function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                $(this).blur();
                return false;
            }
        });

    function setFundingLocation(countryISO, city) {
        if (countryISO && city) {
            var country = $('#fundingfilter-country').select2('data')[0].text;
            $('.location-selection .selection-text').html(`${country}, ${city}`).removeClass('placeholder');
        } else {
            $('.location-selection .selection-text').html('Select location..')
                .removeClass('placeholder')
                .addClass('placeholder');
        }
    }

    function setChallengeFundingLocation(countryISO, city) {
        if (countryISO && city) {
            var country = $('#challengefundingfilter-country').select2('data')[0].text;
            $('.location-selection .selection-text').html(`${country}, ${city}`).removeClass('placeholder');
        } else {
            $('.location-selection .selection-text').html('Select location..')
                .removeClass('placeholder')
                .addClass('placeholder');
        }
    }

    // TODO open location-field popup if an error occurred in one of its fields

    $('body').on('click', '#submit-personal-product', function (e) {
        $("#personal-product").val('1');
    });

    // experience widget
    $('body').on('click', '#experience-actual_position', function () {
        let $end_date_input = $('#experience-end_date');

        if ($(this).is(':checked')) {
            $end_date_input.prop('disabled', true);
            $end_date_input.val('')
        } else {
            $end_date_input.prop('disabled', false);
        }
    });

    // fix datepicker wrong position
    $.extend($.datepicker, {
        _checkOffset: function (inst, offset, isFixed) {
            return offset
        }
    });

    // dismiss product modal on buy action after 2 seconds
    $('body').on('click', '.js-buy-product', function () {
        setTimeout(function (){
            $('#product-popup').find('.modal-dialog ').find('.close').trigger('click');
        }, 2000)
    })

    event.on('humhub:modules:client:pjax:success', function (evt, events, update) {
        initProfileCarousels();
    });

    initProfileCarousels();

});


function imageError(e, color) {
    e.style.backgroundColor = color;
    e.classList.add('myCoinName')
    // e.setAttribute("c","error.png");
    e.removeAttribute("onError");
    e.removeAttribute("onclick");
}

function initProfileCarousels() {

    // user profile slicked widgets
    $('.marketPlacesSlider').slick({
        infinite: false,
        slidesToShow: 1,
        variableWidth: true,
        appendArrows: $('.marketPlacePortfolio .arrows'),
    });
    $('.projectsSlider').slick({
        infinite: false,
        slidesToShow: 1,
        variableWidth: true,
        appendArrows: $('.projectsPortfolio .arrows'),
    });
    $('.slick-prev').append('<i class="fa fa-angle-left"></i>');
    $('.slick-next').append('<i class="fa fa-angle-right"></i>');

}

$('body').on('click', '#challenge-any_reward_asset', function () {
    document.getElementById("challenge-no_rewarding").checked = false;
    document.getElementById("challenge-specific_reward_asset").checked = false;
    document.getElementById("challenge-label_exchange_rate").style.visibility = "hidden";
    document.getElementById("challenge-label_specific_reward_asset").style.visibility = "hidden";
    document.getElementById("challenge-exchange_rate").style.visibility = "hidden";
    document.getElementById("challenge-specific_reward_asset_id").parentElement.style.visibility = "hidden";
});
$('body').on('click', '#challenge-no_rewarding', function () {
    document.getElementById("challenge-any_reward_asset").checked = false;
    document.getElementById("challenge-specific_reward_asset").checked = false;
    document.getElementById("challenge-label_exchange_rate").style.visibility = "hidden";
    document.getElementById("challenge-label_specific_reward_asset").style.visibility = "hidden";
    document.getElementById("challenge-exchange_rate").style.visibility = "hidden";
    document.getElementById("challenge-specific_reward_asset_id").parentElement.style.visibility = "hidden";
});
$('body').on('click', '#challenge-specific_reward_asset', function () {
    document.getElementById("challenge-any_reward_asset").checked = false;
    document.getElementById("challenge-no_rewarding").checked = false;
    document.getElementById("challenge-label_exchange_rate").style.visibility = "visible";
    document.getElementById("challenge-exchange_rate").style.visibility = "visible";
    document.getElementById("challenge-specific_reward_asset_id").parentElement.style.visibility = "visible";
    document.getElementById("challenge-label_specific_reward_asset").style.visibility = "visible";
});

$(document).ready(function () {
    if ($('#challenge-specific_reward_asset_id').length > 0) {
        document.getElementById("challenge-specific_reward_asset_id").parentElement.style.visibility = "hidden";
    }
});

$('body').on('change', '#firstButton', function () {
    if ($(this).prop('checked')) {
        $("#firstButtonTitle").attr("required",true);
        $("#firstButtonText").attr("required", true);
        $("#firstButtonReceiver").attr("required", true);
    } else {
        $("#firstButtonTitle").attr("required", false);
        $("#firstButtonText").attr("required", false);
        $("#firstButtonReceiver").attr("required", false);
    }
});

$('body').on('change', '#secondButton', function () {
    if ($(this).prop('checked')) {
        $("#secondButtonTitle").attr("required",true);
        $("#secondButtonText").attr("required", true);
        $("#secondButtonReceiver").attr("required", true);
    } else {
        $("#secondButtonTitle").attr("required", false);
        $("#secondButtonText").attr("required", false);
        $("#secondButtonReceiver").attr("required", false);
    }
});

