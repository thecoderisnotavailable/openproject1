<?php

/**
 * user class
 */
class User
{
    private $userid;
    private $access = array();

    function __construct()
    {
        $this->userid = $_SESSION['user']['id'];
    }
    /**
     * fname, dp, settings
     */
    public function get_user_details()
    {
        $user = DB::queryFirstRow("SELECT fname, dp, settings FROM userdata WHERE uid=%i", $this->userid);
        return $user;
    }
    /**
     * uname, email, phone, status
     */
    public function get_user_login()
    {
        $user = DB::queryFirstRow("SELECT uname, email, phone, status FROM userdata WHERE id=%i", $this->userid);
        return $user;
    }
    /**
     * retuns fname
     */
    public function get_user_fname()
    {
        $user = DB::queryFirstRow("SELECT fname FROM userdata WHERE uid=%i", $this->userid);
        return $user['fname'];
    }
    /**
     * user id
     */
    public function get_user_id()
    {
        return $this->userid;
    }
    /**
     * profile pic location: NOT RECOMMENDED
     */
    public function get_pic()
    {
        $user = DB::queryFirstRow("SELECT dp FROM userdata WHERE uid=%i", $this->userid);
        return $user['dp'];
    }
}
