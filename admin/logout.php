<?php
setcookie('auth', '', time() - 10);
header('Location: ./');
exit;
