<?php

$_SESSION['email'] = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

