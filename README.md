# Magento 2 Configurable Matrix Table

![alt text](https://raw.githubusercontent.com/magekey/module-configurable-matrix-table/master/docs/images/preview.png)

## Features:

- Show matrix table in tabs for configurable products. 

## Installing the Extension

    composer require magekey/module-configurable-matrix-table

## Deployment

    php bin/magento maintenance:enable                  #Enable maintenance mode
    php bin/magento setup:upgrade                       #Updates the Magento software
    php bin/magento setup:di:compile                    #Compile dependencies
    php bin/magento setup:static-content:deploy         #Deploys static view files
    php bin/magento cache:flush                         #Flush cache
    php bin/magento maintenance:disable                 #Disable maintenance mode

## Versions tested
> 2.2.2
