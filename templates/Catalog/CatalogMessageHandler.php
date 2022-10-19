    
    <div class="messageWrapper">
        <div class="message <?= ($data['status'] == 1) ? 'messageSuccess' : 'messageError'; ?>"
            onclick="this.style.display = 'none';">

            {:message}

        </div>
    </div>
