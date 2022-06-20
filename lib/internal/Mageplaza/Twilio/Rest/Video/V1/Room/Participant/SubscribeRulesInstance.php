<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Video\V1\Room\Participant;

use Twilio\Deserialize;
use Twilio\Exceptions\TwilioException;
use Twilio\InstanceResource;
use Twilio\Values;
use Twilio\Version;

/**
 * PLEASE NOTE that this class contains beta products that are subject to change. Use them with caution.
 *
 * @property string $participantSid
 * @property string $roomSid
 * @property string $rules
 * @property \DateTime $dateCreated
 * @property \DateTime $dateUpdated
 */
class SubscribeRulesInstance extends InstanceResource {
    /**
     * Initialize the SubscribeRulesInstance
     *
     * @param \Twilio\Version $version Version that contains the resource
     * @param mixed[] $payload The response payload
     * @param string $roomSid The SID of the Room resource for the Subscribe Rules
     * @param string $participantSid The SID of the Participant resource for the
     *                               Subscribe Rules
     * @return \Twilio\Rest\Video\V1\Room\Participant\SubscribeRulesInstance
     */
    public function __construct(Version $version, array $payload, $roomSid, $participantSid) {
        parent::__construct($version);

        // Marshaled Properties
        $this->properties = array(
            'participantSid' => Values::array_get($payload, 'participant_sid'),
            'roomSid' => Values::array_get($payload, 'room_sid'),
            'rules' => Values::array_get($payload, 'rules'),
            'dateCreated' => Deserialize::dateTime(Values::array_get($payload, 'date_created')),
            'dateUpdated' => Deserialize::dateTime(Values::array_get($payload, 'date_updated')),
        );

        $this->solution = array('roomSid' => $roomSid, 'participantSid' => $participantSid, );
    }

    /**
     * Magic getter to access properties
     *
     * @param string $name Property to access
     * @return mixed The requested property
     * @throws TwilioException For unknown properties
     */
    public function __get($name) {
        if (\array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }

        if (\property_exists($this, '_' . $name)) {
            $method = 'get' . \ucfirst($name);
            return $this->$method();
        }

        throw new TwilioException('Unknown property: ' . $name);
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString() {
        return '[Twilio.Video.V1.SubscribeRulesInstance]';
    }
}