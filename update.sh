#!/bin/bash
echo "Aggiornamento database polizia in corso...";
cd /var/www/html/database/polizia;
sudo git fetch;
sudo git pull;
echo "Controllo modifiche...";
sudo git status -uno;
echo "Controllo eseguito!";
echo "Aggiunta permesso eseguibile...";
sudo chmod +x ./update.sh
echo "Fatto."
md5=md5sum ./update.sh;
sha1=sha1sum ./update.sh;
echo "MD5: ${md5}";
echo "SHA1: ${sha1}";
