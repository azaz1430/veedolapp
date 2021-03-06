<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Api;

use Twilio\Domain;
use Twilio\Exceptions\TwilioException;
use Twilio\InstanceContext;
use Twilio\ListResource;
use Twilio\Rest\Api\V2010\Account\AddressContext;
use Twilio\Rest\Api\V2010\Account\AddressList;
use Twilio\Rest\Api\V2010\Account\ApplicationContext;
use Twilio\Rest\Api\V2010\Account\ApplicationList;
use Twilio\Rest\Api\V2010\Account\AuthorizedConnectAppContext;
use Twilio\Rest\Api\V2010\Account\AuthorizedConnectAppList;
use Twilio\Rest\Api\V2010\Account\AvailablePhoneNumberCountryContext;
use Twilio\Rest\Api\V2010\Account\AvailablePhoneNumberCountryList;
use Twilio\Rest\Api\V2010\Account\BalanceList;
use Twilio\Rest\Api\V2010\Account\CallContext;
use Twilio\Rest\Api\V2010\Account\CallList;
use Twilio\Rest\Api\V2010\Account\ConferenceContext;
use Twilio\Rest\Api\V2010\Account\ConferenceList;
use Twilio\Rest\Api\V2010\Account\ConnectAppContext;
use Twilio\Rest\Api\V2010\Account\ConnectAppList;
use Twilio\Rest\Api\V2010\Account\IncomingPhoneNumberContext;
use Twilio\Rest\Api\V2010\Account\IncomingPhoneNumberList;
use Twilio\Rest\Api\V2010\Account\KeyContext;
use Twilio\Rest\Api\V2010\Account\KeyList;
use Twilio\Rest\Api\V2010\Account\MessageContext;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Api\V2010\Account\NewKeyList;
use Twilio\Rest\Api\V2010\Account\NewSigningKeyList;
use Twilio\Rest\Api\V2010\Account\NotificationContext;
use Twilio\Rest\Api\V2010\Account\NotificationList;
use Twilio\Rest\Api\V2010\Account\OutgoingCallerIdContext;
use Twilio\Rest\Api\V2010\Account\OutgoingCallerIdList;
use Twilio\Rest\Api\V2010\Account\QueueContext;
use Twilio\Rest\Api\V2010\Account\QueueList;
use Twilio\Rest\Api\V2010\Account\RecordingContext;
use Twilio\Rest\Api\V2010\Account\RecordingList;
use Twilio\Rest\Api\V2010\Account\ShortCodeContext;
use Twilio\Rest\Api\V2010\Account\ShortCodeList;
use Twilio\Rest\Api\V2010\Account\SigningKeyContext;
use Twilio\Rest\Api\V2010\Account\SigningKeyList;
use Twilio\Rest\Api\V2010\Account\SipList;
use Twilio\Rest\Api\V2010\Account\TokenList;
use Twilio\Rest\Api\V2010\Account\TranscriptionContext;
use Twilio\Rest\Api\V2010\Account\TranscriptionList;
use Twilio\Rest\Api\V2010\Account\UsageList;
use Twilio\Rest\Api\V2010\Account\ValidationRequestList;
use Twilio\Rest\Api\V2010\AccountContext;
use Twilio\Rest\Api\V2010\AccountInstance;
use Twilio\Rest\Api\V2010\AccountList;
use Twilio\Version;

/**
 * @property AccountList accounts
 * @method AccountContext accounts(string $sid)
 * @property AccountContext account
 * @property AddressList addresses
 * @property ApplicationList applications
 * @property AuthorizedConnectAppList authorizedConnectApps
 * @property AvailablePhoneNumberCountryList availablePhoneNumbers
 * @property BalanceList balance
 * @property CallList calls
 * @property ConferenceList conferences
 * @property ConnectAppList connectApps
 * @property IncomingPhoneNumberList incomingPhoneNumbers
 * @property KeyList keys
 * @property MessageList messages
 * @property NewKeyList newKeys
 * @property NewSigningKeyList newSigningKeys
 * @property NotificationList notifications
 * @property OutgoingCallerIdList outgoingCallerIds
 * @property QueueList queues
 * @property RecordingList recordings
 * @property SigningKeyList signingKeys
 * @property SipList sip
 * @property ShortCodeList shortCodes
 * @property TokenList tokens
 * @property TranscriptionList transcriptions
 * @property UsageList usage
 * @property ValidationRequestList validationRequests
 * @method AddressContext addresses(string $sid)
 * @method ApplicationContext applications(string $sid)
 * @method AuthorizedConnectAppContext authorizedConnectApps(string $connectAppSid)
 * @method AvailablePhoneNumberCountryContext availablePhoneNumbers(string $countryCode)
 * @method CallContext calls(string $sid)
 * @method ConferenceContext conferences(string $sid)
 * @method ConnectAppContext connectApps(string $sid)
 * @method IncomingPhoneNumberContext incomingPhoneNumbers(string $sid)
 * @method KeyContext keys(string $sid)
 * @method MessageContext messages(string $sid)
 * @method NotificationContext notifications(string $sid)
 * @method OutgoingCallerIdContext outgoingCallerIds(string $sid)
 * @method QueueContext queues(string $sid)
 * @method RecordingContext recordings(string $sid)
 * @method SigningKeyContext signingKeys(string $sid)
 * @method ShortCodeContext shortCodes(string $sid)
 * @method TranscriptionContext transcriptions(string $sid)
 */
class V2010 extends Version {
    protected $_accounts = null;
    protected $_account = null;
    protected $_addresses = null;
    protected $_applications = null;
    protected $_authorizedConnectApps = null;
    protected $_availablePhoneNumbers = null;
    protected $_balance = null;
    protected $_calls = null;
    protected $_conferences = null;
    protected $_connectApps = null;
    protected $_incomingPhoneNumbers = null;
    protected $_keys = null;
    protected $_messages = null;
    protected $_newKeys = null;
    protected $_newSigningKeys = null;
    protected $_notifications = null;
    protected $_outgoingCallerIds = null;
    protected $_queues = null;
    protected $_recordings = null;
    protected $_signingKeys = null;
    protected $_sip = null;
    protected $_shortCodes = null;
    protected $_tokens = null;
    protected $_transcriptions = null;
    protected $_usage = null;
    protected $_validationRequests = null;

    /**
     * Construct the V2010 version of Api
     * 
     * @param Domain $domain Domain that contains the version
     * @return V2010 V2010 version of Api
     */
    public function __construct(Domain $domain) {
        parent::__construct($domain);
        $this->version = '2010-04-01';
    }

    /**
     * Magic getter to lazy load root resources
     *
     * @param string $name Resource to return
     *
     * @return ListResource The requested resource
     * @throws TwilioException For unknown resource
     */
    public function __get($name) {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new TwilioException('Unknown resource ' . $name);
    }

    /**
     * Magic caller to get resource contexts
     *
     * @param string $name Resource to return
     * @param array $arguments Context parameters
     *
     * @return InstanceContext The requested resource context
     * @throws TwilioException For unknown resource
     */
    public function __call($name, $arguments) {
        $property = $this->$name;
        if (method_exists($property, 'getContext')) {
            return call_user_func_array(array($property, 'getContext'), $arguments);
        }

        throw new TwilioException('Resource does not have a context');
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString() {
        return '[Twilio.Api.V2010]';
    }

    /**
     * @return AccountList
     */
    protected function getAccounts() {
        if (!$this->_accounts) {
            $this->_accounts = new AccountList($this);
        }
        return $this->_accounts;
    }

    /**
     * @return AccountContext Account provided as the
     *                                               authenticating account
     */
    protected function getAccount() {
        if (!$this->_account) {
            $this->_account = new AccountContext(
                $this,
                $this->domain->getClient()->getAccountSid()
            );
        }
        return $this->_account;
    }

    /**
     * Setter to override the primary account
     *
     * @param AccountContext|AccountInstance $account account to use as the primary
     *                                                account
     */
    public function setAccount($account) {
        $this->_account = $account;
    }

    /**
     * @return AddressList
     */
    protected function getAddresses() {
        return $this->account->addresses;
    }

    /**
     * @return ApplicationList
     */
    protected function getApplications() {
        return $this->account->applications;
    }

    /**
     * @return AuthorizedConnectAppList
     */
    protected function getAuthorizedConnectApps() {
        return $this->account->authorizedConnectApps;
    }

    /**
     * @return AvailablePhoneNumberCountryList
     */
    protected function getAvailablePhoneNumbers() {
        return $this->account->availablePhoneNumbers;
    }

    /**
     * @return BalanceList
     */
    protected function getBalance() {
        return $this->account->balance;
    }

    /**
     * @return CallList
     */
    protected function getCalls() {
        return $this->account->calls;
    }

    /**
     * @return ConferenceList
     */
    protected function getConferences() {
        return $this->account->conferences;
    }

    /**
     * @return ConnectAppList
     */
    protected function getConnectApps() {
        return $this->account->connectApps;
    }

    /**
     * @return IncomingPhoneNumberList
     */
    protected function getIncomingPhoneNumbers() {
        return $this->account->incomingPhoneNumbers;
    }

    /**
     * @return KeyList
     */
    protected function getKeys() {
        return $this->account->keys;
    }

    /**
     * @return MessageList
     */
    protected function getMessages() {
        return $this->account->messages;
    }

    /**
     * @return NewKeyList
     */
    protected function getNewKeys() {
        return $this->account->newKeys;
    }

    /**
     * @return NewSigningKeyList
     */
    protected function getNewSigningKeys() {
        return $this->account->newSigningKeys;
    }

    /**
     * @return NotificationList
     */
    protected function getNotifications() {
        return $this->account->notifications;
    }

    /**
     * @return OutgoingCallerIdList
     */
    protected function getOutgoingCallerIds() {
        return $this->account->outgoingCallerIds;
    }

    /**
     * @return QueueList
     */
    protected function getQueues() {
        return $this->account->queues;
    }

    /**
     * @return RecordingList
     */
    protected function getRecordings() {
        return $this->account->recordings;
    }

    /**
     * @return SigningKeyList
     */
    protected function getSigningKeys() {
        return $this->account->signingKeys;
    }

    /**
     * @return SipList
     */
    protected function getSip() {
        return $this->account->sip;
    }

    /**
     * @return ShortCodeList
     */
    protected function getShortCodes() {
        return $this->account->shortCodes;
    }

    /**
     * @return TokenList
     */
    protected function getTokens() {
        return $this->account->tokens;
    }

    /**
     * @return TranscriptionList
     */
    protected function getTranscriptions() {
        return $this->account->transcriptions;
    }

    /**
     * @return UsageList
     */
    protected function getUsage() {
        return $this->account->usage;
    }

    /**
     * @return ValidationRequestList
     */
    protected function getValidationRequests() {
        return $this->account->validationRequests;
    }
}