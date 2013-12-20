define([
    'backbone'
], function (Backbone) {
    'use strict';

    var LogAdminModel = Backbone.Model.extend({
        defaults: {
            'id':    null,
            'type':  '',
            'value': ''
        }
    });

    return LogAdminModel;
});