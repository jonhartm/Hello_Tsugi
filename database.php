<?php

// Uninstall this tool
$DATABASE_UNINSTALL = array(
  "DROP TABLE IF EXISTS {$CFG->dbprefix}helloTusgi"
);

$DATABASE_INSTALL = array(
array( "{$CFG->dbprefix}helloTsugi",
  "CREATE TABLE {$CFG->dbprefix}helloTsugi (
    link_id     INTEGER NOT NULL,
    user_id     INTEGER NOT NULL,
    guesses     SMALLINT,
    correct     BOOLEAN,

    UNIQUE(link_id, user_id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8")
);
?>
