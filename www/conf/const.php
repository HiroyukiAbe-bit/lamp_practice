<?php

define('MODEL_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../model/ec_site/');
define('VIEW_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../view/ec_site/');


define('IMAGE_PATH', '/ec_site/assets/images/');
define('STYLESHEET_PATH', '/ec_site/assets/css/');
define('IMAGE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/ec_site/assets/images/' );

define('DB_HOST', 'localhost');
define('DB_NAME', 'ec_site');
define('DB_USER', 'root');
define('DB_PASS', 'Wjtr8Il0n');
define('DB_CHARSET', 'utf8');

define('SIGNUP_URL', '/ec_site/signup.php');
define('LOGIN_URL', '/ec_site/login.php');
define('LOGOUT_URL', '/ec_site/logout.php');
define('HOME_URL', '/ec_site/index.php');
define('CART_URL', '/ec_site/cart.php');
define('HISTORY_URL', '/ec_site/history.php');
define('FINISH_URL', '/ec_site/finish.php');
define('ADMIN_URL', '/ec_site/admin.php');


define('REGEXP_ALPHANUMERIC', '/\A[0-9a-zA-Z]+\z/');
define('REGEXP_POSITIVE_INTEGER', '/\A([1-9][0-9]*|0)\z/');


define('USER_NAME_LENGTH_MIN', 6);
define('USER_NAME_LENGTH_MAX', 100);
define('USER_PASSWORD_LENGTH_MIN', 6);
define('USER_PASSWORD_LENGTH_MAX', 100);

define('USER_TYPE_ADMIN', 1);
define('USER_TYPE_NORMAL', 2);

define('ITEM_NAME_LENGTH_MIN', 1);
define('ITEM_NAME_LENGTH_MAX', 100);

define('ITEM_STATUS_OPEN', 1);
define('ITEM_STATUS_CLOSE', 0);

define('PERMITTED_ITEM_STATUSES', array(
  'open' => 1,
  'close' => 0,
));

define('PERMITTED_IMAGE_TYPES', array(
  IMAGETYPE_JPEG => 'jpg',
  IMAGETYPE_PNG => 'png',
));

//ページネーション用の1ページの表示数定数
define('MAX_VIEW',8);