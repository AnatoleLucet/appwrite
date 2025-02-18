<?php

namespace Tests\E2E\Scopes;

use Tests\E2E\Client;

trait ProjectCustom
{
    /**
     * @var array
     */
    protected static $project = [];

    /**
     * @return array
     */
    public function getProject(): array
    {
        if(!empty(self::$project)) {
            return self::$project;
        }

        $team = $this->client->call(Client::METHOD_POST, '/teams', [
            'origin' => 'http://localhost',
            'content-type' => 'application/json',
            'cookie' => 'a_session_console=' . $this->getRoot()['session'],
            'x-appwrite-project' => 'console',
        ], [
            'name' => 'Demo Project Team',
        ]);

        $this->assertEquals(201, $team['headers']['status-code']);
        $this->assertEquals('Demo Project Team', $team['body']['name']);
        $this->assertNotEmpty($team['body']['$id']);

        $project = $this->client->call(Client::METHOD_POST, '/projects', [
            'origin' => 'http://localhost',
            'content-type' => 'application/json',
            'cookie' => 'a_session_console=' . $this->getRoot()['session'],
            'x-appwrite-project' => 'console',
        ], [
            'name' => 'Demo Project',
            'teamId' => $team['body']['$id'],
            'description' => 'Demo Project Description',
            'logo' => '',
            'url' => 'https://appwrite.io',
            'legalName' => '',
            'legalCountry' => '',
            'legalState' => '',
            'legalCity' => '',
            'legalAddress' => '',
            'legalTaxId' => '',
        ]);

        $this->assertEquals(201, $project['headers']['status-code']);
        $this->assertNotEmpty($project['body']);

        $key = $this->client->call(Client::METHOD_POST, '/projects/' . $project['body']['$id'] . '/keys', [
            'origin' => 'http://localhost',
            'content-type' => 'application/json',
            'cookie' => 'a_session_console=' . $this->getRoot()['session'],
            'x-appwrite-project' => 'console',
        ], [
            'name' => 'Demo Project Key',
            'scopes' => [
                'users.read',
                'users.write',
                'teams.read',
                'teams.write',
                'collections.read',
                'collections.write',
                'documents.read',
                'documents.write',
                'files.read',
                'files.write',
                'locale.read',
                'avatars.read',
            ],
        ]);

        $this->assertEquals(201, $project['headers']['status-code']);
        $this->assertNotEmpty($key['body']);
        $this->assertNotEmpty($key['body']['secret']);

        // return [
        //     'email' => $this->demoEmail,
        //     'password' => $this->demoPassword,
        //     'session' => $session,
        //     'projectUid' => $project['body']['$id'],
        //     'projectAPIKeySecret' => $key['body']['secret'],
        //     'projectSession' => $this->client->parseCookie($user['headers']['set-cookie'])['a_session_' . $project['body']['$id']],
        // ];

        self::$project = [
            '$id' => $project['body']['$id'],
            'name' => $project['body']['name'],
            'apiKey' => $key['body']['secret'],
        ];

        return self::$project;
    }
}
