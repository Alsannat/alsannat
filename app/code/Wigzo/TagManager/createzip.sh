PKG="Wigzo-1.0.0"
rm "$HOME/%PKG.zip"
zip -r "$HOME/$PKG.zip" * -x createzip.sh -x validate_m2_package.php
cd ..
php TagManager/validate_m2_package.php "$HOME/$PKG.zip"
