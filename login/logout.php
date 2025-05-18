<?php

setcookie("token", "", time() - 3600, "/", "", true, true);

header("Location: ../pawfect/login/index.php");
exit();