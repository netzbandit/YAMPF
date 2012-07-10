<?php
// With this setting you can have
// two different config sets.
// It also cotrols the debugging and error handling
define ('DEVELOPMENT_ENVIRONMENT',true);

// Number of times a controller/action
// redirect can happen within a request.
// This is to prevent infinite loops.
define ('MAX_STACK_LOOP', 20);


// does this app use a database?
define('USE_DB', false);

if(DEVELOPMENT_ENVIRONMENT) {
    // DB CONNECTION
    define('DB_NAME', '');
    define('DB_USER', '');
    define('DB_PASSWORD', '');
    define('DB_HOST', '');

} else {
    // DB CONNECTION
    define('DB_NAME', '');
    define('DB_USER', '');
    define('DB_PASSWORD', '');
    define('DB_HOST', '');
}

