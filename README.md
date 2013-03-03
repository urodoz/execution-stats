TimeExecutionStatsBundle
========================

Service to handle progress of time execution of service and methods in your Symfony2 application

Installation on Symfony 2.1
---------------------------

On the *composer.json* file add :

``` json
    
    "require": {
        "urodoz/execution-stats": "dev-master",        
    }
```

Run composer update

```
php composer.phar update
```

Add the bundle to the *AppKernel.php*

``` php
<?php
    
    $bundles = array(
        new Urodoz\TimeExecutionStatsBundle\UrodozTimeExecutionStatsBundle();
    );
    
```
    
You can add the bundle only in dev and test environment as follows

``` php
    
    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        //Adding bundle to dev and test environments
        $bundles[] = new Urodoz\TimeExecutionStatsBundle\UrodozTimeExecutionStatsBundle();
    }
    
```
    
Update the *Doctrine* schema (you can use --dump-sql option before to check the SQL executed). The tracking time data is stored on a log table inside your data model.

```
php app/console doctrine:schema:update --force
```
    
Usage as Symfony 2 service
--------------------------

You can access now to the time tracking service **urodoz.timeTracker**. 

The parameters to track a service are : 

* Name : Name (unique) of the tracking item
* Tag : Used to group results
* Version : The version of the service. Can be a generic parameter setted for all platform. The graphs will show the progression of performance between different versions.

Example of tracking :

``` php

    protected function doSomeTask()
    {
        //Head of the method
        if($this->getContainer()->has("urodoz.timeTracker")) {
            $this->get('urodoz.timeTracker')->start(
                "user.doSomeTask",
                "UserBundle",
                $this->container->getParameter("appVersion"));
        }
        ...
        //code
        ...
        if($this->getContainer()->has("urodoz.timeTracker")) {
            $this->get('urodoz.timeTracker')->stop("user.doSomeTask");
        }
    }
```

See tracking data on Symfony 2 commands
---------------------------------

Once the bundle has been installed. You have access to 3 new commands on your *app/console* :

``` 
php app/console urodoz:statsbundle:pack
```

Will pack the data on database

```
php app/console urodoz:statsbundle:ranking
```

Show the slower tracking methods or services , optionally grouped by tags

```
php app/console urodoz:statsbundle:show
```

Show data of performance improve, referenced from the last versions

See tracking data generated on HTML
-----------------------------------

*Currenty on development*

Integrate tracking data into Jenkins application
------------------------------------------------

*Currenty on development*
