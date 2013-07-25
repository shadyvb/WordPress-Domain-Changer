<?php
class Controller
{

    protected $events = array();

    public function addError($msg, $option = null) {
        $this->events[] = new Controller_Event('error', $msg, $options);
    }

    public function addNotice($msg, $option = null) {
        $this->events[] = new Controller_Event('notice', $msg, $options);
    }

    public function addAction($msg, $option = null) {
        $this->events[] = new Controller_Event('action', $msg, $options);
    }

    public function getEvents() {
        return $this->events;
    }

    public function render() {

    }

    public function redirect($url) {

    }

}