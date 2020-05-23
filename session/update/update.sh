#!/bin/bash
echo "Aggiornamento database polizia in corso...";
cd /var/www/html/database/polizia;
git fetch;
git pull;
echo "Controllo modifiche...";
git status -uno;
echo "Controllo eseguito!";
echo "Aggiunta permesso eseguibile...";
sudo chmod +x ./session/update/update.sh
echo "Fatto."
pwd
md5=$(md5sum ./session/update/update.sh);
sha1=$(sha1sum ./session/update/update.sh);
echo "MD5: ${md5}";
echo "SHA1: ${sha1}";
