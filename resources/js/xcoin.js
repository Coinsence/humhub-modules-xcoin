/*
 * @link https://www.coinsence.org/
 * @copyright Copyright (c) 2019 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 */
humhub.module('xcoin', function (module, require, $) {

    var client = require('client');

    var loader = '<div class="loader humhub-ui-loader" style="padding:0">' +
        '<div class="sk-spinner sk-spinner-three-bounce">' +
        '<div class="sk-bounce1"></div>' +
        '<div class="sk-bounce2"></div>' +
        '<div class="sk-bounce3"></div>' +
        '</div></div>';

    $("body").on('click', '#ether-enable-btn' ,function () {
        $(this).fadeOut();

        var $daoAddressContainer = $('#dao-address-container');
        var $coinAddressContainer = $('#coin-address-container');

        $daoAddressContainer.html(loader);
        $coinAddressContainer.html(loader);

        client.ajax($(this).data('target-url'), {type: 'GET'})
            .then(function (data) {
                $daoAddressContainer.html(
                    "<a href='https://rinkeby.etherscan.io/address/'" + data.item.daoAddress + ">" +
                    data.item.daoAddress
                    + "</a>"
                );

                $coinAddressContainer.html(
                    "<a href='https://rinkeby.etherscan.io/token/'" + data.item.coinAddress + ">" +
                    data.item.coinAddress
                    + "</a>"
                );
            })
            .catch(function (e) {
                module.log.error(e, true);
            });
    });
});

