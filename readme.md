# App-Arena.com App Module: Logging
Github: https://github.com/apparena/aa_app_mod_logging

Docs:   http://www.app-arena.com/docs/display/developer

This is a module of the [aa_app_template](https://github.com/apparena/aa_app_mod_logging)

## Module job
Log actions, admin stats or useragents with over requests into database by a default structure and with a basic function call.
In backbonejs, you can do this in views with this.log(type [action|admin|agent], scope [string], data[json]);

The function this.log() is implemented by default in the template router as a view prototype function.

### Dependencies
* Nothing

### Examples

#### Demo Calls
*#page/logging/action
*#page/logging/admin
*#page/logging/group

#### Prototype view calls
* action logs
```javascript
this.log('action', 'scope', {
    auth_uid:      1,
    auth_uid_temp: 0,
    code:          1234,
    data_obj:      {
        logging_demo: 'this is a action logging demo'
    }
});
```

* admin logs
```javascript
this.log('admin', 'scope', This is a admin logging demo. Value can be empty);
```

* group logs
```javascript
this.log('group', {
    'scope_acrion_demo1' : {
        auth_uid:      1,
        auth_uid_temp: 0,
        code:          1234,
        data_obj:      {
            logging_demo: 'this is a action logging demo'
        }
    },
    'scope_acrion_demo2' : {
        auth_uid:      1,
        auth_uid_temp: 0,
        code:          1234,
        data_obj:      {
            logging_demo: 'this is a action logging demo'
        }
    },
    'scope_admin' : {
        value: 'This is a admin logging demo. Value can be empty'
    }
});
```

### Normal use over direct view call
Normally not needed. Its depended in the app_template by default.

* action logs
```javascript
logger.action('scope', {
    auth_uid:      1,
    auth_uid_temp: 0,
    code:          1234,
    data_obj:      {
        logging_demo: 'this is a action logging demo'
    }
});
```

* admin logs
```javascript
logger.admin('scope', 'This is a admin logging demo. Value can be empty');
```

* group logs
```javascript
logger.group({
    'scope_acrion_demo1' : {
        auth_uid:      1,
        auth_uid_temp: 0,
        code:          1234,
        data_obj:      {
            logging_demo: 'this is a action logging demo'
        }
    },
    'scope_acrion_demo2' : {
        auth_uid:      1,
        auth_uid_temp: 0,
        code:          1234,
        data_obj:      {
            logging_demo: 'this is a action logging demo'
        }
    },
    'scope_admin' : {
        value: 'This is a admin logging demo. Value can be empty'
    }
});
```