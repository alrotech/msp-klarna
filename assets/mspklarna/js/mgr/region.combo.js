/**
 * The MIT License
 * Copyright (c) 2021 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file.
 */

mspKlarna.combo.Region = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: 'region',
        hiddenName: 'region',
        displayField: 'name',
        valueField: 'id',
        fields: ['id', 'name'],
        pageSize: 10,
        typeAhead: true,
        preselectValue: false,
        value: 0,
        editable: true,
        hideMode: 'offsets',
        url: mspKlarna.ms2Connector,
        baseParams: {
            // action: 'mgr/settings/status/getlist',
            // ??
            combo: true
        }
    });

    mspKlarna.combo.Region.superclass.constructor.call(this, config);
};

Ext.extend(mspKlarna.combo.Region, MODx.combo.ComboBox);
Ext.reg('klarna-combo-region', mspKlarna.combo.Region);
