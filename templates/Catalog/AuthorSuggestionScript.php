<script>

    let myLibraryKeypress = false;

    document.getElementById("authorFirstname").addEventListener("keydown", (e) => {
        if(e.key) {
            myLibraryKeypress = true;
        }
    });

    document.getElementById("authorFirstname").addEventListener('input', (e) => {
        if (myLibraryKeypress === false) {
            useSelectedAuthor(document.getElementById('authorFirstname'));
        }
        myLibraryKeypress = false;
    });

    document.getElementById("authorLastname").addEventListener("keydown", (e) => {
        if(e.key) {
            myLibraryKeypress = true;
        }
    });

    document.getElementById("authorLastname").addEventListener('input', (e) => {
        if (myLibraryKeypress === false) {
            useSelectedAuthor(document.getElementById('authorLastname'));
        }
        myLibraryKeypress = false;
    });

</script>