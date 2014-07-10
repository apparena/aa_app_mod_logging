# App-Arena.com App Module: Logging
**Github:** https://github.com/apparena/aa_app_mod_logging
**Docs:**   http://www.appalizr.com/index.php/logging.html

This is a module of the [aa_app_template](https://github.com/apparena/aa_app_template)

## Module job
Log actions, admin stats or useragents with over requests into database by a default structure and with a basic function call.
In backbonejs, you can do this in views with this.log(type [action|admin|agent], scope [string], data[json]);

The function this.log() is implemented by default in the template router as a view prototype function.

### Dependencies
* Nothing

### Important functions
* **action** - to log any action
* **admin** - to log admin thinks
* **group** - to log many actions and admin thinks in one request

### Function parameter
#### this.log(Type, Scope, {Options})
| Key | Type | Description |
|--------|--------|--------|
| type | string | log type, use admin/action/agent/group |
| scope | string | scopename for you log, define what you want |
| data | object | params as JSON object with basic structure auth_uid [only on action logs] int (User ID) auth_uid_temp [only on action logs] int (Temporary user ID if user ID not exist. Later, if user ID is not zero, all user ID's with the same temporary user ID's will be changed to the right user ID.) code [only on action logs] int (scope or status code) log [only on admin logs] json (Only with one parameter, scope as key and a value that will be stored. If the scope exist, it will be count one up) data_obj json (Additional thinks to log. Here you are free to add key/value pairs. If you add the key admin, you can set them an JSON string like the "log" parameter. With this combination, you can safe a action and a admin log in one request.) |

### Examples
#### Demo Calls
* \#page/logging/action
* \#page/logging/admin
* \#page/logging/group

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