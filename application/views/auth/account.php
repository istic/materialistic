<div class="row contentblock">
    <div class="col-md-4 col-md-push-4">
        <?php echo validation_errors(); ?>
        <form role="form" action="account" method="POST">
            <p>If you're looking to change your password, <a href="change_password">wander over here</a> instead</p>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" placeholder="Name" name="name" id="name" value="<?PHP echo set_value('name', $user->name) ?>"/>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" placeholder="Email" name="email" id="email" value="<?PHP echo set_value('email', $user->email) ?>"/>
            </div>


            <div class="form-group">
                <label for="pronoun">Pronoun</label>
                <?PHP $default_pronoun = set_value('pronoun', $user->pronoun); ?>
                <select name="pronoun" class="form-control">
                    <?PHP
                    foreach ($pronouns as $pronoun => $description) {
                        if ($pronoun == $default_pronoun) {
                            $selected = " SELECTED";
                        } else {
                            $selected = "";
                        }
                        printf("\t<option value=\"%s\"%s>%s</option>\n", $pronoun, $selected, $description);
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="home_currency">Home Currency</label>
                <?PHP $home_currency = set_value('home_currency', $user->home_currency); ?>
                <select name="home_currency" class="form-control">
                    <?PHP
                    $currencies = array('USD', 'GBP', 'CAD');
                    foreach ($currencies as $currency) {
                        if ($currency == $home_currency) {
                            $selected = " SELECTED";
                        } else {
                            $selected = "";
                        }
                        printf("\t<option value=\"%s\"%s>%s</option>\n", $currency, $selected, $currency);
                    }
                    ?>
                </select>
            </div>


            <p>Please enter your current password for verification.</p>
            <div class="form-group">
                <label for="password">Password</label>
                <input class="form-control" type="password" placeholder="Password" name="password" id="password"/>
            </div>

            <div class="formsubmit pull-right">
                <input type="submit" class="btn btn-default" value="Save" />
            </div>
        </form>	

    </div>
</div>