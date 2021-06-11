<?php
class acceptanceletter {

        public $core;
        public $view;

        public function configView() {
                $this->view->header = FALSE;
                $this->view->footer = FALSE;
                $this->view->menu = FALSE;
              //  $this->view->javascript = array();
              //  $this->view->css = array();

                return $this->view;
        }

        public function buildView($core) {
                $this->core = $core;
        }

         public function showAcceptanceletter($item) {
          
                echo "
              



                      <form>Enter Student Number here:
                          <input type='text' name='comments' id='student'>
                          <br>
                      </form>
                     

                      <button onclick='clickyClick()'>Generate Acceptance</button>

              

                      <script>
                        function clickyClick() {
                            url = '/sis/acceptancedocument/show/' + document.getElementById('student').value
                            window.open(url, '_blank');
                        }
                      </script>












                    


                ";
        }

        

   


}
?>

