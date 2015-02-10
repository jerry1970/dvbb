<?php
class home {
    
    public function index() {
    }
    
    public function forum($params) {
        dvbb::addToViewParams($params);
    }
    public function post($params) {
        dvbb::addToViewParams($params);
    }
    
}