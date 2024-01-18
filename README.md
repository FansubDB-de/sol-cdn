Sol-CDN
==========

Simple PHP Content-Delivery-Network (CDN).

Configuring
----------
On HTTPD (or Apache):
1. Upload `index.php` to your webroot (important!)
2. Create `/storage/` and `/cache/` directories
3. (Optional) By default, your page will show the `/storage/default.jpg`-file, edit `$requestedFile` for changing this
4. Create an `.htaccess`-file and put this into it:
```hack
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

On NGINX:
1. No idea
