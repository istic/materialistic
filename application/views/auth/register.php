

<form action="register" method="POST" class="signupform">
    <div class="row contentblock">
        <div class="col-md-7 col-md-push-2 whiteblock">
            <?php echo validation_errors(); ?>

            Dear <?PHP echo APPNAME ?>,<br/>
            <p style="text-indent:3em;">
                Hi there, I'd like to make an application to join your carefully constructed website. 
                I've read your <a href="/docs/terms" target="_blank">Terms &amp; Conditions</a>, and they seem fairly reasonable, and I reassure you I'm not
                to going use the account you're about to grant me to defraud or grief other people, 
                nor to deliberately spoil their life or gameplay with it; and generally I promise not to be a dick on this site.
            </p>

            <p>
                For your records, you can contact me on my email address, which is 
                <input type="text" placeholder="Your Email Address" value="<?PHP echo set_value('email') ?>" name="email">, although only if you promise only 
                to use it to validate my account and for security purposes. I'll choose what other forms 
                of email you can send me later.
            </p>

            <p>
                When I log in, I'd like to use the password <input type="password" placeholder="Password" name="password" value="">, 
                which I promise not to tape to my monitor on a postit note. I appreciate you're not demanding
                I retype that, and have carefully double checked both that and my email address so that I
                don't have to email support later.</p>

            <p>
                My preferred currency that I'd like my report in is
                <?PHP $default_home_currency = set_value('home_currency', "USD"); ?>
                <select name="home_currency">
                    <?PHP
                    $currencies = array('USD', 'GBP', 'CAD');
                    foreach ($currencies as $home_currency ) {
                        if ($home_currency == $default_home_currency) {
                            $selected = " SELECTED";
                        } else {
                            $selected = "";
                        }
                        printf("\t<option value=\"%s\"%s>%s</option>\n", $home_currency, $selected, $home_currency);
                    }
                    ?>
                </select></p>


            <p>
                Lastly, when you display stuff about me, please use the pronoun 
                <?PHP $default_pronoun = set_value('pronoun', "GENERIC"); ?>
                <select name="pronoun">
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
                .</p>

            <p>
                I am now finding this signup form device tired, and so will close off.</p>

            <p>Yours faithfully, </p>

            <input type="text" placeholder="Preferred Display Name" name="name" id="name" value="<?PHP echo set_value('name') ?>" class="cursive">

            <div style="clear: both;">&nbsp;</div>
            <input type="submit" class="btn pull-right btn-primary" value="Send This" />

            <div style="clear: both;">&nbsp;</div>
        </div>
    </div>
</form>
