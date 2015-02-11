<?php
class home {
    
    public function index() {
    }
    
    public function forum($params) {
        app::addToViewParams($params);
    }
    public function post($params) {
        app::addToViewParams($params);
    }
    
}