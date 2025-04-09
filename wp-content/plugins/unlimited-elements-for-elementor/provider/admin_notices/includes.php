<?php

if ( ! defined( 'ABSPATH' ) ) exit;

$path = dirname(__FILE__) . '/';

require_once $path . 'builders/builder_abstract.class.php';
require_once $path . 'builders/builder.class.php';
require_once $path . 'builders/banner_builder.class.php';

require_once $path . 'notices/notice_abstract.class.php';

require_once $path . 'notices/bf_banner.class.php';
//require_once $path . 'notices/simple_example.class.php';
require_once $path . 'notices/doubly.class.php';
//require_once $path . 'notices/black_friday.class.php';
//require_once $path . 'notices/rating.class.php';

require_once $path . 'manager.class.php';
require_once $path . 'options.class.php';
require_once $path . 'notices.class.php';
