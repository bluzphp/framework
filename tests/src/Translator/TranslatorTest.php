<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Translator;

use Bluz\Tests\TestCase;
use Bluz\Translator\Translator;

/**
 * TranslatorTest
 *
 * @package  Bluz\Tests\Translator
 *
 * @author   Anton Shevchuk
 * @created  22.08.2014 16:37
 */
class TranslatorTest extends TestCase
{
    /**
     * Test Translator initialization
     *
     * @expectedException \Bluz\Config\ConfigException
     */
    public function testInvalidConfigurationThrowException()
    {
        $translator = new Translator();
        $translator->addTextDomain('any', '/this/directory/is/not/exists');
    }

    /**
     * Test Translate
     */
    public function testTranslate()
    {
        $translator = new Translator();
        $translator->setDomain('messages');
        $translator->setLocale('uk_UA');
        $translator->setPath(PATH_APPLICATION .'/locale');

        $this->assertEquals('message', $translator->translate('message'));
    }

    /**
     * Test Plural Translate
     */
    public function testPluralTranslate()
    {
        $translator = new Translator();
        $translator->setDomain('messages');
        $translator->setLocale('uk_UA');
        $translator->setPath(PATH_APPLICATION .'/locale');

        $this->assertEquals('messages', $translator->translatePlural('message', 'messages', 2));
    }
}
