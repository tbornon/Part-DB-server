<?php
/**
 * part-db version 0.1
 * Copyright (C) 2005 Christoph Lechner
 * http://www.cl-projects.de/.
 *
 * part-db version 0.2+
 * Copyright (C) 2009 K. Jacobs and others (see authors.php)
 * http://code.google.com/p/part-db/
 *
 * Part-DB Version 0.4+
 * Copyright (C) 2016 - 2019 Jan Böhmer
 * https://github.com/jbtronics
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 */

namespace App\Services;

use App\Entity\Category;
use App\Entity\NamedDBElement;
use App\Entity\Part;
use App\Exceptions\EntityNotSupported;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EntityURLGenerator
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Generates an URL to the page using the given page type and element.
     * For the given types, the [type]URL() functions are called (e.g. infoURL()).
     * Not all entity class and $type combinations are supported.
     *
     * @param $entity mixed The element for which the page should be generated.
     * @param string $type The page type. Currently supported: 'info', 'edit', 'create', 'clone', 'list'/'list_parts'
     * @return string The link to the desired page.
     * @throws EntityNotSupported Thrown if the entity is not supported for the given type.
     * @throws \InvalidArgumentException Thrown if the givent type is not existing.
     */
    public function getURL($entity, string $type)
    {
        switch ($type) {
            case 'info':
                return $this->infoURL($entity);
            case 'edit':
                return $this->editURL($entity);
            case 'create':
                return $this->createURL($entity);
            case 'clone':
                return $this->cloneURL($entity);
            case 'list':
            case 'list_parts':
                return $this->listPartsURL($entity);
        }

        throw new \InvalidArgumentException('Method is not supported!');
    }

    /**
     * Generates an URL to a page, where info about this entity can be viewed.
     *
     * @param $entity mixed The entity for which the info should be generated.
     * @return string The URL to the info page
     * @throws EntityNotSupported If the method is not supported for the given Entity
     */
    public function infoURL($entity): string
    {
        if ($entity instanceof Part) {
            return $this->urlGenerator->generate('part_info', ['id' => $entity->getID()]);
        }

        //Otherwise throw an error
        throw new EntityNotSupported('The given entity is not supported yet!');
    }

    /**
     * Generates an URL to a page, where this entity can be edited.
     *
     * @param $entity mixed The entity for which the edit link should be generated.
     * @return string The URL to the edit page.
     * @throws EntityNotSupported If the method is not supported for the given Entity
     */
    public function editURL($entity): string
    {
        if ($entity instanceof Part) {
            return $this->urlGenerator->generate('part_edit', ['id' => $entity->getID()]);
        }

        //Otherwise throw an error
        throw new EntityNotSupported('The given entity is not supported yet!');
    }

    /**
     * Generates an URL to a page, where a entity of this type can be created.
     *
     * @param $entity mixed The entity for which the link should be generated.
     * @return string The URL to the page.
     * @throws EntityNotSupported If the method is not supported for the given Entity
     */
    public function createURL($entity): string
    {
        if ($entity instanceof Part) {
            return $this->urlGenerator->generate('part_new');
        }

        throw new EntityNotSupported('The given entity is not supported yet!');
    }

    /**
     * Generates an URL to a page, where a new entity can be created, that has the same informations as the
     * given entity (element cloning)
     *
     * @param $entity mixed The entity for which the link should be generated.
     * @return string The URL to the page.
     * @throws EntityNotSupported If the method is not supported for the given Entity
     */
    public function cloneURL($entity): string
    {
        if ($entity instanceof Part) {
            return $this->urlGenerator->generate('part_clone', ['id' => $entity->getID()]);
        }

        throw new EntityNotSupported('The given entity is not supported yet!');
    }

    /**
     * Generates an URL to a page, where all parts are listed, which are contained in the given element.
     *
     * @param $entity mixed The entity for which the link should be generated.
     * @return string The URL to the page.
     * @throws EntityNotSupported If the method is not supported for the given Entity
     */
    public function listPartsURL($entity) : string
    {
        if ($entity instanceof Category) {
            return $this->urlGenerator->generate('app_partlists_showcategory', ['id' => $entity->getID()]);
        }
        throw new EntityNotSupported('The given entity is not supported yet!');

    }

    /**
     * Generates an HTML link to the info page about the given entity.
     *
     * @param $entity mixed The entity for which the info link should be generated.
     *
     * @return string The HTML of the info page link
     *
     * @throws EntityNotSupported
     */
    public function infoHTML($entity): string
    {
        $href = $this->infoURL($entity);

        if ($entity instanceof NamedDBElement) {
            return sprintf('<a href="%s">%s</a>', $href, $entity->getName());
        }

        throw new EntityNotSupported('The given entity is not supported yet!');
    }
}
