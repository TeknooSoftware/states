<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

use Teknoo\States\Doctrine\Entity\AbstractStandardEntity;
use Teknoo\States\Doctrine\Entity\StandardTrait as StandardEntityTrait;
use Teknoo\States\Doctrine\Document\AbstractStandardDocument;
use Teknoo\States\Doctrine\Entity\StandardTrait as StandardDocumentTrait;

return [
    'Teknoo\UniversalPackage\States\AbstractStandardEntity' => AbstractStandardEntity::class,
    'Teknoo\UniversalPackage\States\StandardEntityTrait' => StandardEntityTrait::class,
    'Teknoo\UniversalPackage\States\AbstractStandardDocument' => AbstractStandardDocument::class,
    'Teknoo\UniversalPackage\States\StandardDocumentTrait' => StandardDocumentTrait::class,
];
