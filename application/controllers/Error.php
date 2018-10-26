<?php
class ErrorController extends Yaf_Controller_Abstract {

    private $_config;

    public function init(){
        $this->_config = Yaf_Application::app()->getConfig();
    }

    public function errorAction() {
        $exception = $this->getRequest()->getParam('exception');

        $showErrors = $this->_config->application->showErrors;
        $this->_view->trace = ($showErrors) ? $exception->getTraceAsString() : '';
        $this->_view->message = ($showErrors) ? $exception->getMessage() : '';
    
        /*Yaf has a few different types of errors*/
        if($exception instanceof Yaf_Exception_LoadFailed){
            return $this->_pageNotFound();
        }
        return $this->_unknownError();
    }

    private function _pageNotFound(){
        $this->_view->error = 'Page was not found';
    }

    private function _unknownError(){
        $this->_view->error = 'Application Error';
    }
}