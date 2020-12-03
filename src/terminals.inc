<?php

static $_terminals = array(
    "/^(root)/" => "T_ROOT",
    "/^(map)/" => "T_MAP",
    "/^(\s+)/" => "T_WHITESPACE",
    "/^(\/[A-Za-z0-9\/:]+[^\s])/" => "T_URL",
    "/^(->)/" => "T_BLOCKSTART",
    "/^(::)/" => "T_DOUBLESEPARATOR",
    "/^(\w+)/" => "T_IDENTIFIER",
);

