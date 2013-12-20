define([
    'jquery',
    'underscore',
    'backbone'
], function ($, _, Backbone) {
    'use strict';

    var LoggerView = Backbone.View.extend({

        initialize: function () {
            _.bindAll(this, 'action', 'agent', 'admin');
        },

        action: function (data) {
            var request = {
                action: 'logAction',
                data:   data
            };

            this.save(request);
        },

        agent: function () {
            var request = {
                action: 'logAgent'
            };
            this.save(request);
        },

        admin: function (data) {
            var request = {
                action: 'logAdmin',
                data:   data
            };
            this.save(request);
        },

        save: function (request) {
            //_.debug.log('log', request.data.scope, request.action);
            request.module = 'logging';
            this.ajax(request, true);
        }
    });

    return LoggerView;
});