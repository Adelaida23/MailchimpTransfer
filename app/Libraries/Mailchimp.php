<?php

namespace App\Libraries;

use MailchimpMarketing\ApiClient;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\ClientException;

class Mailchimp
{
    protected $mailchimp;
    public function __construct($config = [])
    {
        foreach ($config as $k => $v) {
            $this->$k = $v;
        }
        $this->init();
    }
    public function init()
    {
        $this->mailchimp = new \MailchimpMarketing\ApiClient();
        $this->mailchimp->setConfig([
            'apiKey' => $this->apiKey,
            'server' => $this->server
        ]);
    }

    public function getCount($list_id)
    {
        $res = $this->mailchimp->lists->getListMembersInfo($list_id);
        if (!empty($res)) {
            return intval($res->total_items);
        }
        return PHP_INT_MAX;
    }

    /**
     * Push Lead to ESP Account
     * @param App\Models\Lead $lead
     * @return Response
     */
    public function push($lead, $field = FALSE)
    {
        if (is_array($field)) {
            $merge_fields = [];
            foreach ($field as $f) {
                $merge_fields[$f] = 'TRUE';
            }
            $merge_fields['FNAME'] = $lead->first_name;
            $merge_fields['LNAME'] = $lead->last_name;
            $options = [
                "email_address" => $lead->email,
                "status" => "subscribed",
                "merge_fields" => $merge_fields
            ];
        } else {
            $options = [
                "email_address" => $lead->email,
                "status" => "subscribed",

            ];
            if (!empty($lead->first_name)) {
                $options["merge_fields"] = [
                    "FNAME" => $lead->first_name,
                    "LNAME" => $lead->last_name
                ];
            }
        }

        $out = null;
        try {
            $out = $this->mailchimp->lists->addListMember($this->list_id, $options);
        } catch (\Exception $e) {
            // already exist ? or bad argument call
        }
    }
    /**
     * Get all the lists in the mailchimp account
     * @param fields String
     * @param exclude_fields String
     * @param count String
     * @param offset String
     * @param before_date_create String
     * @return Array
     */
    public function getLists($fields = null, $exclude_fields = null, $count = '10', $offset = '0', $before_date_created = null, $since_date_created = null, $before_campaign_last_sent = null, $since_campaign_last_sent = null, $email = null, $sort_field = null, $sort_dir = null, $has_ecommerce_store = null)
    {
        return $this->mailchimp->lists->getAllLists($fields, $exclude_fields, $count, $offset, $before_date_created, $since_date_created, $before_campaign_last_sent, $since_campaign_last_sent, $email, $sort_field, $sort_dir, $has_ecommerce_store);
    }
    /**
     * @param list_id | Mailchimp ID List NO WEB ID
     * @param status | subscribed, unsubscribed, cleaned, transactional, pending, archived
     * @param email_type | html, text
     * @return Array
     */
    public function getListMembers($list_id, $fields = null, $exclude_fields = null, $count = '10', $offset = '0', $email_type = null, $status = null, $since_timestamp_opt = null, $before_timestamp_opt = null, $since_last_changed = null, $before_last_changed = null, $unique_email_id = null, $vip_only = null, $interest_category_id = null, $interest_ids = null, $interest_match = null, $sort_field = null, $sort_dir = null, $since_last_campaign = null, $unsubscribed_since = null)
    {
    }
    /**
     * @param $list_id | Mailchimp List ID
     * @param $member_md5 | Email hash into MD5
     * @return Object
     */
    public function getMemberData($list_id, $member_md5)
    {
        return $this->mailchimp->lists->getListMember($list_id, $member_md5);
    }
    /**
     * @param $list_id | Mailchimp List ID
     * @param $options | Array with options and conditions for the new segment
     * @return Object | Created segment
     */
    public function createSegment($list_id, $options = [])
    {
        return $this->mailchimp->lists->createSegment($list_id, $options);
    }
    /**
     *
     *
     *
     */
    public function getSegments($list_id, $fields = null, $exclude_fields = null, $count = '10', $offset = '0', $type = null, $since_created_at = null, $before_created_at = null, $include_cleaned = null, $include_transactional = null, $include_unsubscribed = null, $since_updated_at = null, $before_updated_at = null)
    {
        return $this->mailchimp->lists->listSegments($list_id, $fields, $exclude_fields, $count, $offset, $type, $since_created_at, $before_created_at, $include_cleaned, $include_transactional, $include_unsubscribed, $since_updated_at, $before_updated_at);
    }
    /**
     * @param $list_id | List ID of Mailchimp service
     * @return Object
     *
     */
    public function getMergeFields($list_id, $fields = null, $exclude_fields = null, $count = '10', $offset = '0', $type = null, $required = null)
    {
        try {
            return $this->mailchimp->lists->getListMergeFields($list_id, $fields = null, $exclude_fields = null, $count = '10', $offset = '0', $type = null, $required = null);
        } catch (\Exception $e) {
            return FALSE;
        }
    }
    /**
     * @param stdClass $object | Return of getMergeFields
     * @param $advertiser | Advertiser ID
     * @return String | Merge Field Tag
     */
    public function getMergeField(\stdClass $object, $advertiser)
    {
        $ads = [];
        foreach ($advertiser as $a) {
            foreach ($object->merge_fields as $value) {
                if ($value->name === "s-$a") {
                    $ads[] = $value->tag;
                }
            }
        }
        return $ads;
    }
    /**
     * @param $list_id | List ID of Mailchimp Service
     * @param $email_hash | MD5 of the Email
     * @param $field | Tag Name of the Merge Field to update
     * @return stdClass | Object with Member Data updated
     */
    public function setMergeField($list_id, $email_hash, $field)
    {
        try {
            $merge_fields = [];
            foreach ($field as $f) {
                $merge_fields[$f] = "TRUE";
            }
            return $this->mailchimp->lists->updateListMember($list_id, $email_hash, [
                'merge_fields' => $merge_fields
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * @param $list_id | Mailchimp ID
     * @param $emails | Array of MD5
     * @return Array | email_array => Plain Text emails, errors => Errors might occur
     **/
    public function MD52Email($list_id, $offer_id, $emails = [])
    {
        $email_array = array();
        $errors = array();
        foreach ($emails as $email) {
            try {
                $model = DB::table('suppressions')->where('hash', '=', $email)->first();
                if (empty($model)) {
                    $response = $this->getMemberData($list_id, $email);
                    $email_array[] = $response->email_address;
                    // Insert suppressed email to table
                    DB::table('suppressions')->insert([
                        'email' => $response->email_address,
                        'hash' => md5($response->email_address),
                        'offer_id' => $offer_id
                    ]);
                } else {
                    $email_array[] = $model->email;
                }
            } catch (ClientException $e) {
                $errors[$email] = json_decode($e->getResponse()->getBody()->getContents());
            }
        }
        return compact('email_array', 'errors');
    }

    public function ping()
    {
        try {
            return $this->mailchimp->ping->get();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function automations()
    {
        $client = $this->mailchimp;
        $response = $client->automations->list(
            $count = 1000,
            // $after_create_date='2020-01-01T00:00:00-05:00'
        );
        return $response;
    }

    //Methods add

    public function getListMembersInformation($list_id)
    {
        try {
            return $this->mailchimp->lists->getListMembersInfo($list_id)->members;
        } catch (\Exception $e) {
            return FALSE;
        }
    }

    public function getOneMemberInfo($list_id, $subscriber_hash)
    {
        try {
            return $this->mailchimp->lists->getListMember($list_id, $subscriber_hash);
        } catch (\Exception $e) {
            return false;
        }
    }
    public function addListOneMember($list_id, $arg_data)
    {
        try {
            return $this->mailchimp->lists->addListMember($list_id, $arg_data);
        } catch (\Exception $e) {
            return false;
        }
    }
    public function archivateListMember($list_id, $subscriber_hash)
    {
        try {
            return $this->mailchimp->lists->deleteListMember($list_id, $subscriber_hash);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteListMemberPermanent($list_id, $subscriber_hash)
    {
        try {
            return $this->mailchimp->lists->deleteListMemberPermanent($list_id, $subscriber_hash);
        } catch (\Exception $e) {
            return false;
        }
    }
}
