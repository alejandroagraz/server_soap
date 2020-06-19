# Server Soap

    Practical example of a soap server in symfony.

## Installation

    composer require annotations
    composer require symfony/orm-pack
    composer require --dev symfony/maker-bundle

## Configure

    Step number 1
        Cree un host virtual para una carpeta wsdl y dentro incluya el archivo soapServer.wsdl
            Example:
                <VirtualHost *:80>
                    DocumentRoot "/opt/lampp/htdocs/wsdl"
                    ServerName wsdl.doc

                    ErrorLog "logs/wsdl.doc-error_log"
                    CustomLog "logs/wsdl.doc-access_log" common
                </VirtualHost>

    Step number 2
        In the /etc/hots file add the ServerName of the VirtualHost previously created and restart the server
            Example: 
                127.0.0.1	wsdl.doc

    Step number 3
        Configure database connection in the .env file
            Example: 
                DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"

    Step number 
        Generate entity
            Example:
                php bin/console make:migration
                php bin/console doctrine:migrations:migrate


## Raise project with symfony server

    In a terminal inside the root folder launch the command: symfony server:start

## Developed Project

    Developed entirely by Jose Agraz 
