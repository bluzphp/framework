<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Translator;

use Bluz\Common\Exception\ConfigurationException;
use Bluz\Tests\FrameworkTestCase;
use Bluz\Translator\Translator;

/**
 * TranslatorTest
 *
 * @package  Bluz\Tests\Translator
 *
 * @author   Anton Shevchuk
 * @created  22.08.2014 16:37
 */
class TranslatorTest extends FrameworkTestCase
{
    /**
     * Test Translator initialization
     */
    public function testInvalidConfigurationThrowException()
    {
        $this->expectException(ConfigurationException::class);
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
        $translator->setPath(PATH_APPLICATION . '/locale');

        self::assertEquals('', $translator->translate(''));
        self::assertEquals('message', $translator->translate('message'));
    }

    /**
     * Test Plural Translate
     */
    public function testPluralTranslate()
    {
        $translator = new Translator();
        $translator->setDomain('messages');
        $translator->setLocale('uk_UA');
        $translator->setPath(PATH_APPLICATION . '/locale');

        self::assertEquals('', $translator->translatePlural('', '', 2));
        if (function_exists('ngettext')) {
            self::assertEquals('messages', $translator->translatePlural('message', 'messages', 2));
        } else {
            self::assertEquals('message', $translator->translatePlural('message', 'messages', 2));
        }
    }

    /**
     * Test Plural Translate
     */
    public function testPluralTranslateWithAdditionalParams()
    {
        $translator = new Translator();
        $translator->setDomain('messages');
        $translator->setLocale('uk_UA');
        $translator->setPath(PATH_APPLICATION . '/locale');

        if (function_exists('ngettext')) {
            self::assertEquals(
                '2 messages',
                $translator->translatePlural('%d message', '%d messages', 2, 2)
            );
        } else {
            self::assertEquals(
                '2 message',
                $translator->translatePlural('%d message', '%d messages', 2, 2)
            );
        }
    }
}
