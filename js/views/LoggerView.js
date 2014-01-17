define([
    'ViewExtend',
    'jquery',
    'underscore',
    'backbone'
], function (View, $, _, Backbone) {
    'use strict';

    return function () {
        View.namespace = 'logging';

        View.code = Backbone.View.extend({

            initialize: function () {
                _.bindAll(this, 'action', 'admin', 'group');
            },

            action: function (scope, data) {
                var request = {
                    action: 'logAction',
                    data:   {
                        scope: scope,
                        data:  data
                    }
                };

                this.save(request);
            },

            admin: function (scope, value) {
                var request = {
                    action: 'logAdmin',
                    data:   {
                        scope: scope,
                        value: value
                    }
                };
                this.save(request);
            },

            group: function (data) {
                var request = {
                    action: 'logGroup',
                    data:   data
                };
                this.save(request);
            },

            save: function (request) {
                request.module = 'logging';
                this.ajax(request, true);
            }
        });

        return View;
    }
});