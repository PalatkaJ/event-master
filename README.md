# NSWI142 final project 
## local testing
set up the connection with ssh (in separate terminal) and then run the server:
```
$ ssh -L 3306:localhost:3306 webik
..
$ php -S 127.0.0.1:8888 -t ./public/
```
## on webik
works thanks to htaccess files,
one /.htaccess and the other in /public/.htaccess 
### Note that the url index.php gets is different locally and on webik