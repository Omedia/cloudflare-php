<?php

class AccessRulesTest extends TestCase
{
    public function testListRules()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/listAccessRules.json');

        $authMock = $this->createMock(\Cloudflare\API\Auth\Auth::class);
        $mock = $this->getMockBuilder(\Cloudflare\API\Adapter\Adapter::class)
            ->setConstructorArgs([$authMock, 'https://example.com'])
            ->getMock();
        $mock->method('get')->willReturn($response);

        $mock->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo('zones/023e105f4ecef8ad9ca31a8372d0c353/firewall/access_rules/rules'),
                $this->equalTo([
                    'page' => 1,
                    'per_page' => 50,
                    'match' => 'all'
                ])
            );

        $zones = new \Cloudflare\API\Endpoints\AccessRules($mock);
        $result = $zones->listRules('023e105f4ecef8ad9ca31a8372d0c353');

        $this->assertObjectHasProperty('result', $result);
        $this->assertObjectHasProperty('result_info', $result);

        $this->assertEquals('92f17202ed8bd63d69a66b86a49a8f6b', $result->result[0]->id);
        $this->assertEquals(1, $result->result_info->page);
        $this->assertEquals('92f17202ed8bd63d69a66b86a49a8f6b', $zones->getBody()->result[0]->id);
    }

    public function testCreateRule()
    {
        $config = new \Cloudflare\API\Configurations\AccessRules();
        $config->setIP('1.2.3.4');

        $response = $this->getPsr7JsonResponseForFixture('Endpoints/createAccessRule.json');

        $authMock = $this->createMock(\Cloudflare\API\Auth\Auth::class);
        $mock = $this->getMockBuilder(\Cloudflare\API\Adapter\Adapter::class)
            ->setConstructorArgs([$authMock, 'https://example.com'])
            ->getMock();
        $mock->method('post')->willReturn($response);

        $mock->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('zones/023e105f4ecef8ad9ca31a8372d0c353/firewall/access_rules/rules'),
                $this->equalTo([
                    'mode' => 'challenge',
                    'configuration' => $config->getArray(),
                    'notes' => 'This rule is on because of an event that occured on date X',
                ])
            );

        $rules = new \Cloudflare\API\Endpoints\AccessRules($mock);
        $rules->createRule(
            '023e105f4ecef8ad9ca31a8372d0c353',
            'challenge',
            $config,
            'This rule is on because of an event that occured on date X'
        );
        $this->assertEquals('92f17202ed8bd63d69a66b86a49a8f6b', $rules->getBody()->result->id);
    }

    public function testUpdateRule()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/updateAccessRule.json');

        $authMock = $this->createMock(\Cloudflare\API\Auth\Auth::class);
        $mock = $this->getMockBuilder(\Cloudflare\API\Adapter\Adapter::class)
            ->setConstructorArgs([$authMock, 'https://example.com'])
            ->getMock();
        $mock->method('patch')->willReturn($response);

        $mock->expects($this->once())
            ->method('patch')
            ->with(
                $this->equalTo('zones/023e105f4ecef8ad9ca31a8372d0c353/firewall/access_rules/rules/92f17202ed8bd63d69a66b86a49a8f6b'),
                $this->equalTo([
                    'mode' => 'challenge',
                    'notes' => 'This rule is on because of an event that occured on date X',
                ])
            );

        $rules = new \Cloudflare\API\Endpoints\AccessRules($mock);
        $rules->updateRule(
            '023e105f4ecef8ad9ca31a8372d0c353',
            '92f17202ed8bd63d69a66b86a49a8f6b',
            'challenge',
            'This rule is on because of an event that occured on date X'
        );
        $this->assertEquals('92f17202ed8bd63d69a66b86a49a8f6b', $rules->getBody()->result->id);
    }

    public function testDeleteRule()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/deleteAccessRule.json');

        $authMock = $this->createMock(\Cloudflare\API\Auth\Auth::class);
        $mock = $this->getMockBuilder(\Cloudflare\API\Adapter\Adapter::class)
            ->setConstructorArgs([$authMock, 'https://example.com'])
            ->getMock();
        $mock->method('delete')->willReturn($response);

        $mock->expects($this->once())
            ->method('delete')
            ->with(
                $this->equalTo('zones/023e105f4ecef8ad9ca31a8372d0c353/firewall/access_rules/rules/92f17202ed8bd63d69a66b86a49a8f6b'),
                $this->equalTo([
                    'cascade' => 'none'
                ])
            );

        $rules = new \Cloudflare\API\Endpoints\AccessRules($mock);
        $rules->deleteRule('023e105f4ecef8ad9ca31a8372d0c353', '92f17202ed8bd63d69a66b86a49a8f6b');
        $this->assertEquals('92f17202ed8bd63d69a66b86a49a8f6b', $rules->getBody()->result->id);
    }
}
