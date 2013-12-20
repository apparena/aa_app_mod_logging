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
* action logs
```javascript
this.log('action', 'user_door_open', {
    auth_uid:      _.uid,
    auth_uid_temp: _.uid_temp,
    code:          2,
    data_obj:      {
        door:  this.model.get('door_id')
    }
});
```

* admin logs
```javascript
this.log('admin', 'app_fangate_open', {
    log: {
        app_fangate_show: ''
    }
});
```

* group logs
```javascript
this.log('action', 'user_terminal_closed', {
    auth_uid:      _.uid,
    auth_uid_temp: _.uid_temp,
    code:          2002,
    data_obj: {
        admin: {
            app_end: ''
        }
    }
});
```

### Load module with require
Not needed. Its depended in the app_template by default.