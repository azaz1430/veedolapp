<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Trunking\V1\Trunk;

use DateTime;
use Twilio\Deserialize;
use Twilio\Exceptions\TwilioException;
use Twilio\InstanceResource;
use Twilio\Values;
use Twilio\Version;

/**
 * @property string accountSid
 * @property string addressRequirements
 * @property string apiVersion
 * @property boolean beta
 * @property string capabilities
 * @property DateTime dateCreated
 * @property DateTime dateUpdated
 * @property string friendlyName
 * @property array links
 * @property string phoneNumber
 * @property string sid
 * @property string smsApplicationSid
 * @property string smsFallbackMethod
 * @property string smsFallbackUrl
 * @property string smsMethod
 * @property string smsUrl
 * @property string statusCallback
 * @property string statusCallbackMethod
 * @property string trunkSid
 * @property string url
 * @property string voiceApplicationSid
 * @property boolean voiceCallerIdLookup
 * @property string voiceFallbackMethod
 * @property string voiceFallbackUrl
 * @property string voiceMethod
 * @property string voiceUrl
 */
class PhoneNumberInstance extends InstanceResource {
    /**
     * Initialize the PhoneNumberInstance
     * 
     * @param Version $version Version that contains the resource
     * @param mixed[] $payload The response payload
     * @param string $trunkSid The trunk_sid
     * @param string $sid The sid
     * @return PhoneNumberInstance
     */
    public function __construct(Version $version, array $payload, $trunkSid, $sid = null) {
        parent::__construct($version);

        // Marshaled Properties
        $this->properties = array(
            'accountSid' => Values::array_get($payload, 'account_sid'),
            'addressRequirements' => Values::array_get($payload, 'address_requirements'),
            'apiVersion' => Values::array_get($payload, 'api_version'),
            'beta' => Values::array_get($payload, 'beta'),
            'capabilities' => Values::array_get($payload, 'capabilities'),
            'dateCreated' => Deserialize::dateTime(Values::array_get($payload, 'date_created')),
            'dateUpdated' => Deserialize::dateTime(Values::array_get($payload, 'date_updated')),
            'friendlyName' => Values::array_get($payload, 'friendly_name'),
            'links' => Values::array_get($payload, 'links'),
            'phoneNumber' => Values::array_get($payload, 'phone_number'),
            'sid' => Values::array_get($payload, 'sid'),
            'smsApplicationSid' => Values::array_get($payload, 'sms_application_sid'),
            'smsFallbackMethod' => Values::array_get($payload, 'sms_fallback_method'),
            'smsFallbackUrl' => Values::array_get($payload, 'sms_fallback_url'),
            'smsMethod' => Values::array_get($payload, 'sms_method'),
            'smsUrl' => Values::array_get($payload, 'sms_url'),
            'statusCallback' => Values::array_get($payload, 'status_callback'),
            'statusCallbackMethod' => Values::array_get($payload, 'status_callback_method'),
            'trunkSid' => Values::array_get($payload, 'trunk_sid'),
            'url' => Values::array_get($payload, 'url'),
            'voiceApplicationSid' => Values::array_get($payload, 'voice_application_sid'),
            'voiceCallerIdLookup' => Values::array_get($payload, 'voice_caller_id_lookup'),
            'voiceFallbackMethod' => Values::array_get($payload, 'voice_fallback_method'),
            'voiceFallbackUrl' => Values::array_get($payload, 'voice_fallback_url'),
            'voiceMethod' => Values::array_get($payload, 'voice_method'),
            'voiceUrl' => Values::array_get($payload, 'voice_url'),
        );

        $this->solution = array('trunkSid' => $trunkSid, 'sid' => $sid ?: $this->properties['sid'], );
    }

    /**
     * Fetch a PhoneNumberInstance
     *
     * @return PhoneNumberInstance Fetched PhoneNumberInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch() {
        return $this->proxy()->fetch();
    }

    /**
     * Generate an instance context for the instance, the context is capable of
     * performing various actions.  All instance actions are proxied to the context
     *
     * @return PhoneNumberContext Context for this
     *                                                           PhoneNumberInstance
     */
    protected function proxy() {
        if (!$this->context) {
            $this->context = new PhoneNumberContext(
                $this->version,
                $this->solution['trunkSid'],
                $this->solution['sid']
            );
        }

        return $this->context;
    }

    /**
     * Deletes the PhoneNumberInstance
     * 
     * @return boolean True if delete succeeds, false otherwise
     * @throws TwilioException When an HTTP error occurs.
     */
    public function delete() {
        return $this->proxy()->delete();
    }

    /**
     * Magic getter to access properties
     * 
     * @param string $name Property to access
     * @return mixed The requested property
     * @throws TwilioException For unknown properties
     */
    public function __get($name) {
        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }

        if (property_exists($this, '_' . $name)) {
            $method = 'get' . ucfirst($name);
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
        $context = array();
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Trunking.V1.PhoneNumberInstance ' . implode(' ', $context) . ']';
    }
}