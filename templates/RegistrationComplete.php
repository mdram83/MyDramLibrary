<div class="wrapper">
 
    <div class="logoTop">
        <a class="logoTop" href="">My Library</a>
    </div>

    <div class="registration">

        <div class="registrationTitle">
            <?php echo $data['failed'] ? 'Something went wrong...' : 'Registration completed'; ?>
        </div>

        <div class="registrationAlternative">
            <?php
                echo $data['failed'] ?
                    '<b>' . $data['message'] . '</b>
                    <br><br>
                    <a class="inherited" href="?module=Registration">Register</a> | 
                    <a class="inherited" href="">Log in</a> | 
                    <a class="inherited" href="">Reset password</a>'
                :
                    'Thank you for registering!<br>
                    <b>Please verify your account with link sent to your email</b>.
                    <br><br>
                    <a class="inherited" href="">Go back to home page</a>'
                ;
            ?>
        </div>
        
    </div>

</div>