# Configuration

## Contents
- [Low level](#low-level)
- [High level](#high-level)

## Low Level

Paulus sets some important application **constants** in its [index.php](../index.php) file. Some of them can be changed, without large consequences, to change some of Paulus' lower-level behavior

#### You can change some of Paulus' directory path's with these constants:
PAULUS_CONFIG_DIR
PAULUS_LIB_DIR
PAULUS_EXTERNAL_LIB_DIR
PAULUS_ROUTES_DIR

#### You can change the location of Paulus' early-loaded (before AutoLoading is enabled) or non-lazy-loaded libaries with these:
PAULUS_AUTOLOADER_LOCATION
PAULUS_ROUTER_LOCATION

#### You can define the optional benchmark header that triggers benchmarking results
##### _The header name is used for both requests and responses_
PAULUS_BENCHMARK_HEADER_NAME

#### You can also enable/disable some optional behaviors here by commenting/un-commenting them here:
##### _They need no value, as they're simply checked if they are defined or not_
PAULUS_INTERNAL_AUTOLOAD_DISABLED
PAULUS_AUTOLOAD_DISABLED
PAULUS_ALLOW_BENCHMARK_HEADER
PAULUS_BENCHMARK_ALWAYS
