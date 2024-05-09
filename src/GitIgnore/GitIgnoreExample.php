# WordPress files
wp-admin
wp-includes
/license.txt
/readme.html
/wp-activate.php
/index.php
/wp-blog-header.php
/wp-comments-post.php
/wp-config-sample.php
/wp-config.php
/wp-cron.php
/wp-links-opml.php
/wp-load.php
/wp-login.php
/wp-mail.php
/wp-settings.php
/wp-signup.php
/wp-trackback.php
/xmlrpc.php

# Content
wp-content/*
!wp-content/mu-plugins/
!wp-content/plugins/
!wp-content/themes/
/wp-content/themes/index.php

# Ignore these plugins from the core
wp-content/plugins/hello.php
wp-content/plugins/akismet/
/wp-content/plugins/index.php

# Ignore specific themes
wp-content/themes/twenty*/

# Mac OS custom attribute store and thumbnails
*.DS_Store
._*

# Error logs
error_log.txt
wordfence-waf.php
wflogs

# Databases and exports
latest_dump.tar.gz
latest_dump
