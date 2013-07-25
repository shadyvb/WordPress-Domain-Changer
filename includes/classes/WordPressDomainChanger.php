<?php
class WordPressDomainChanger
{

    /**
     * Actions that occurred during request.
     *
     * @var array
     */
    public $actions = array();

    /**
     * Notices that occurred during request.
     *
     * @var array
     */
    public $notices = array();

    /**
     * Errors that occurred during request.
     *
     * @var array
     */
    public $errors = array();

    /**
     * An instance of WordPressConfigFile class
     *
     * @var mixed; WordPressConfigFile
     */
    public $config = null;

    /**
     * An instance of the mysqli class
     *
     * @var mixed; mysqli
     */
    public $db = null;


    /**
     * Return an instance of WordPressConfigFile
     *
     * @return WordPressConfigFile;
     */
    public function getConfig() {
        if(!$this->config) $this->config = new WordPressConfigFile();
        return $this->config;
    }


    /**
     * Returns the best guess of the "New Domain" based on this file's location at runtime.
     *
     * @return string;
     */
    public function getNewDomain() {
        $new_domain = str_replace('http://','', $_SERVER['SERVER_NAME']);
        if(isset($_SERVER['SERVER_PORT']) && strlen($_SERVER['SERVER_PORT']) > 0 && $_SERVER['SERVER_PORT'] != 80) {
            $new_domain .= ':'.$_SERVER['SERVER_PORT'];
        }
        return $new_domain;
    }


    /**
     * Returns the "siteurl" WordPress option (if possible).
     *
     * @return mixed; false if not found.
     */
    public function getOldDomain() {
        $config = $this->getConfig();
        if($config->exists() && $config->db()) {
            $options_table  = $config->getTablePrefix() . 'options';
            $result         = $config->db()->query('SELECT * FROM ' . $options_table . ' WHERE option_name="siteurl" LIMIT 1;');
            if(is_object($result) && ($result->num_rows > 0)) {
                $row = $result->fetch_assoc();
                return str_replace('http://', '', $row['option_value']);
            } else {
                $this->error[] = 'The option_name "siteurl" does not exist in the "' . $options_table . '" table!';
            }
        }
        return false;
    }

    // Request Helpers Below

    public function isPasswordDefault() {
        return DDWPDC_PASSWORD == 'Replace-This-Password';
    }

    public function isAuthenticationRequest() {
        return isset($_POST['auth_password']);
    }

    public function isCorrectPassword($password) {
        return md5($password) == md5(DDWPDC_PASSWORD);
    }

    public function startAuthenticatedSession() {
        $expire = time() + DDWPDC_COOKIE_LIFETIME;
        setcookie(DDWPDC_COOKIE_NAME_AUTH, md5(DDWPDC_PASSWORD), $expire);
        setcookie(DDWPDC_COOKIE_NAME_EXPIRE, $expire, $expire);
    }

    public function isAuthenticatedSession() {
        return (isset($_COOKIE[DDWPDC_COOKIE_NAME_AUTH]) && ($_COOKIE[DDWPDC_COOKIE_NAME_AUTH] == md5(DDWPDC_PASSWORD))) ? true : false;
    }


    /**
     * Enhanced version of preg_quote() that works properly in PHP < 5.3
     *
     * @param string;
     * @param mixed; string, null default
     * @return string;
     */
    public static function preg_quote($string, $delimiter = null) {
        $string = preg_quote($string, $delimiter);
        if(phpversion() < 5.3) $string = str_replace('-', '\-', $string);
        return $string;
    }

}