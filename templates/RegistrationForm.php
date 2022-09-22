<div class="wrapper">
 
    <div class="logoTop">
        <a class="logoTop" href="">My Library</a>
    </div>

    <div class="registration">

        <div class="registrationTitle">Create new account</div>

        <form method="POST" action="?module=Registration&action=register">
           
            <input id="username" name="username" 
                class="registration" 
                type="text" 
                minlength="1" 
                maxlength="100" 
                pattern="[a-zA-Z0-9]{1,100}" 
                placeholder="Username..." 
                oninput="checkUsernameInput();"  
                required>

            <div id="invalidUsernameComment" class="invalidInputComment"></div>            
           
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
                required>

            <div id="invalidEmailComment" class="invalidInputComment">Use valid email address</div>

            <input id="password" name="password" 
                class="registration" 
                type="password" 
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,}$" 
                placeholder="Password..." 
                maxlength="72" 
                onfocusout ="
                    document.getElementById('invalidPasswordComment').style.display = 'none';
                    this.checkValidity();" 
                oninvalid="
                    setStyle(this, 'registration');
                    addStyle(this, 'alerted');
                    document.getElementById('invalidPasswordComment').style.display = 'inline-block';"
                required>

            <div id="invalidPasswordComment" class="invalidInputComment">8+ characters including upper & lowercase, number and special char</div>

            <div class="confirmServiceTerms">
                By clicking Register you confirm that you've read and agree to our 
                <a class="inherited blueFont" href="">Terms</a>.
            </div>

            <input class="registration" type="submit" value="Register">

        </form>

        <div class="registrationAlternative">
            <a class="inherited" href="">Or log in with existing account</a>
        </div>

    </div>

</div>