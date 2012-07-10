<?php
/**
 * This is an example controller - and also the default one
 * that is called when no controller/action are in the URL.
 */
class StartController extends Controller {

    /**
     * Sample action
     * Puts a message to the view data.
     */
    public function index() {
        $this->set("hello", "Hello, yasp-mf!");
    }

}