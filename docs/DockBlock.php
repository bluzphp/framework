<?php
/**
 * An complex example of how to write "dockblock", based on PEAR standard
 *
 * PEAR standard you can find at http://pear.php.net/manual/tr/standards.sample.php
 *
 * Docblock comments start with "/**" at the top.  Notice how the "/"
 * lines up with the normal indenting and the asterisks on subsequent rows
 * are in line with the first asterisk. The last line of comment text
 * should be immediately followed on the next line by the closing asterisk
 * and slash and then the item you are commenting on should be on the next
 * line below that. Don't add extra lines. Please put a blank line
 * between paragraphs as well as between the end of the description and
 * the start of the tags. Wrap comments before 80 columns in order to
 * ease readability for a wide variety of users.
 *
 * Docblocks can only be used for programming constructs which allow them
 * (classes, properties, methods, defines, includes, globals). See the
 * phpDocumentor documentation for more information.
 * http://phpdoc.org/docs/latest/index.html
 *
 * The Javadoc Style Guide is an excellent resource for figuring out
 * how to say what needs to be said in docblock comments. Much of what is
 * written here is a summary of what is found there, though there are some
 * cases where what's said here overrides what is said there.
 * http://www.oracle.com/technetwork/java/javase/documentation/index-137868.html
 *
 * The first line of any docblock is the summary. Make them one short
 * sentence, without a period at the end. Summaries for classes, properties
 * and constants should omit the subject and simply state the object,
 * because they are describing things rather than actions or behaviors.
 */

/**
 * Short description for file
 *
 * Usually this block is the same for all files in your project
 * It's should consists the following tags:
 *  - copyright string
 *  - license with link to full text
 *  - link to library repository or project homepage
 * All other information should be write in class dockblock
 *
 * Syntax and order of tags:
 * @.copyright [description]
 * @.license   [<url>] [name]
 * @.link      [URI] [<description>]
 *
 * @copyright Bluz PHP Team
 * @license   MIT
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz;

/**
 * Short summary for class
 *
 * You should know the simple rule - one class in one file,
 * then all information about package, author, version, etc
 * you can write in dockblock of class
 *
 * Syntax and order of tags:
 * @.package    [level 1]\[level 2]\[etc.]
 * @.author     [name] [<email address>]
 * @.version    [<vector>] [<description>]
 * @.link       [URI] [<description>]
 * @.see        [URI | FQSEN] [<description>]
 * @.since      [version] [<description>]
 * @.deprecated [version] [<description>]
 *
 * Then you can describe magic methods and properties of class,
 * it's required for autosuggestion mechanism in IDE
 *
 * Syntax and order of magic properties and methods:
 * @.property   [Type] [name] [<description>]
 * @.method     [return type] [name]([[type] [parameter]<, ...>]) [<description>]
 *
 * @package     Bluz
 * @author      Anton Shevchuk <Anton.Shevchuk@gmail.com>
 * @version     1.5.0 release version
 * @link        https://github.com/bluzphp/framework
 * @see         DockBlock, DockBlock::setFoo()
 * @since       1.0.0 first time this was introduced
 * @deprecated  2.0.0 no longer used by internal code and not recommended
 *
 * @property    integer $number
 * @method      integer getMagicNumber()
 */
class DockBlock
{
    /**
     * Short summary for property is optional, but recommended
     *
     * Syntax and order of tags:
     * @.link [URI] [<description>]
     * @.see  [URI | FQSEN] [<description>]
     * @.var  ["Type"] [$element_name] [<description>]
     *
     * @link https://github.com/bluzphp/framework
     * @see  DockBlock
     * @var  string Should contain a description
     */
    protected $foo = 'bar';

    /**
     * Registers the status of foo's universe
     *
     * Summaries for methods should use 3rd person declarative rather
     * than 2nd person imperative, beginning with a verb phrase.
     *
     * Summaries should add description beyond the method's name. The
     * best method names are "self-documenting", meaning they tell you
     * basically what the method does.  If the summary merely repeats
     * the method name in sentence form, it is not providing more
     * information.
     *
     * Below are the tags commonly used for methods.  A `param` tag is
     * required for each parameter the method has.  The `return` tag are
     * mandatory.  The `throws` tag is required if the method uses exceptions.
     * The remainder should only be used when necessary.
     * Please use them in the order they appear here. phpDocumentor has
     * several other tags available, feel free to use them.
     *
     * The `param` tag contains the data type, then the parameter's
     * name, followed by a description.  By convention, the first noun in
     * the description is the data type of the parameter.  Articles like
     * "a", "an", and  "the" can precede the noun. The descriptions
     * should start with a phrase. If further description is necessary,
     * follow with sentences. Having two spaces between the name and the
     * description aids readability.
     *
     * When writing a phrase, do not capitalize and do not end with a period.
     * When writing a phrase followed by a sentence, do not capitalize the
     * phrase, but end it with a period to distinguish it from the start
     * of the next sentence
     *
     * Return tags should contain the data type then a description of
     * the data returned. The data type can be any of PHP's data types
     * (int, float, bool, string, array, object, resource, mixed)
     * and should contain the type primarily returned. For example, if
     * a method returns an object when things work correctly but false
     * when an error happens, say 'object' rather than 'mixed'.
     * Use 'void' if nothing is returned.
     *
     * Here's an example of how to format examples:
     * <code>
     * try {
     *     $dockBlock = new DockBlock();
     *     $dockBlock->setFoo('Bar');
     * } catch (\Exception $e) {
     *     echo $e->getMessage();
     * }
     * </code>
     *
     * Syntax and order of tags:
     * @.param      [Type] [name] [<description>]
     * @.return     [Type] [<description>]
     * @.throws     [Type] [<description>]
     *
     * @.see        [URI | FQSEN] [<description>]
     * @.since      [version] [<description>]
     * @.deprecated [version] [<description>]
     *
     * @param  string $arg1 the string to quote
     * @param  int    $arg2 an integer of how many problems happened.
     *                      Indent to the description's starting point
     *                      for long ones.
     *
     * @return int the integer of the set mode used. FALSE if foo
     *             foo could not be set.
     *
     * @throws \Exception if first argument is not a string
     *
     * @see        DockBlock::$foo, DockBlock::setFoo()
     * @since      1.3.0 Added the $arg2
     * @since      1.2.0
     * @deprecated 2.0.0
     */
    public function setFoo($arg1, $arg2 = 0)
    {
        /*
         * This is a "Block Comment." The format is the same as
         * Docblock Comments except there is only one asterisk at the
         * top. phpDocumentor doesn't parse these.
         */
        if (is_int($arg1)) {
            throw new \Exception("First argument should be string");
        }

        if ($arg1 == 'good' || $arg1 == 'fair') {
            $this->foo = $arg1;
            return 1;
        } elseif ($arg1 == 'poor' && $arg2 > 1) {
            $this->foo = 'poor';
            return 2;
        } else {
            return false;
        }
    }
}
