<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Tests\Integration\Taskrouter\V1\Workspace;

use Twilio\Exceptions\DeserializeException;
use Twilio\Exceptions\TwilioException;
use Twilio\Http\Response;
use Twilio\Tests\HolodeckTestCase;
use Twilio\Tests\Request;

class WorkerTest extends HolodeckTestCase {
    public function testReadRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->taskrouter->v1->workspaces("WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                         ->workers->read();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers'
        ));
    }

    public function testReadFullResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "meta": {
                    "page": 0,
                    "page_size": 50,
                    "first_page_url": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers?PageSize=50&Page=0",
                    "previous_page_url": null,
                    "url": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers?PageSize=50&Page=0",
                    "next_page_url": null,
                    "key": "workers"
                },
                "workers": [
                    {
                        "sid": "WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "friendly_name": "testWorker",
                        "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "activity_sid": "WAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "activity_name": "Offline",
                        "workspace_sid": "WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "attributes": "{}",
                        "available": false,
                        "date_created": "2017-05-30T23:05:29Z",
                        "date_updated": "2017-05-30T23:05:29Z",
                        "date_status_changed": "2017-05-30T23:05:29Z",
                        "url": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "links": {
                            "channels": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Channels",
                            "activity": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Activities/WAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                            "workspace": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                            "statistics": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/Statistics",
                            "worker_channels": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Channels",
                            "reservations": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Reservations"
                        }
                    }
                ]
            }
            '
        ));

        $actual = $this->twilio->taskrouter->v1->workspaces("WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                               ->workers->read();

        $this->assertGreaterThan(0, count($actual));
    }

    public function testReadEmptyResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "meta": {
                    "first_page_url": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers?PageSize=50&Page=0",
                    "key": "workers",
                    "next_page_url": null,
                    "page": 0,
                    "page_size": 50,
                    "previous_page_url": null,
                    "url": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers?PageSize=50&Page=0"
                },
                "workers": []
            }
            '
        ));

        $actual = $this->twilio->taskrouter->v1->workspaces("WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                               ->workers->read();

        $this->assertNotNull($actual);
    }

    public function testCreateRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->taskrouter->v1->workspaces("WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                         ->workers->create("friendlyName");
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $values = array(
            'FriendlyName' => "friendlyName",
        );

        $this->assertRequest(new Request(
            'post',
            'https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers',
            null,
            $values
        ));
    }

    public function testCreateResponse() {
        $this->holodeck->mock(new Response(
            201,
            '
            {
                "sid": "WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "friendly_name": "NewWorker",
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "activity_sid": "WAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "activity_name": "Offline",
                "workspace_sid": "WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "attributes": "{}",
                "available": false,
                "date_created": "2017-05-30T23:19:38Z",
                "date_updated": "2017-05-30T23:19:38Z",
                "date_status_changed": "2017-05-30T23:19:38Z",
                "url": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "links": {
                    "channels": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Channels",
                    "activity": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Activities/WAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                    "workspace": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                    "statistics": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/Statistics",
                    "worker_channels": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Channels",
                    "reservations": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Reservations"
                }
            }
            '
        ));

        $actual = $this->twilio->taskrouter->v1->workspaces("WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                               ->workers->create("friendlyName");

        $this->assertNotNull($actual);
    }

    public function testFetchRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->taskrouter->v1->workspaces("WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                         ->workers("WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
        ));
    }

    public function testFetchResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "activity_name": "available",
                "activity_sid": "WAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "attributes": "{}",
                "available": false,
                "date_created": "2017-05-30T23:32:39Z",
                "date_status_changed": "2017-05-30T23:32:39Z",
                "date_updated": "2017-05-30T23:32:39Z",
                "friendly_name": "NewWorker3",
                "sid": "WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "url": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "workspace_sid": "WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "links": {
                    "channels": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Channels",
                    "activity": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Activities/WAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                    "workspace": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                    "statistics": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/Statistics",
                    "worker_channels": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Channels",
                    "reservations": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Reservations"
                }
            }
            '
        ));

        $actual = $this->twilio->taskrouter->v1->workspaces("WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                               ->workers("WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();

        $this->assertNotNull($actual);
    }

    public function testUpdateRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->taskrouter->v1->workspaces("WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                         ->workers("WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->update();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'post',
            'https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
        ));
    }

    public function testUpdateResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "sid": "WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "friendly_name": "blah",
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "activity_sid": "WAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "activity_name": "Offline",
                "workspace_sid": "WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "attributes": "{}",
                "available": false,
                "date_created": "2017-05-30T23:32:22Z",
                "date_updated": "2017-05-31T00:05:57Z",
                "date_status_changed": "2017-05-30T23:32:22Z",
                "url": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "links": {
                    "channels": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Channels",
                    "activity": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Activities/WAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                    "workspace": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                    "statistics": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/Statistics",
                    "worker_channels": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Channels",
                    "reservations": "https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Reservations"
                }
            }
            '
        ));

        $actual = $this->twilio->taskrouter->v1->workspaces("WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                               ->workers("WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->update();

        $this->assertNotNull($actual);
    }

    public function testDeleteRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->taskrouter->v1->workspaces("WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                         ->workers("WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->delete();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'delete',
            'https://taskrouter.twilio.com/v1/Workspaces/WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Workers/WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
        ));
    }

    public function testDeleteResponse() {
        $this->holodeck->mock(new Response(
            204,
            null
        ));

        $actual = $this->twilio->taskrouter->v1->workspaces("WSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                               ->workers("WKaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->delete();

        $this->assertTrue($actual);
    }
}