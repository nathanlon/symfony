<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Iterator;

/**
 * ExcludeDirectoryFilterIterator filters out directories.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ExcludeDirectoryFilterIterator extends \FilterIterator
{
    private $patterns;

    /**
     * Constructor.
     *
     * @param \Iterator $iterator      The Iterator to filter
     * @param array     $directories   An array of directories to exclude
     * @param array     $extraPatterns An array of regex patterns to exclude
     */
    public function __construct(\Iterator $iterator, array $directories, array $extraPatterns = null)
    {
        $this->patterns = array();
        foreach ($directories as $directory) {
            $this->patterns[] = '#(^|/)'.preg_quote($directory, '#').'(/|$)#';
        }

        if (!is_null($extraPatterns)) {
            foreach ($extraPatterns as $pattern) {
                $this->patterns[] = $pattern;
            }
        }

        parent::__construct($iterator);
    }

    /**
     * Filters the iterator values.
     *
     * @return Boolean true if the value should be kept, false otherwise
     */
    public function accept()
    {
        $path = $this->isDir() ? $this->current()->getRelativePathname() : $this->current()->getRelativePath();
        $path = strtr($path, '\\', '/');
        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $path)) {
                return false;
            }
        }

        return true;
    }
}
