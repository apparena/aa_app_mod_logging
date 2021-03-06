define([
    'jquery',
    'underscore',
    'modules/aa_app_mod_logging/js/views/LoggerView'
], function ($, _, Logger) {
    'use strict';

    return function (type) {
        var logger = Logger().init();

        switch (type) {
            case 'action':
                logger.action('scope_name', {
                    auth_uid:      1,
                    auth_uid_temp: 0,
                    code:          1234,
                    data_obj:      {
                        logging_demo: 'this is a action logging demo'
                    }
                });
                break;

            case 'admin':
                logger.admin('scope_name', 'This is a admin logging demo. Value can be empty');
                break;

            case 'group':
                logger.group({
                    'scope_action_demo1': {
                        auth_uid:      1,
                        auth_uid_temp: 0,
                        code:          1234,
                        data_obj:      {
                            logging_demo: 'this is a action logging demo'
                        }
                    },

                    'scope_action_demo2': {
                        auth_uid:      1,
                        auth_uid_temp: 0,
                        code:          1234,
                        data_obj:      {
                            logging_demo: 'this is a action logging demo'
                        }
                    },

                    'scope_admin_name': 'This is a admin logging demo. Value can be empty'
                });
                break;
        }
    };
});