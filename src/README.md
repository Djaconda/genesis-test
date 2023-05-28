Genesis Test Application
============================

Genesis main application.

DIRECTORY STRUCTURE
-------------------

      api/                    contains default API endpoint
      bin/                    contains binary files 
      common/                 contains application sources (all of the code goes here)
            modules/          contains application modules
      config/                 contains all of the application configurations
            general/
                  api/        contains API application configurations
                  common/     contains common configurations
                  console/    contains Console application configurations
                  container/  contains DI container configurations
      data/                   (optional directory) contains application files.
      runtime/                contains files generated during runtime
      tests/                  contains various tests for the application
            unit/             contains unit tests
      vendor/                 contains dependent 3rd-party packages

DESCRIPTION
------------

`/rate` endpoint return preloaded data from the [Coingecko](https://api.coingecko.com)

The cosole command will update the rate per hour by cron

~~~
0 * * * * bin/yii rate/rate/refresh
~~~
