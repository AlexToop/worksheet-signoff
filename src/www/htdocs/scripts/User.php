<?php


/*
 *  User object that is created for everyone entering the website.
 */


class User {
    private $username;
    private $password;
    private $usergroup;
    private $isAuthenticated;


    /**
     * User constructor.
     *
     * @param $username
     * @param $password
     */
    function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }


    /**
     * Verifies the validity of credentials provided.
     */
    function checkLogin() {
        include_once '../../htdocs_private/auth/AberCheckLdap.php';
        $this->isAuthenticated = checkldap($this->username, $this->password);
        //$this->isAuthenticated = true; // todo only for development
    }


    /**
     * Sets the user-group of the user
     *
     * @param $classid
     */
    function checkUserGroup($classid) {
        include '../scripts/DatabaseDAO.php';
        $this->usergroup = null;

        $databaseDao = new DatabaseDAO();
        $usergroup = $databaseDao->getUserGroupForUserInClass($this->username, $classid);
        if($usergroup == 1) {
            $this->usergroup = "student";
        } elseif($usergroup == 2) {
            $this->usergroup = "demonstrator";
        } elseif($usergroup == 3) {
            $this->usergroup = "lecturer";
        } elseif($usergroup == 4) {
            $this->usergroup = "admin";
        }
        $databaseDao->closeConnection();
    }


    /**
     * Gets user-group from object
     *
     * @return mixed
     */
    function getUserGroup() {
        return $this->usergroup;
    }


    /**
     * Gets the authentication status from the object
     *
     * @return mixed
     */
    function getIsAuthenticated() {
        return $this->isAuthenticated;
    }


    /**
     * Gets the username from the object
     *
     * @return mixed
     */
    public function getUsername() {
        return $this->username;
    }


    /**
     * Sets the username of the object
     *
     * @param mixed $usergroup
     */
    public function setUsergroup($usergroup) {
        $this->usergroup = $usergroup;
    }


    /**
     * Sets the authentication status of the object
     *
     * @param mixed $isAuthenticated
     */
    public function setIsAuthenticated($isAuthenticated) {
        $this->isAuthenticated = $isAuthenticated;
    }
}


?>