define([
    'backbone'
], function (Backbone) {
    'use strict';

    var LogAgentModel = Backbone.Model.extend({
        defaults: {
            'hash_id': '',
            'data':     {}
        }
    });

    return LogAgentModel;
});