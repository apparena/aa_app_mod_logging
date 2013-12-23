define([
    'jquery',
    'underscore',
    'backbone'
], function ($, _, Backbone) {
    'use strict';

    var namespace = 'logging',
        View, Init, Remove, Instance;

    View = Backbone.View.extend({

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

    Remove = function () {
        _.singleton.view[namespace].unbind().remove();
        delete _.singleton.view[namespace];
    };

    Init = function (init) {

        if (_.isUndefined(_.singleton.view[namespace])) {
            _.singleton.view[namespace] = new View();
        } else {
            if (!_.isUndefined(init) && init === true) {
                Remove();
                _.singleton.view[namespace] = new View();
            }
        }

        return _.singleton.view[namespace];
    };

    Instance = function () {
        return _.singleton.view[namespace];
    };

    return {
        init:        Init,
        view:        View,
        remove:      Remove,
        namespace:   namespace,
        getInstance: Instance
    };
});