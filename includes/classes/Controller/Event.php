<?php
class Controller_Event
{

    protected $types        = array('error', 'notice', 'action');

    protected $created_at   = null;
    protected $type         = null;
    protected $msg          = null;
    protected $options      = array('html' => false);

    public function __construct($type, $msg, $options = null) {
        $this->created_at = time();
        $this->type       = $type;
        $this->msg        = $msg;
        if(is_array($options)) $this->options = $options;

        if(!$this->isValidType()) throw new Exception( 'Unknown Event Type: "' . $type . '"');
    }

    public function isValidType() {
        return in_array($this->type, $this->types);
    }

    public function getMessage() {
        return $this->isHtmlSafe() ? htmlspecialchars($this->msg) : $this->msg;
    }

    public function isHtmlSafe() {
        (bool) $this->options['html'];
    }

    public function getType() {
        return $this->type;
    }

    public function getOptions() {
        return $this->options;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

}