/**
 * The MIT License
 * Copyright (c) 2021 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file.
 */

let mspKlarna = function(config) {
    config = config || {};
    mspKlarna.superclass.constructor.call(this, config);
};
Ext.extend(mspKlarna, Ext.Component, {combo: {}, grid: {}, window: {}});
Ext.reg('klarna', mspKlarna);

mspKlarna = new mspKlarna();
