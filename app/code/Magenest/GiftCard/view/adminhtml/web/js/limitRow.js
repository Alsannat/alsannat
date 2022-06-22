/**
 * Created by thuy on 18/06/2017.
 */

function setLimitBGForGiftCard()
{
    var trCnt = jQuery('div[data-index="container_design"]').find('table[data-index="link"]').find('tbody').find('tr');

    if (trCnt.length >= 1) {

        jQuery('div[data-index="container_design"]').find('table[data-index="link"]').find('tfoot').hide();
    } else {

        jQuery('div[data-index="container_design"]').find('table[data-index="link"]').find('tfoot').show();

    }
}

setInterval(setLimitBGForGiftCard,100);