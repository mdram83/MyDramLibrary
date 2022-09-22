<html>
<head></head>
<body>
    Hello.
    <br>
    To confirm your registration please click on link below:
    <br>
    <a href="https://127.0.0.1/index.php?module=Registration&action=verify&id=
    <?php echo $data['id']; ?>&hash=<?php echo $data['hash']; ?>">
        https://127.0.0.1/index.php?module=Registration&action=verify&id=
        <?php echo $data['id']; ?>&hash=<?php echo $data['hash']; ?>
    </a>
    <br>
    Thank you!
    <br>
</body>
</html>