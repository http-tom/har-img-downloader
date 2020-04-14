## What it does

PHP script written for PHP7.* to take one or more har files, process them to get all image resources that are of mimetype JPEG or PNG and downloads them.


### HAR Files

You can generate a har file from within Chrome developer tools under network (right-click -> Save all as HAR with content). These are JSON files that can be placed in the har folder.

### Deployment

Add to your webserver or run one from the command line. Navigate to `index.php`, for example:

```
php -S localhost:8080
```

```
http://localhost:8080/index.php
```

### Getting to the downloaded images

By default images are downloaded into `images/` directory

