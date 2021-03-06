<?php
/**
 * Markdown extended parser
 *
 * @package Greengrape
 */

namespace Greengrape;

use dflydev\markdown\MarkdownExtraParser;

/**
 * MarkdownExtendedParser
 *
 * Based on changes in https://github.com/egil/php-markdown-extra-extended
 *
 * @uses MarkdownExtraParser
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class MarkdownExtendedParser extends MarkdownExtraParser
{
    /**
     * Tags that are always treated as block tags
     *
     * @var string
     */
    public $block_tags_re = 'figure|figcaption|p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|address|form|fieldset|iframe|hr|legend';

    /**
     * Constructor
     *
     * @param array $configuration
     * @return void
     */
    public function __construct(array $configuration = null)
    {
        $this->block_gamut += array(
            "doFencedFigures" => 7,
        );

        parent::__construct($configuration);
    }

    /**
     * Transform
     *
     * @param mixed $text
     * @return void
     */
    public function transform($text)
    {
        $text = parent::transform($text);
        return $text;
    }

    /**
     * Do block quotes
     *
     * @param string $text Text
     * @return string
     */
    public function doBlockQuotes($text)
    {
        $text = preg_replace_callback('/
            (?>^[ ]*>[ ]?
                (?:\((.+?)\))?
                [ ]*(.+\n(?:.+\n)*)
            )+
            /xm',
            array(&$this, '_doBlockQuotes_callback'), $text);

        return $text;
    }

    /**
     * Do block quotes callback
     *
     * @param array $matches Matches
     * @return string
     */
    public function _doBlockQuotes_callback($matches)
    {
        $cite = $matches[1];
        $bq = '> ' . $matches[2];
        # trim one level of quoting - trim whitespace-only lines
        $bq = preg_replace('/^[ ]*>[ ]?|^[ ]+$/m', '', $bq);
        $bq = $this->runBlockGamut($bq);        # recurse

        $bq = preg_replace('/^/m', "  ", $bq);
        # These leading spaces cause problem with <pre> content,
        # so we need to fix that:
        $bq = preg_replace_callback('{(\s*<pre>.+?</pre>)}sx',
            array(&$this, '_doBlockQuotes_callback2'), $bq);

        $res = "<blockquote";
        $res .= empty($cite) ? ">" : " cite=\"$cite\">";
        $res .= "\n$bq\n</blockquote>";
        return "\n". $this->hashBlock($res)."\n\n";
    }

    /**
     * Do fenced code blocks
     *
     * @param string $text Text
     * @return string
     */
    public function doFencedCodeBlocks($text)
    {
        $text = preg_replace_callback('{
                (?:\n|\A)
                # 1: Opening marker
                (
                    ~{3,}|`{3,} # Marker: three tilde or more.
                )

                [ ]?(\w+)?(?:,[ ]?(\d+))?[ ]* \n # Whitespace and newline following marker.

                # 3: Content
                (
                    (?>
                        (?!\1 [ ]* \n)    # Not a closing marker.
                        .*\n+
                    )+
                )

                # Closing marker.
                \1 [ ]* \n
            }xm',
            array(&$this, '_doFencedCodeBlocks_callback'), $text);

        return $text;
    }

    /**
     * _doFencedCodeBlocks_callback
     *
     * @param array $matches Matches
     * @return string
     */
    public function _doFencedCodeBlocks_callback($matches)
    {
        $codeblock = $matches[4];
        $codeblock = htmlspecialchars($codeblock, ENT_NOQUOTES);
        $codeblock = preg_replace_callback('/^\n+/',
            array(&$this, '_doFencedCodeBlocks_newlines'), $codeblock);
        $cb = empty($matches[3]) ? "<pre><code" : "<pre class=\"linenums:$matches[3]\"><code";
        $cb .= empty($matches[2]) ? ">" : " class=\"language-$matches[2]\">";
        $cb .= "$codeblock</code></pre>";
        return "\n\n".$this->hashBlock($cb)."\n\n";
    }

    /**
     * Do fenced figures
     *
     * @param string $text Text
     * @return string
     */
    public function doFencedFigures($text)
    {
        $text = preg_replace_callback('{
            (?:\n|\A)
            # 1: Opening marker
            (
                ={3,} # Marker: equal sign.
            )

            [ ]?(?:\[([^\]]+)\])?[ ]* \n # Whitespace and newline following marker.

            # 3: Content
            (
                (?>
                    (?!\1 [ ]?(?:\[([^\]]+)\])?[ ]* \n)    # Not a closing marker.
                    .*\n+
                )+
            )

            # Closing marker.
            \1 [ ]?(?:\[([^\]]+)\])?[ ]* \n
        }xm', array(&$this, '_doFencedFigures_callback'), $text);

        return $text;
    }

    /**
     * Do fenced figures callback
     *
     * @param array $matches Matches
     * @return string
     */
    public function _doFencedFigures_callback($matches)
    {
        # get figcaption
        $topcaption = empty($matches[2]) ? null : $this->runBlockGamut($matches[2]);
        $bottomcaption = empty($matches[4]) ? null : $this->runBlockGamut($matches[4]);
        $figure = $matches[3];
        $figure = $this->runBlockGamut($figure); # recurse

        $figure = preg_replace('/^/m', "  ", $figure);
        # These leading spaces cause problem with <pre> content,
        # so we need to fix that - reuse blockqoute code to handle this:
        $figure = preg_replace_callback('{(\s*<pre>.+?</pre>)}sx',
            array(&$this, '_doBlockQuotes_callback2'), $figure);

        $res = "<figure>";
        if(!empty($topcaption)){
            $res .= "\n<figcaption>$topcaption</figcaption>";
        }
        $res .= "\n$figure\n";
        if(!empty($bottomcaption) && empty($topcaption)){
            $res .= "<figcaption>$bottomcaption</figcaption>";
        }
        $res .= "</figure>";
        return "\n". $this->hashBlock($res)."\n\n";
    }
}
