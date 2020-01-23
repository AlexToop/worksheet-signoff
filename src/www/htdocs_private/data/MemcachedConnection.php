<?php

/**
 * Provides an interface to access Memcached. Similar to the file template for database
 * connections used by Edel Sherratt (eds@aber.ac.uk), however, the content of this file
 * was created by Alexander Toop.
 *
 * @return Memcached
 */
function get_memcached_handle() {

    $memAddress = "127.0.0.1";
    $port = 11211;

    // Displays a message if the environment does not have the Memcached extension.
    if(!class_exists('Memcached')) {
        die("WARNING: Please alert alt28@aber.ac.uk regarding the sign-off queue environment not connecting.");
    }

    $memcache = new Memcached;
    $memcache->addServer($memAddress, $port);
    return $memcache;
}

?>