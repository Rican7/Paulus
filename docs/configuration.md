# Configuration

## Contents
- [Low level](#low-level)
- [High level](#high-level)

## Low Level

Paulus sets some important application **constants** in its [index.php](../index.php) file. Some of them can be changed, without large consequences, to change some of Paulus' lower-level behavior

#### You can change some of Paulus' directory path's with these constants:
- PAULUS_CONFIG_DIR
- PAULUS_LIB_DIR
- PAULUS_APP_DIR
- PAULUS_EXTERNAL_LIB_DIR
- PAULUS_MODELS_DIR
- PAULUS_ROUTES_DIR

#### You can change the location of Paulus' early-loaded (before AutoLoading is enabled) or non-lazy-loaded libaries with these:
- PAULUS_AUTOLOADER_LOCATION
- PAULUS_ROUTER_LOCATION

#### You can define the optional benchmark header that triggers benchmarking results
_The header name is used for both requests and responses_
- PAULUS_BENCHMARK_HEADER_NAME

#### You can also enable/disable some optional behaviors here by commenting/un-commenting them here:
_They need no value, as they're simply checked if they are defined or not_
- PAULUS_INTERNAL_AUTOLOAD_DISABLED
 - Disables Paulus' autoloader responsible for autoloading all of its internal libraries (not recommended)
- PAULUS_APPLICATION_AUTOLOAD_DISABLED
 - Disables the application autoloader responsible for autoloading all of the application's libraries/classes
- PAULUS_EXTERNAL_AUTOLOAD_DISABLED
 - Disables the external autoloader responsible for autoloading all of the high-level defined external libraries (not recommended)
- PAULUS_ALLOW_BENCHMARK_HEADER
 - Allows the client to send a header that enables the response to output a similar header containing the speed of script execution (time)
- PAULUS_BENCHMARK_ALWAYS
 - Enables Paulus to always (regardless of sent headers) respond with a header containing benchmark/speed-of-execution information.

## High Level

Paulus allows for easy configuration via name-spaced files that define and return arrays of configuration information. These items are designed to make it easy to alter Paulus' behavior and response/output

### App Meta
_Define some of our application's meta-information_
- **'base_url'** - Set the base url of the application
- **'app_protocol'** - Set the application's protocol (scheme). It will auto-detect by default.
- **'app_url'** - Set the application's full url. It will compile itself from the other options by default.
- **'title'** - Set the application's title

### Autoload Directories
_Define the directories that Paulus will search in when using the external-autoloader_
Paulus follows the [PSR-0](http://phpmaster.com/autoloading-and-the-psr-0-standard/) style for autoloading, so classes will be autoloaded according to their [Namespace](http://php.net/manual/en/language.namespaces.php) and class name.

### Database
_Define the configuration that is passed to PHP ActiveRecord (by default... but you can use whatever ORM/DB-class you want)_
- **'connections'** - Set an array of connection slugs/aliases. Key = Slug name. Value = Connection parameters.
 - For more details on syntax and driver support, see [PHP AR: Configuration Setup](http://www.phpactiverecord.org/projects/main/wiki/Configuration__Setup)
- **'default_connection'** - Set the default connection to use for PHP AR (value should match a slug from the 'connections' array).
- **'model_directory'** - Set the directory that you will store your ActiveRecord Models in, so PHP AR can auto-load them correctly.

### External Libs
_Define the external libraries to be explicitly loaded_
Not all libraries that you may use will be autoload-able (lol). This configuration allows for some external libraries to be defined so that they can be explicitly loaded on app startup.
**Note**: Feel free to disable PHP ActiveRecord if you'd rather use another ORM/DB-class

### REST
_Define our application's REST response-style configuration_
- **'defaults'** - Set an array of response defaults
 - **'status'** - Set a default status message (for when one isn't set in the application logic, or when one doesn't match to our pre-defined status-codes)
 - **'data-type'** - Set the default format/type that the application will respond in
 - **'jsonp-padding'** - Set the name of the JSONP callback function "padding"
- **'status-codes'** - Set an array of status messages (value) matched to their corresponding HTTP Status Codes (key)
- **'mime-types'** - Set an array of MIME types (value) matched to their corresponding data-type (key)
- **'http-access-control'** - Set an array of [CORS](http://en.wikipedia.org/wiki/Cross-origin_resource_sharing) related access control headers
 - **'allow-headers'** - Set the allowable headers as a comma-delimitted string
 - **'allow-methods'** - Set the allowable methods as a comma-delimitted string
 - **'allow-origin'** - Set the allowable origin (client access location)

### Routing
_Define our Router's (Klein by default) configuration_
- **'load_all_automatically'** - Boolean deciding whether all routes in the **PAULUS_ROUTES_DIR** should be automatically loaded
- **'routes'** - Set an array of all the routes that should be loaded manually (if **'load_all_automatically'** isn't **true**)
- **'top_level_route'** - Set a specific route to handle top-level requests (non-namespaced)
- **'auto_start_controllers'** - Boolean deciding whether controllers matching the route's namespace should automatically be loaded and instanciated
- **'pass_app_to_service'** - Boolean deciding whether the 'app' property (referring to the Paulus instance) should be passed to each route through the 'service' argument
- **'controller_base_namespace'** - Set a string defining the base namespace used when loading and instanciating controllers for each route

### Template
_Define our built-in, quick templating engine's configuration_
- **'global_template_processing'** - Boolean deciding whether the template processor should run on every response
- **'only_process_returned_data'** - Boolean deciding whether the template processor should only process the data that is returned in the 'data' property of the response
- **'keys'** - Set an array of all the template keys and their replaced values for our template processor
