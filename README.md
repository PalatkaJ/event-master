# NSWI142 final project 
## installation instructions
### Database
Create a new mysql database and import the file `db_schema.sql`, this will create
the tables and insert test data to the tables.
### Configuration
Open file: [.config.template.php](.config.template.php) and fill in your db credentials. 
Rename/ copy it to `.config.php`.

## Preview
![Landing Page Screenshot](./docs/landing_page.png)
![Register for Event Screenshot](./docs/register_for_event.png)

### local testing
set up the connection with ssh (in separate terminal) and then run the server:
```
$ ssh -L 3306:localhost:3306 webik
..
$ php -S 127.0.0.1:8888 -t ./public/
```
### on webik
works thanks to htaccess files,
one /.htaccess and the other in /public/.htaccess 

