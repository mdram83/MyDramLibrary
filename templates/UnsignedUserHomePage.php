<div class="wrapper">
 
    <div class="logoTop">
        <a class="logoTop" href="">My Library</a>
    </div>

    <div class="registration">

       
        <div class="registrationTitle">Log in</div>

        <?php if (isset($data['failedLogin'])): ?>
            <div class="loginFailed"><?= $data['failureMessage']; ?></div>
        <?php endif ?>

        <form method="POST" action="?action=login">

            <input id="email" name="email" 
                class="registration" 
                type="email"  
                maxlength="255" 
                placeholder="Email address..."
                onfocusout ="
                    document.getElementById('invalidEmailComment').style.display = 'none';
                    this.checkValidity();" 
                oninvalid="
                    setStyle(this, 'registration');
                    addStyle(this, 'alerted');
                    document.getElementById('invalidEmailComment').style.display = 'inline-block';"
                value="
                <?php
                if (isset($data['failedLogin'])) {
                    echo $data['email'];
                }
                ?>" 
                required>

            <div id="invalidEmailComment" class="invalidInputComment">Provide your email address</div>

            <input id="password" name="password" 
                class="registration" 
                type="password" 
                placeholder="Password..." 
                onfocusout ="
                    document.getElementById('invalidPasswordComment').style.display = 'none';
                    this.checkValidity();" 
                oninvalid="
                    setStyle(this, 'registration');
                    addStyle(this, 'alerted');
                    document.getElementById('invalidPasswordComment').style.display = 'inline-block';"
                required>

            <div id="invalidPasswordComment" class="invalidInputComment">Provide your password</div>

            <input class="login" type="submit" value="Log in">
        </form>

        <div class="registrationAlternative">
            <a class="inherited" href="?module=Registration">Or register new account</a>
        </div>

    </div>

    <div class="dashboard">
        Keep your titles organized and share with your friends.
        <br>
        <br>
        Join our
        <b><?= $data['userCount']; ?> users</b>
        <br>
        from <b><?= $data['cityCount']; ?> cities </b>
        <br>
        storing <b><?= $data['titleCount']; ?> titles </b>
    </div>
</div>