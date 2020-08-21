<?php

declare(strict_types=1);

/**
 * This file is part of Part-DB (https://github.com/Part-DB/Part-DB-symfony).
 *
 * Copyright (C) 2019 - 2020 Jan Böhmer (https://github.com/jbtronics)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Repository;

class AttachmentRepository extends DBElementRepository
{
    /**
     * Gets the count of all private/secure attachments.
     */
    public function getPrivateAttachmentsCount(): int
    {
        $qb = $this->createQueryBuilder('attachment');
        $qb->select('COUNT(attachment)')
            ->where('attachment.path LIKE :like');
        $qb->setParameter('like', '\\%SECURE\\%%');
        $query = $qb->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    /**
     * Gets the count of all external attachments (attachments only containing an URL).
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getExternalAttachments(): int
    {
        $qb = $this->createQueryBuilder('attachment');
        $qb->select('COUNT(attachment)')
            ->where('attachment.path LIKE :http')
            ->orWhere('attachment.path LIKE :https');
        $qb->setParameter('http', 'http://%');
        $qb->setParameter('https', 'https://%');
        $query = $qb->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    /**
     * Gets the count of all attachments where an user uploaded an file.
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUserUploadedAttachments(): int
    {
        $qb = $this->createQueryBuilder('attachment');
        $qb->select('COUNT(attachment)')
            ->where('attachment.path LIKE :base')
            ->orWhere('attachment.path LIKE :media')
            ->orWhere('attachment.path LIKE :secure');
        $qb->setParameter('secure', '\\%SECURE\\%%');
        $qb->setParameter('base', '\\%BASE\\%%');
        $qb->setParameter('media', '\\%MEDIA\\%%');
        $query = $qb->getQuery();

        return (int) $query->getSingleScalarResult();
    }
}
