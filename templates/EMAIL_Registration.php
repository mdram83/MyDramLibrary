<html>
<head></head>
<body>
    Hello.
    <br>
    To confirm your registration please click on link below:
    <br>
    <a href="<?php echo \MyDramLibrary\Configuration\UrlConfiguration::BASE_HREF; ?>index.php?module=Registration&action=verify&id=
    <?php echo $data['id']; ?>&hash=<?php echo $data['hash']; ?>">
        <?php echo \MyDramLibrary\Configuration\UrlConfiguration::BASE_HREF; ?>index.php?module=Registration&action=verify&id=
        <?php echo $data['id']; ?>&hash=<?php echo $data['hash']; ?>
    </a>
    <br>
    Thank you!
    <br>
</body>
</html>