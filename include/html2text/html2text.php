<?php
/******************************************************************************
 * Copyright (c) 2010 Jevon Wright and others.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Jevon Wright - initial API and implementation
 ****************************************************************************/

/**
 * Tries to convert the given HTML into a plain text format - best suited for
 * e-mail display, etc.
 *
 * <p>In particular, it tries to maintain the following features:
 * <ul>
 *   <li>Links are maintained, with the 'href' copied over
 *   <li>Information in the &lt;head&gt; is lost
 * </ul>
 *
 * @param the $html
 *
 * @throws Html2TextException
 * @internal param \the $html input HTML
 * @return the HTML converted, as best as possible, to text
 */
function convert_html_to_text($html)
{
    $html = fix_newlines($html);

    $doc = new DOMDocument();
    if (!$doc->loadHTML($html)) {
        throw new Html2TextException('Could not load HTML - badly formed?', $html);
    }

    $output = iterate_over_node($doc);

    // remove leading and trailing spaces on each line
    $output = preg_replace("/[ \t]*\n[ \t]*/im", "\n", $output);

    // remove leading and trailing whitespace
    $output = trim($output);

    return $output;
}

/**
 * Unify newlines; in particular, \r\n becomes \n, and
 * then \r becomes \n. This means that all newlines (Unix, Windows, Mac)
 * all become \ns.
 *
 * @param text text with any number of \r, \r\n and \n combinations
 *
 * @return the fixed text
 */
function fix_newlines($text)
{
    // replace \r\n to \n
    $text = str_replace("\r\n", "\n", $text);
    // remove \rs
    $text = str_replace("\r", "\n", $text);

    return $text;
}

/**
 * @param $node
 *
 * @return null|string
 */
function next_child_name($node)
{
    // get the next child
    $nextNode = $node->nextSibling;
    while (null != $nextNode) {
        if ($nextNode instanceof DOMElement) {
            break;
        }
        $nextNode = $nextNode->nextSibling;
    }
    $nextName = null;
    if ($nextNode instanceof DOMElement && null != $nextNode) {
        $nextName = strtolower($nextNode->nodeName);
    }

    return $nextName;
}

/**
 * @param $node
 *
 * @return null|string
 */
function prev_child_name($node)
{
    // get the previous child
    $nextNode = $node->previousSibling;
    while (null != $nextNode) {
        if ($nextNode instanceof DOMElement) {
            break;
        }
        $nextNode = $nextNode->previousSibling;
    }
    $nextName = null;
    if ($nextNode instanceof DOMElement && null != $nextNode) {
        $nextName = strtolower($nextNode->nodeName);
    }

    return $nextName;
}

/**
 * @param $node
 *
 * @return string
 */
function iterate_over_node($node)
{
    if ($node instanceof DOMText) {
        return preg_replace("/\\s+/im", ' ', $node->wholeText);
    }
    if ($node instanceof DOMDocumentType) {
        // ignore
        return '';
    }

    $nextName = next_child_name($node);
    $prevName = prev_child_name($node);

    $name = strtolower($node->nodeName);

    // start whitespace
    switch ($name) {
        case 'hr':
            return "------\n";

        case 'style':
        case 'head':
        case 'title':
        case 'meta':
        case 'script':
            // ignore these tags
            return '';

        case 'h1':
        case 'h2':
        case 'h3':
        case 'h4':
        case 'h5':
        case 'h6':
            // add two newlines
            $output = "\n";
            break;

        case 'p':
        case 'div':
            // add one line
            $output = "\n";
            break;

        default:
            // print out contents of unknown tags
            $output = '';
            break;
    }

    // debug
    //$output .= "[$name,$nextName]";

    for ($i = 0; $i < $node->childNodes->length; ++$i) {
        $n = $node->childNodes->item($i);

        $text = iterate_over_node($n);

        $output .= $text;
    }

    // end whitespace
    switch ($name) {
        case 'style':
        case 'head':
        case 'title':
        case 'meta':
        case 'script':
            // ignore these tags
            return '';

        case 'h1':
        case 'h2':
        case 'h3':
        case 'h4':
        case 'h5':
        case 'h6':
            $output .= "\n";
            break;

        case 'p':
        case 'br':
            // add one line
            if ('div' !== $nextName) {
                $output .= "\n";
            }
            break;

        case 'div':
            // add one line only if the next child isn't a div
            if ('div' !== $nextName && null != $nextName) {
                $output .= "\n";
            }
            break;

        case 'a':
            // links are returned in [text](link) format
            $href = $node->getAttribute('href');
            if (null == $href) {
                // it doesn't link anywhere
                if (null != $node->getAttribute('name')) {
                    $output = "[$output]";
                }
            } else {
                if ($href == $output) {
                    // link to the same address: just use link
                    $output;
                } else {
                    // replace it
                    $output = "[$output]($href)";
                }
            }

            // does the next node require additional whitespace?
            switch ($nextName) {
                case 'h1':
                case 'h2':
                case 'h3':
                case 'h4':
                case 'h5':
                case 'h6':
                    $output .= "\n";
                    break;
            }

            // no break
        default:
            // do nothing
    }

    return $output;
}

/**
 * Class Html2TextException
 */
class Html2TextException extends Exception
{
    public $more_info;

    /**
     * @param string $message
     * @param string $more_info
     */
    public function __construct($message = '', $more_info = '')
    {
        parent::__construct($message);
        $this->more_info = $more_info;
    }
}
