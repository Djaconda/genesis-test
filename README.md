Genesis Test application
============================

Docker compose for Genesis Test application.

DIRECTORY STRUCTURE
-------------------

      data/                   contains docker data (apache logs, etc)
      docker/                 contains all of the docker service configurations      
      src/                    contains application sources (all of the code goes here)

REQUIREMENTS
------------

Requirements `docker`, `docker-compose`, `make`.

CONFIGURATION
-------------

Cange file `docker-compose.override.yml`
Cange file `.env`

INSTALLATION
------------

~~~
make init
~~~
