/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS("pimcore.plugin.coreshop.global");
pimcore.plugin.coreshop.global = {

    settings : {},

    initialize : function(settings) {
        this._countriesLoaded = false;
        this._currenciesLoaded = false;
        this._zonesLoaded = false;
        this._orderStatesLoaded = false;

        this.settings = settings;

        if(intval(this.settings.coreshop.isInstalled)) {
            this._initStores();
        }
        else {
            pimcore.plugin.coreshop.broker.fireEvent("storesLoaded");
        }
    },

    _initStores : function() {
        this._initCountries();
        this._initCurrencies();
        this._initZones();
        this._initOrderStates();
    },

    _initCurrencies : function() {
        var self = this;

        var currencyProxy = new Ext.data.HttpProxy({
            url:'/plugin/CoreShop/admin_currency/get'
        });
        var currencyReader = new Ext.data.JsonReader({
            totalProperty:'total',
            successProperty:'success'
        }, [
            {name:'id'},
            {name:'name'},
            {name:'symbol'},
            {name:'isoCode'},
            {name:'numericIsoCode'},
            {name:'exchangeRate'}
        ]);

        var currencyStore = new Ext.data.Store({
            restful:false,
            proxy:currencyProxy,
            reader:currencyReader
        });
        currencyStore.load();

        currencyStore.on("beforeload", function() {
            self._currenciesLoaded = false;
        });

        currencyStore.on("load", function() {
            self._currenciesLoaded = true;
            self._checkStoresLoaded();
        });

        pimcore.globalmanager.add("coreshop_currencies", currencyStore);
    },

    _initZones : function() {
        var self = this;
        var zoneProxy = new Ext.data.HttpProxy({
            url:'/plugin/CoreShop/admin_zone/get'
        });
        var zoneReader = new Ext.data.JsonReader({
            totalProperty:'total',
            successProperty:'success'
        }, [
            {name:'id'},
            {name:'name'},
            {name:'active'}
        ]);

        var zoneStore = new Ext.data.Store({
            restful:false,
            proxy:zoneProxy,
            reader:zoneReader
        });
        zoneStore.load();

        zoneStore.on("beforeload", function() {
            self._zonesLoaded = false;
        });

        zoneStore.on("load", function() {
            self._zonesLoaded = true;
            self._checkStoresLoaded();
        });

        pimcore.globalmanager.add("coreshop_zones", zoneStore);
    },

    _initCountries : function() {
        var self = this;
        var countryProxy = new Ext.data.HttpProxy({
            url:'/plugin/CoreShop/admin_country/get-countries'
        });
        var countryReader = new Ext.data.JsonReader({
            totalProperty:'total',
            successProperty:'success'
        }, [
            {name:'id'},
            {name:'name'},
            {name:'symbol'},
            {name:'isoCode'},
            {name:'numericIsoCode'},
            {name:'exchangeRate'}
        ]);

        var countryStore = new Ext.data.Store({
            restful:false,
            proxy:countryProxy,
            reader:countryReader
        });
        countryStore.load();

        countryStore.on("beforeload", function() {
            self._countriesLoaded = false;
        });

        countryStore.on("load", function() {
            self._countriesLoaded = true;
            self._checkStoresLoaded();
        });

        pimcore.globalmanager.add("coreshop_countries", countryStore);
    },

    _initOrderStates : function() {
        var self = this;
        var orderStateProxy = new Ext.data.HttpProxy({
            url:'/plugin/CoreShop/admin_orderstates/list'
        });
        var orderStateReader = new Ext.data.JsonReader({
            totalProperty:'total',
            successProperty:'success'
        }, [
            {name:'id'},
            {name:'name'}
        ]);

        var orderStateStore = new Ext.data.Store({
            restful:false,
            proxy:orderStateProxy,
            reader:orderStateReader
        });
        orderStateStore.load();

        orderStateStore.on("beforeload", function() {
            self._orderStatesLoaded = false;
        });

        orderStateStore.on("load", function() {
            self._orderStatesLoaded = true;
            self._checkStoresLoaded();
        });

        pimcore.globalmanager.add("coreshop_order_states", orderStateStore);
    },

    _checkStoresLoaded : function() {
        if(this._countriesLoaded && this._zonesLoaded && this._currenciesLoaded && this._orderStatesLoaded) {
            pimcore.plugin.coreshop.broker.fireEvent("storesLoaded");
            return true;
        }
        else {
            return false;
        }
    }
};

if (!String.prototype.format) {
    String.prototype.format = function() {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function(match, number) {
            return typeof args[number] != 'undefined'
                ? args[number]
                : match
                ;
        });
    };
}