# Upgrade Guide

## Upgrading from 0.5 to 0.6 - package name changed

The package name changed from `laravel-mail-allowlist` to a more fitting `laravel-mail-middleware`. Change the `composer.json` entries as follows:

```diff
"require": {
-   "tobmoeller/laravel-mail-allowlist": "^0.5.0"
+   "tobmoeller/laravel-mail-middleware": "^0.6.0"
}
```

The configuration file name was changed to `mail-middleware.php` and the environment variable names were updated. You should compare and incorporate any of these changes into your existing file if you have previously published it. (It may be easiest to make a backup copy of your existing file, re-publish it from this package, and then re-make your customizations to it.)

If you have used any of the environment variables to control the package configuration, you have to change the names accordingly:

```diff
-MAIL_ALLOWLIST_ENABLED
+MAIL_MIDDLEWARE_PACKAGE_ENABLED
 
-MAIL_ALLOWLIST_MIDDLEWARE_ENABLED
+MAIL_MIDDLEWARE_ENABLED
 
-MAIL_ALLOWLIST_ALLOWED_DOMAINS
+MAIL_MIDDLEWARE_ALLOWED_DOMAINS
 
-MAIL_ALLOWLIST_ALLOWED_EMAILS
+MAIL_MIDDLEWARE_ALLOWED_EMAILS
 
-MAIL_ALLOWLIST_GLOBAL_TO
+MAIL_MIDDLEWARE_GLOBAL_TO

-MAIL_ALLOWLIST_GLOBAL_CC
+MAIL_MIDDLEWARE_GLOBAL_CC

-MAIL_ALLOWLIST_GLOBAL_BCC
+MAIL_MIDDLEWARE_GLOBAL_BCC
 
-MAIL_ALLOWLIST_LOG_ENABLED
+MAIL_MIDDLEWARE_LOG_ENABLED

-MAIL_ALLOWLIST_LOG_CHANNEL
+MAIL_MIDDLEWARE_LOG_CHANNEL

-MAIL_ALLOWLIST_LOG_LEVEL
+MAIL_MIDDLEWARE_LOG_LEVEL
 
-MAIL_ALLOWLIST_SENT_MIDDLEWARE_ENABLED
+MAIL_MIDDLEWARE_SENT_ENABLED
 
-MAIL_ALLOWLIST_SENT_LOG_ENABLED
+MAIL_MIDDLEWARE_SENT_LOG_ENABLED

-MAIL_ALLOWLIST_SENT_LOG_CHANNEL
+MAIL_MIDDLEWARE_SENT_LOG_CHANNEL

-MAIL_ALLOWLIST_SENT_LOG_LEVEL
+MAIL_MIDDLEWARE_SENT_LOG_LEVEL
```

## Upgrading from 0.4 to 0.5

The `mail-allowlist.php` configuration structure has changed. You should compare and incorporate any of these changes into your existing file if you have previously published it. (It may be easiest to make a backup copy of your existing file, re-publish it from this package, and then re-make your customizations to it.)

