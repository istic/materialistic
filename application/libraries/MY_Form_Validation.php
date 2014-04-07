<?PHP
/**
* Class and Function List:
* Function list:
* - __construct()
* Classes list:
* - MY_Form_Validation extends CI_Form_validation
*/

class MY_Form_Validation extends CI_Form_validation {
    public function __construct() {
        parent::__construct();
        $this->set_error_delimiters(' <div class="alert alert-error"><a href="#" class="close" data-dismiss="alert">&times;</a>', '</div>');
    }
}
