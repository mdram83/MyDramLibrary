var myLibraryAuthors = new Array(0);

function addStyle(element, styleName) {
    element.className = element.className + ' ' + styleName;
}

function setStyle(element, styleName) {
    element.className = styleName;
}

function changeIsbnButtonStyle(state) {
    var button = document.getElementById('isbnCheck');
    switch (state) {
        case 'loading':
            button.disabled = true;
            button.textContent = "Loading...";
            button.style.opacity = 0.5;
            button.style.cursor = "progress";
            break;
        case 'success':
            button.disabled = true;
            button.textContent = "Loaded";
            button.style.opacity = 0.8;
            button.style.cursor = "default";
            break;
        case 'failed':
            button.disabled = true;
            button.textContent = "Not found";
            button.style.opacity = 0.8;
            button.style.cursor = "not-allowed";
            break;
        case 'enabled':
            button.disabled = false;
            button.textContent = "Get details";
            button.style.opacity = 1.0;
            button.style.cursor = "pointer";
            break;
    }
}

function addCategoryField() {
    var countHiddenElements = 0;
    var firstHiddenElement = null;

    for (i = 0; i < 10; i++) {
        var element = document.getElementById('category0' + i);
        if (element.style.display == 'none') {
            if (countHiddenElements == 0) {
                firstHiddenElement = element;
            }
            countHiddenElements++;
        }
    }
    if (firstHiddenElement != null) {
        firstHiddenElement.style.display = 'inherit';
        firstHiddenElement.focus();
    }
    if (countHiddenElements <= 1) {
        document.getElementById('moreCategories').style.display = 'none';
    }

}

function ajaxCreateCategoryDatalist() {

    var categoriesDatalist = document.getElementById('categories');
    if (categoriesDatalist.options.length === 0) {

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
    
                var categoriesJSON = JSON.parse(this.responseText);
                for (var i = 0; i < categoriesJSON.length; i++) {
                    var option = document.createElement('option');
                    option.value = categoriesJSON[i]['category'];
                    categoriesDatalist.appendChild(option);
                }
                return;
            }
        };
        
        xhttp.open("POST", "?module=AjaxCatalogList&action=getCategoryList", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhttp.send();

    }
    
}

function ajaxCreatePublisherDatalist() {

    var publishersDatalist = document.getElementById('publishers');
    if (publishersDatalist.options.length === 0) {

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
    
                var publishersJSON = JSON.parse(this.responseText);
                for (var i = 0; i < publishersJSON.length; i++) {
                    var option = document.createElement('option');
                    option.value = publishersJSON[i]['publisher'];
                    publishersDatalist.appendChild(option);
                }
                return;
            }
        };
        
        xhttp.open("POST", "?module=AjaxCatalogList&action=getPublisherList", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhttp.send();

    }
    
}

function ajaxGetDetailsWithISBN() {

    var isbnField = document.getElementById('isbn');
    var isbnValue = isbnField.value;
    if (isbnValue != '') {

        changeIsbnButtonStyle('loading');
        var params = 'isbn=' + encodeURIComponent(isbnValue);
        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
    
                var details = JSON.parse(this.responseText);
                console.log(this.responseText); // temp
                var fields = new Array('title', 'authorFirstname', 'authorLastname', 'isbn', 'publisher', 'series', 'volume', 'pages');
                for (var i = 0; i < fields.length; i++) {
                    document.getElementById(fields[i]).value = details[fields[i]] ?? document.getElementById(fields[i]).value;
                }

                var categories = details['category'] ?? null;
                if (categories !== null) {
                    for (i = 0; i < categories.length; i++) {
                        var element = document.getElementById('category0' + i);
                        if (element.style.display == 'none') addCategoryField();
                        element.value = details['category'][i];
                        if (i == 9) break;
                    }
                }

                changeIsbnButtonStyle('success');
                return;
            }
            if (this.readyState == 4 && (this.status == 404 || this.status == 500)) {
                changeIsbnButtonStyle('failed');
                return;
            }

        };
        
        xhttp.open("POST", "?module=AjaxExternalAPIRouter&action=getDetailsWithISBNOpenlibrary", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhttp.send(params);

    } else {
        isbnField.focus();
        alert("Please provide ISBN number first");
    }
    
}

function ajaxCreateAuthorDatalist(input) {

    var authorFirstnamesDatalist = document.getElementById('authorFirstnames');
    var authorLastnamesDatalist = document.getElementById('authorLastnames');
    if (authorFirstnamesDatalist.options.length === 0 && authorLastnamesDatalist.options.length === 0) {

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
    
                var authorsJSON = JSON.parse(this.responseText);

                var authorsFirstnames = Object.values(authorsJSON['firstname']);
                for (var i = 0; i < authorsFirstnames.length; i++) {
                    authorFirstnamesDatalist.append(new Option(authorsFirstnames[i], authorsFirstnames[i]));
                }

                var authorsLastnames = Object.values(authorsJSON['lastname']);
                for (var i = 0; i < authorsLastnames.length; i++) {
                    authorLastnamesDatalist.append(new Option(authorsLastnames[i], authorsLastnames[i]));
                }

                var authors = myLibraryAuthors = Object.values(authorsJSON['author']);
                for (var i = 0; i < authors.length; i++) {
                    authorFirstnamesDatalist.append(new Option(authors[i]['firstname'], authors[i]['authorName']));
                    authorLastnamesDatalist.append(new Option(authors[i]['lastname'], authors[i]['authorName']));
                }

                return;
            }
        };
        
        xhttp.open("POST", "?module=AjaxCatalogList&action=getAuthorNames", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhttp.send();

        input.focus();
    }
    
}

function useSelectedAuthor(input) {
    console.log(myLibraryKeypress);
    var selectedAuthor = input.value;
    var options = document.getElementById(input.list.id).childNodes
    for (i = 0; i < options.length; i++) {
        if(selectedAuthor == options[i].value) {
            if (options[i].value != options[i].label) {
                for (j = 0; j < myLibraryAuthors.length; j++) {
                    if (selectedAuthor == myLibraryAuthors[j]['authorName']) {
                        document.getElementById('authorFirstname').value = myLibraryAuthors[j]['firstname'];
                        document.getElementById('authorLastname').value = myLibraryAuthors[j]['lastname'];
                        return;
                    }
                }
            }
        }
    }

}