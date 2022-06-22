<?php

namespace Criteo\OneTag\Helper;

/**
 * Description of TagGenerator
 *
 * @author Criteo
 */
class TagGenerator extends \Magento\Framework\App\Helper\AbstractHelper {

    private $ECP = 'magento2';
    private $PLUGIN_VERSION = '1.1.6';
    private $_params = array();
    private $_dataLayer = array();
    private $_allow_methods = array(
        'setAccount',
        'setEmail',
        'setHashedEmail',
        'setSiteType',
        'trackTransaction',
        'viewHome',
        'viewList',
        'viewItem',
        'viewBasket'
    );

    public function __call($method, $args) {
        if (in_array($method, $this->_allow_methods)) {
            return $this->add_param($method, $args);
        }
    }

    public function cto_get_code() {

        $ecp_plugin = $this->ECP . '-' . $this->PLUGIN_VERSION;
        $tag = implode(",\n", $this->_params);

        $code = <<<EOT
	    <!-- CRITEO ONETAG MAGENTO 2 EXTENSION VERSION $this->PLUGIN_VERSION -->
            <!-- START OF CRITEO ONETAG -->
            <script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
            <script type="text/javascript">
                var deviceType = (window.innerWidth <= 767) ? 'm' : (window.innerWidth >= 980) ? 'd' : 't';
                window.criteo_q = window.criteo_q || [];
                window.criteo_q.push({"event": "setSiteType", "type": deviceType, "ecpplugin": "$ecp_plugin"});
                window.criteo_q.push( {$tag} );

            </script>
            <!-- END OF CRITEO ONETAG -->
EOT;

        return $code;
    }

    public function cto_set_dataLayer() {

        //map data layer info to data layer defined spec
        $output = array();
        $event = '';

        foreach ($this->_dataLayer as $row) {
            switch ($row['event']) {
                case 'setEmail':
                    $output['email'] = $row['email'];
                    break;
                case 'viewHome':
                    $event = 'homepage';
                    break;
                case 'viewList':
                    $event = 'listingpage';
                    $output['products'] = $row['item'];
                    break;
                case 'viewItem':
                    $event = 'productpage';
                    $output['products'] = $row['item'];
                    break;
                case 'viewBasket':
                    $event = 'basketpage';
                    $output['products'] = $row['item'];
                    break;
                case 'trackTransaction':
                    $event = 'transactionpage';
                    $output['transactionid'] = $row['id'];
                    $output['products'] = $row['item'];
                    break;
                default:
                    break;
            }
        }

        $dataLayer = json_encode($output);

        $code = <<<EOT
            <script type="text/javascript">
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({
                    "event": "crto_$event",
                    "crto": {$dataLayer}
                });
            </script>
EOT;
        return $code;
    }

    private function add_param($event, $array) {
        $param = array('event' => $event);

        if (count($array) > 0) {
            foreach ($array[0] as $key => $value) {
                $param[$key] = $value;
            }
        }

        $this->_params[] = json_encode($param);
        $this->_dataLayer[] = $param;
    }

}
