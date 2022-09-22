function addStyle(element, styleName) {
    element.className = element.className + ' ' + styleName;
}

function setStyle(element, styleName) {
    element.className = styleName;
}

function checkUsernameInput() {

    var inputElement = document.getElementById('username');
    var errorElement = document.getElementById('invalidUsernameComment');

    inputElement.setCustomValidity('');
    inputElement.checkValidity();

    if (!inputElement.validity.valid) {
        setStyle(inputElement, 'registration');
        addStyle(inputElement, 'alerted');
        errorElement.style.display = 'inline-block';
        errorElement.innerHTML = 'Use only letters and/or numbers';
        return;
    }

    ajaxExistingUsernameCheck();

    if (inputElement.validity.valid) {
        setStyle(inputElement, 'registration');
        errorElement.style.display = 'none';
        errorElement.innerHTML = '';
        return;
    }    
}

function ajaxExistingUsernameCheck() {
    var inputElement = document.getElementById('username');
    var errorElement = document.getElementById('invalidUsernameComment');
    var xhttp = new XMLHttpRequest();
   
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText == 1) {
                setStyle(inputElement, 'registration');
                addStyle(inputElement, 'existing');
                inputElement.setCustomValidity('Username already in use');
                errorElement.style.display = 'inline-block';
                errorElement.innerHTML = 'Username already in use';
                return;
            }
        }
    };
    
    xhttp.open("POST", "?module=AjaxRegistrationSupport&action=userExists", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhttp.send("user=" + inputElement.value);
}