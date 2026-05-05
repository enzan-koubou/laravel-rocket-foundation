<?php
namespace Tests\Helpers;

use EnzanRocket\Foundation\Tests\TestCase;

class URLHelperTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var \EnzanRocket\Foundation\Helpers\URLHelperInterface $helper */
        $helper = app()->make(\EnzanRocket\Foundation\Helpers\URLHelperInterface::class);
        $this->assertNotNull($helper);
    }

    public function testSwapHost()
    {
        /** @var \EnzanRocket\Foundation\Helpers\URLHelperInterface $helper */
        $helper = app()->make(\EnzanRocket\Foundation\Helpers\URLHelperInterface::class);
        $result = $helper->swapHost('http://takaaki.info/path/to/somewhere', 'example.com');
        $this->assertEquals('http://example.com/path/to/somewhere', $result);
    }

    public function testNormalizeUrlPath()
    {
        /** @var \EnzanRocket\Foundation\Helpers\URLHelperInterface $helper */
        $helper = app()->make('EnzanRocket\Foundation\Helpers\URLHelperInterface');
        $result = $helper->normalizeUrlPath('Test Strings');
        $this->assertEquals('test-strings', $result);
    }

    public function testAsset()
    {
        /** @var \EnzanRocket\Foundation\Helpers\URLHelperInterface $helper */
        $helper = app()->make(\EnzanRocket\Foundation\Helpers\URLHelperInterface::class);
        $hash   = md5(time());
        config()->set('asset.hash', $hash);
        $result = $helper->asset('img/test.png');
        $this->assertEquals('http://:/static/user/img/test.png?'.$hash, $result);

        $result = $helper->asset('img/test.png', 'common');
        $this->assertEquals('http://:/static/common/img/test.png?'.$hash, $result);
    }
}
