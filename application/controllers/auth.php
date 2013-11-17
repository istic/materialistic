<?PHP

class Auth extends MY_Controller {

    var $ssl_page = True;

    public function __construct() {
        parent::__construct();
        $this->viewdata['title'] = "User";
    }

    // Action
    public function register() {
        if ($this->current_user->logged_in()) {
            redirect("/");
            return;
        }
        $this->viewdata['subtitle'] = "Register";
        $this->viewdata['navsection'] = "register";

        $this->load->library('session');
        $this->load->library('form_validation');

        $this->load->model("Pronoun");

        $this->load->helper(array('form', 'url'));


        $this->form_validation->set_rules('name', 'Name', 'required|trim|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|xss_clean|callback_check_email');
        $this->form_validation->set_rules('pronoun', 'Pronoun', 'trim');
        $this->form_validation->set_rules('home_currency', 'Home Currency', 'required|trim');

        $this->viewdata['pronouns'] = shuffle_assoc($this->Pronoun->list_options());

        if ($this->form_validation->run() == FALSE) {
            $this->render('auth/register');
        } else {
            $data = $this->input->post();
            unset($data['signature']);
            $id = $this->User->create_user($data);
            $data['id'] = $id;
            
            $user = new User_Object($data);
            
            $this->session->set_userdata("authorised_user", $id);
            $this->session->set_userdata("user", $user);
            $this->current_user = $user;
            
            $this->validate_email();
            redirect("/");
        }
    }

    // Action
    public function login() {
        $this->viewdata['navsection'] = "login";
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('url');

        $this->viewdata['subtitle'] = "Login";
        if ($this->current_user->logged_in()) {
            redirect("/");
            return;
        }

        $this->form_validation->set_rules('password', 'Password', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|callback_check_auth');
        $this->form_validation->set_rules('redirect_to', 'Redirect', 'trim');

        if ($this->input->get("redirect_to")) {
            $this->viewdata['redirect_to'] = $this->input->get("redirect_to");
        } else {
            $this->viewdata['redirect_to'] = "";
        }

        if ($this->form_validation->run() == FALSE) {
            $this->render('auth/login');
        } else {
            if ($location = $this->input->post("redirect_to")) {
                redirect($location);
            } else {
                redirect("/");
            }
        }
    }

    public function check_email($str) {

        $this->load->model("User");

        if ($this->current_user->logged_in()) {
            $email_in_use = $this->User->email_in_use($str, $this->current_user);
        } else {
            $email_in_use = $this->User->email_in_use($str);
        }
        if ($email_in_use) {
            $this->form_validation->set_message('check_email', 'This email address is already in use');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function check_auth($email) {
        $this->load->model("User");

        $password = $this->input->post("password");
        $valid = $this->User->check_login($email, $password);

        if ($valid) {
            $this->session->set_userdata("authorised_user", $valid->id);
            $this->session->set_userdata("user", $valid);
            return true;
        } else {
            $this->form_validation->set_message('check_auth', "Couldn't log you in with that combination");
            return false;
        }
    }

    public function verify_password($password) {
        $this->load->model("User");

        if ($this->User->hash_password($password) != $this->current_user->password) {
            $this->form_validation->set_message('verify_password', "Password wasn't correct");
            return false;
        } else {
            return true;
        }
    }

    public function logout() {
        $this->requires_authentication();
        $this->session->unset_userdata("authorised_user");
        $this->session->unset_userdata("user");
        redirect("/");
    }

    public function forgot() {
        $this->viewdata['subtitle'] = "Recover Password";

        $this->load->model("User");

        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $this->render('auth/forgot_password');
        } else {
            $user = $this->User->user_by_email($this->input->post("email"));

            if ($user) {

                $uniqid = uniqid();
                $data = array('resetlink' => $this->config->config['secure_url'] . "/auth/reset/" . $uniqid);

                $user->reset_code = $uniqid;
                $user->reset_stamp = date("Y-m-d h:i:s");
                $this->User->save($user->get_data());

                $email = $this->load->view("auth/emails/forgot_password", $data, true);

                // ANd finally, Postmark
                $this->load->library('postmark');
                $this->postmark->to($user->email, $user->name);
                $this->postmark->subject('['.APPNAME.'] Password Reset');
                $this->postmark->tag('Password Reset');
                $this->postmark->message_plain($email);
                $result = $this->postmark->send();
            }

            $this->render("auth/forgot_password_done");
        }
    }

    function reset($code) {
        $this->viewdata['subtitle'] = "Reset Password";

        $this->load->model("User");

        $this->load->library('form_validation');
        
        $user = $this->User->check_reset_code($code);

        $this->viewdata['user'] = $user;

        $this->form_validation->set_rules('password', 'Password', 'required|matches[password2]');
        $this->form_validation->set_rules('password2', 'Password Repeat', 'required');

        if (!$user) {
            $this->render('auth/password_reset_invalid');
        } elseif ($this->form_validation->run() == FALSE) {
            $this->render('auth/password_reset');
        } else {
            $this->User->change_password($user->id, $this->input->post("password"));

            $email = $this->load->view("auth/emails/reset_password", array(), true);

            // ANd finally, Postmark
            $this->load->library('postmark');
            $this->postmark->to($user->email, $user->name);
            $this->postmark->subject('['.APPNAME.'] Password Reset Complete');
            $this->postmark->tag('Password Reset');
            $this->postmark->message_plain($email);
            $this->postmark->send();

            $this->render('auth/password_reset_done');
        }
    }

    function account() {
        $this->viewdata['navsection'] = "account";
        $this->viewdata['Subtitle'] = "Account Details";

        $this->requires_authentication();

        $this->load->model("User");
        $this->load->model("Pronoun");
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('name', 'Name', 'required|trim|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required|callback_verify_password');
        $this->form_validation->set_rules('pronoun', 'Pronoun', 'trim');
        $this->form_validation->set_rules('home_currency', 'Home Currency', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|xss_clean|callback_check_email');
        
        $this->viewdata['pronouns'] = shuffle_assoc($this->Pronoun->list_options());
        $this->viewdata['user'] = $this->current_user;

        if ($this->form_validation->run() == FALSE) {
            $this->render("auth/account");
        } else {
            $data = array(
                'id' => $this->current_user->id,
                'name' => $this->input->post("name"),
                'email' => $this->input->post("email"),
                'home_currency' => $this->input->post("home_currency"),
                'pronoun' => $this->input->post("pronoun")
            );

            $this->User->save($data);
            $this->check_auth($this->input->post("email")); // Log them back in with the new details.

            if ($this->input->post("email") != $this->current_user->email) {
                $this->validate_email();
            }

            redirect("/");
        }
    }

    function change_password() {
        $this->viewdata['navsection'] = "account";
        $this->viewdata['subtitle'] = "Change Password";

        $this->requires_authentication();
        $this->load->model("User");
        $this->load->library('form_validation');
       
        $this->viewdata['user'] = $this->current_user;
        $this->viewdata['banner_text'] = "Change Password";

        $this->form_validation->set_rules('oldpassword', 'Old Password', 'required|callback_verify_password');
        $this->form_validation->set_rules('password', 'New Password', 'required|matches[password2]');
        $this->form_validation->set_rules('password2', 'New Password Repeat', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->render('auth/password_change');
        } else {
            $this->User->change_password($this->current_user->id, $this->input->post("password"));

            $email = $this->load->view("auth/emails/reset_password", array(), true);

            // ANd finally, Postmark
            $this->load->library('postmark');
            $this->postmark->to($this->current_user->email, $this->current_user->name);
            $this->postmark->subject('['.APPNAME.'] Password Change');
            $this->postmark->tag('Password Change');
            $this->postmark->message_plain($email);
            $this->postmark->send();

            $this->session->unset_userdata("authorised_user");
            $this->session->unset_userdata("user");
            $this->render('auth/password_change_done');
        }
    }

    function validate_email() {
        $this->viewdata['navsection'] = "account";
        $this->viewdata['subtitle'] = "Verify Email";

        $this->requires_authentication();

        $this->load->model("User");
        $this->load->model("Notification");

        $user = $this->current_user;

        $uniqid = uniqid();
        $data = array('validatelink' => $this->config->config['secure_url'] . "/auth/validate/" . $uniqid);

        $user->validate_code = $uniqid;
        $user->validated_stamp = NULL;

        $this->User->save($user->get_data());

        $email = $this->load->view("auth/emails/verify_email", $data, true);

        // ANd finally, Postmark
        $this->load->library('postmark');
        $this->postmark->to($user->email, $user->name);
        $this->postmark->subject('['.APPNAME.'] Validate Email Address');
        $this->postmark->tag('Validate Email');
        $this->postmark->message_plain($email);
        $this->postmark->send();

        $this->Notification->send($this->current_user->id, "We've sent a verification link to your email address. Please click on it at some point");

        redirect('/');
    }

    function validate($code) {
        $this->viewdata['subtitle'] = "Verify Email";
        $this->requires_authentication();

        $result = $this->User->verify_email($this->current_user->id, $code);

        if ($result) {
            $this->Notification->send($this->current_user->id, "Email verified, Thanks.");
            redirect('/');
        } else {
            $this->render('auth/email_verify_failed');
        }
    }

}
