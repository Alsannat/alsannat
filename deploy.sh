sudo php bin/magento maintenance:enable
sleep 2
sudo rm -rf generated/* &&  sudo rm -rf pub/static/* && sudo rm -rf var/view_preprocessed && sudo rm -rf var/cache && sudo rm var/page_cache
sleep 2
sudo php -d memory_limit=4G bin/magento setup:upgrade
sleep 4
sudo php bin/magento setup:di:compile
sleep 4
sudo php -d memory_limit=4G bin/magento setup:static-content:deploy -f en_US ar_SA
sleep 4
sudo php -d memory_limit=4G bin/magento cache:enable
sudo chmod 777 var/ pub/static generated/ -R
sudo chown ubuntu.www-data var/ pub/static generated/ -R
sudo php bin/magento maintenance:disable