<?php

// DB 드라이버: mysql 또는 sqlite

define('POSTCODIFY_DB_DRIVER', 'mysql');

// DB 이름: mysql인 경우 DB명을 입력, sqlite인 경우 파일명을 입력할 것

define('POSTCODIFY_DB_DBNAME', '');

// DB 접속 정보: mysql인 경우에만 입력, sqlite인 경우 공백으로 선언할 것

define('POSTCODIFY_DB_HOST', 'localhost');
define('POSTCODIFY_DB_PORT', 3306);
define('POSTCODIFY_DB_USER', '');
define('POSTCODIFY_DB_PASS', '');