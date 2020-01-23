<?php
/**
 * Function to determine if a user can be authenticated against the
 * LDAP server.
 *
 * @param $username The username, e.g. abc01.
 * @param $password The plain text version of the user's password
 * @return 1 (true) if the user details match the LDAP record. Otherwise, 0 (false) is
 *         returned.
 * @author Sandy Spence (axs@aber.ac.uk)
 */
function checkldap($username, $password) {
    $ldap = ldap_connect("ldap.dcs.aber.ac.uk") or
    die ('Failed to connect to ldap server: error was ' . ldap_error($ldap));
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    $params = "uid=$username,ou=People,dc=dcs,dc=aber,dc=ac,dc=uk";

    if(@ldap_bind($ldap, $params, $password)) {
        return 1;
    } else {
        return 0;
    }
}

?>