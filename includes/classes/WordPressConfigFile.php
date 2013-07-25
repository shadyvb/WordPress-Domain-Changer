<?php

class WordPressConfigFile
{


    /**
     * Absolute path of the wp-config.php file.
     *
     * @var mixed
     */
    private $path = null;

    /**
     * File contents of the wp-config.php file.
     *
     * @var mixed
     */
    private $contents = null;

    /**
     * An instance of mysqli initializted using config options.
     *
     * @var mixed; mysqli
     */
    private $db = null;


    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct($path = null) {
        $this->path = $path ? $path : $this->findConfigFilePath();
    }

    /**
     * Returns the path.
     *
     * @return string
     */
    function getPath() {
        return $this->path;
    }

     /**
     * Returns the contents.
     *
     * @return string
     */
    function getContents() {
        if(!$this->contents && $this->exists()) {
            $this->contents = file_get_contents($this->getPath());
        }
        return $this->contents;
    }

    /**
     * Returns true if a wp-config.php file was found.
     *
     * @return boolean
     */
    function exists() {
        return file_exists($this->getPath());
    }

    /**
     * Returns the absolute path to the first instance of a wp-config.php file going up the directory tree.
     *
     * @return mixed; false if file not found.
     */
    public static function findConfigFilePath() {
        $directory = dirname(__FILE__);
        while($directory != '/') {
            if(in_array("wp-config.php", scandir($directory))) {
                return realpath($directory . 'wp-config.php');
            }
            $directory = realpath($directory . '/../');
        }
        return false;
    }

    /**
     * Gets a constant's value from the wp-config.php file (if loaded).
     *
     * @return mixed; false if not found.
     */
    public function getConstant($constant) {
        if($this->getContents()) {
            preg_match("!define\('".$constant."',[^']*'(.+?)'\);!", $this->contents, $matches);
            return (isset($matches[1])) ? $matches[1] : false;
        }
        return false;
    }

    /**
     * Gets $table_prefix value from the wp-config.php file (if loaded).
     *
     * @return string;
     */
    public function getTablePrefix() {
        if($this->getContents()) {
            preg_match("!table_prefix[^=]*=[^']*'(.+?)';!", $this->contents, $matches);
            return (isset($matches[1])) ? $matches[1] : '';
        }
        return '';
    }

    /**
     * Returns a mysqli instance initilized using wp-config settings.
     *
     * @return mixed; mysqli on success, false on error.
     */
    public function db() {
        if(!$this->db) {
            $host       = $this->getConstant('DB_HOST');
            $user       = $this->getConstant('DB_USER');
            $password   = $this->getConstant('DB_PASSWORD');
            $database   = $this->getConstant('DB_NAME');
            $this->db = @new mysqli($host, $user, $password, $database);
            if(mysqli_connect_error()) return false;
        }
        return $this->db;
    }

}