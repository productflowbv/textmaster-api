<?php

/*
 * This file is part of the Textmaster Api v1 client package.
 *
 * (c) Christian Daguerre <christian@daguer.re>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Textmaster\Model;

interface AuthorInterface extends AbstractObjectInterface
{
    /**
     * Save the object.
     *
     * @return AuthorInterface
     */
    public function save();

    /**
     * Get author ID
     *
     * @return string
     */
    public function getAuthorId();

    /**
     * Set author ID
     *
     * @param string $authorId
     *
     * @return AuthorInterface
     */
    public function setAuthorId($authorId);
}
