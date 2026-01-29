# EventMaster - semestral project
## Installation Instructions
After cloning the repository there are still some steps needed to deploy the web application.
### DB creation
First we need to create the database instance.
```
mysql -u [USER] -p
# Enter password when prompted
CREATE DATABASE [DB_NAME];
EXIT;
```
### Import
After creating the db, import the tables and required test data.
```
mysql -u [USER] -p [DB_NAME] < db_schema.sql
```
### Application configuration
The application uses JSON file for configuration.
1. copy `config.json.example` into `config.json`
2. open `config.json` and update the credentials to match your local environment
### Deployment
After all the initialization steps, run `deploy.sh` and you can click [here](http://127.0.0.1:8888) to visit the deployed application.

## Preview of the application
![Landing Page Screenshot](images/landing_page.png)
![Register for Event Screenshot](images/register_for_event.png)

### Notes (for me)
#### local testing
set up the connection with ssh (in separate terminal) and then run the server:
```
$ ssh -L 3306:localhost:3306 webik
..
$ php -S 127.0.0.1:8888 -t ./public/
```
#### on webik
works thanks to htaccess files,
one /.htaccess and the other in /public/.htaccess 

