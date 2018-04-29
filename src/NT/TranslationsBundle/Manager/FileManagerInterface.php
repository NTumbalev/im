<?php

namespace NT\TranslationsBundle\Manager;

/**
 * File manager interface.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
interface FileManagerInterface
{
    /**
     * Create a new file.
     *
     * @param string $name
     * @param string $path
     * @return File
     */
    public function create($name, $path, $flush = false);

    /**
     * Returns a translation file according to the given name and path.
     *
     * @param string $name
     * @param string $path
     * @return File
     */
    public function getFor($name, $path);
}
