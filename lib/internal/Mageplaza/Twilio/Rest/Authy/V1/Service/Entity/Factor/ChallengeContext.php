<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Authy\V1\Service\Entity\Factor;

use Twilio\Exceptions\TwilioException;
use Twilio\InstanceContext;
use Twilio\Options;
use Twilio\Values;
use Twilio\Version;

/**
 * PLEASE NOTE that this class contains preview products that are subject to change. Use them with caution. If you currently do not have developer preview access, please contact help@twilio.com.
 */
class ChallengeContext extends InstanceContext {
    /**
     * Initialize the ChallengeContext
     *
     * @param \Twilio\Version $version Version that contains the resource
     * @param string $serviceSid Service Sid.
     * @param string $identity Unique identity of the Entity
     * @param string $factorSid Factor Sid.
     * @param string $sid A string that uniquely identifies this Challenge, or
     *                    `latest`.
     * @return \Twilio\Rest\Authy\V1\Service\Entity\Factor\ChallengeContext
     */
    public function __construct(Version $version, $serviceSid, $identity, $factorSid, $sid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = array(
            'serviceSid' => $serviceSid,
            'identity' => $identity,
            'factorSid' => $factorSid,
            'sid' => $sid,
        );

        $this->uri = '/Services/' . \rawurlencode($serviceSid) . '/Entities/' . \rawurlencode($identity) . '/Factors/' . \rawurlencode($factorSid) . '/Challenges/' . \rawurlencode($sid) . '';
    }

    /**
     * Deletes the ChallengeInstance
     *
     * @return boolean True if delete succeeds, false otherwise
     * @throws TwilioException When an HTTP error occurs.
     */
    public function delete() {
        return $this->version->delete('delete', $this->uri);
    }

    /**
     * Fetch a ChallengeInstance
     *
     * @return ChallengeInstance Fetched ChallengeInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch() {
        $params = Values::of(array());

        $payload = $this->version->fetch(
            'GET',
            $this->uri,
            $params
        );

        return new ChallengeInstance(
            $this->version,
            $payload,
            $this->solution['serviceSid'],
            $this->solution['identity'],
            $this->solution['factorSid'],
            $this->solution['sid']
        );
    }

    /**
     * Update the ChallengeInstance
     *
     * @param array|Options $options Optional Arguments
     * @return ChallengeInstance Updated ChallengeInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function update($options = array()) {
        $options = new Values($options);

        $data = Values::of(array('AuthPayload' => $options['authPayload'], ));

        $payload = $this->version->update(
            'POST',
            $this->uri,
            array(),
            $data
        );

        return new ChallengeInstance(
            $this->version,
            $payload,
            $this->solution['serviceSid'],
            $this->solution['identity'],
            $this->solution['factorSid'],
            $this->solution['sid']
        );
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString() {
        $context = array();
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Authy.V1.ChallengeContext ' . \implode(' ', $context) . ']';
    }
}