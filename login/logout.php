<?php

setcookie("token", "", time() - 3600, "/", "", true, true);

header("Location: index.php");
exit();