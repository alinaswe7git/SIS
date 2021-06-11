<?php
class test {

        public $core;
        public $view;

        public function configView() {
                $this->view->header = TRUE;
                $this->view->footer = TRUE;
                $this->view->menu = TRUE;
                $this->view->javascript = array();
                $this->view->css = array();

                return $this->view;
        }

        public function buildView($core) {
                $this->core = $core;
        }

        public function showTest($item) {
			
			
		echo "ok "; 
		//include $this->core->conf['conf']['formPath'] . "editcourse.form.php";
	}
}
?>
