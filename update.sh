#!/bin/bash
echo "Aggiornamento database polizia in corso...";
cd /var/www/html/database/polizia;
git fetch;
git pull;
echo "Controllo modifiche...";
git status -uno;
echo "Controllo eseguito!";
echo "Aggiunta permesso eseguibile...";
chmod +x ./update.sh
echo "Fatto."
md5=md5sum ./update.sh;
sha1=sha1sum ./update.sh;
echo "MD5: ${md5}";
echo "SHA1: ${sha1}";
