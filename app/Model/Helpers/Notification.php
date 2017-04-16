<?php

namespace App\Model\Helpers;

use App\Model\App\Device;
use DB;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class Notification
{
    /** @var OptionsBuilder */
    private $option_builder;

    /** @var PayloadNotificationBuilder */
    private $notification_builder;

    /** @var PayloadDataBuilder */
    private $data_builder;

    /** @var String message */
    private $message;

    /** @var string Title */
    private $title;

    /** @var array Data */
    private $data;

    /**
     * Notification constructor for new messages.
     *
     * @param string $title
     * @param string $message
     */
    public function __construct($title, $message) {
        $this->message = $message;
        $this->title   = $title;

        $this->option_builder = new OptionsBuilder();
        $this->option_builder->setTimeToLive(60*20);

        $this->notification_builder = new PayloadNotificationBuilder($title);
        $this->notification_builder->setBody($message)->setSound('default');
    }


    /**
     * Add data to the push notification
     *
     * @param array $data
     */
    public function add_data(array $data) {
        $this->data = $data;

        $default = [
            "style" => "inbox",
            "summaryText" => "Er zijn %n% nieuwe berichten",
            "ledColor" => [0, 0, 255, 0]
        ];

        $data = array_merge($default, $data);

        $this->data_builder = new PayloadDataBuilder();
        $this->data_builder->addData($data);
    }


    /**
     * Send the push notification
     *
     * @param array $recipients
     */
    public function send(array $recipients) {

        $tokens = [];

        foreach($recipients as $recipient) {
            $tokens[] = $recipient['token'];
        }

        if(count($tokens) == 0) {
            return;
        }

        $option       = $this->option_builder->build();
        $notification = $this->notification_builder->build();
        $data         = $this->data_builder->build();

        $response = FCM::sendTo($tokens, $option, $notification, $data);

        // Insert the sent notification into the database
        $this->save_sent_notification();

        // Remove tokens that need removal
        $this->remove_devices($response->tokensToDelete());

        // Update tokens that need to be updated
        $this->update_devices($response->tokensToModify());

        // Remove devices which countered an error
        $this->remove_devices_with_error($response->tokensWithError());
    }


    /**
     * Save the sent notification in the database
     */
    private function save_sent_notification() {
        DB::table('notifications')->insert([
            'sent'    => date('Y-m-d H:i:s'),
            'title'   => $this->title,
            'content' => $this->message,
            'page'    => isset($this->data['page']) ? $this->data['page'] : null
        ]);
    }


    /**
     * Remove tokens from the database
     *
     * @param $tokens
     */
    private function remove_devices($tokens) {
        foreach($tokens as $key => $token) {
            Device::where('token', $token)->delete();
        }
    }


    /**
     * Remove tokens from the database
     *
     * @param $tokens
     */
    private function remove_devices_with_error($tokens) {
        foreach($tokens as $token => $error) {
            Device::where('token', $token)->delete();
        }
    }


    /**
     * Update devices
     *
     * @param $tokens
     */
    private function update_devices($tokens) {
        // Update the old tokens
        foreach($tokens as $old_token => $new_token) {
            // Delete the device with the new token, if it already exists
            Device::where('token', $new_token)->delete();

            // Update the old token, to use the new token
            Device::where('token', $old_token)->update(['token' => $new_token]);
        }
    }
}