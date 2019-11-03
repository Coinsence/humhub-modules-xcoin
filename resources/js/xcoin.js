/*
 * @link https://www.coinsence.org/
 * @copyright Copyright (c) 2019 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 */
humhub.module('xcoin', function (module, require, $) {

    var client = require('client');

    $("body").on('click', '#ether-enable-btn' ,function () {
        $(this).fadeOut();

        var $loader = $('#ethereum-loader');
        $loader.show();

        client.ajax($(this).data('target-url'), {type: 'GET'}).catch(function (e) {module.log.error(e, true);});
    });

    $("body").on('change', '#spacemodulemanualsettings-selectallmembers',function () {
        if (this.checked)
            $('.field-spacemodulemanualsettings-selectedmembers').hide();
        else
            $('.field-spacemodulemanualsettings-selectedmembers').show();
    });

});

