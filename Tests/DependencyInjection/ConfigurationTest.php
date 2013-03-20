<?php

namespace Ano\Bundle\BarbeQBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Ano\Bundle\BarbeQBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDummyTest()
    {
        $this->assertTrue(true);
    }

    protected static function getBundleDefaultConfig()
    {
        return array();
    }
}
