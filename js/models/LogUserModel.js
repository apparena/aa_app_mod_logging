define([
    'backbone'
], function (Backbone) {
    'use strict';

    var LogUserModel = Backbone.Model.extend({
        defaults: {
            'auth_uid': '',
            'data':     {},
            'action':   '',
            'agent_id': 0,
            'ip':       ''
        }
    });

    return LogUserModel;
});