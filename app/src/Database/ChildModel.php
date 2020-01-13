<?php

declare(strict_types=1);

namespace App\Database;

use App\Database\Mapper\ChildMapper;
use Cake\Chronos\Chronos;
use Cycle\Annotated\Annotation as Cycle;

/**
 * @Cycle\Entity(
 *     table="children",
 *     mapper=ChildMapper::class
 * )
 * @Cycle\Table(
 *     columns={
 *      "version": @Cycle\Column(type="primary"),
 *      "id": @Cycle\Column(type="integer"),
 *      "name": @Cycle\Column(type="string"),
 *      "created_at": @Cycle\Column(type="timestamp", typecast="typecast_datetime"),
 *      "updated_at": @Cycle\Column(type="timestamp", nullable=true, typecast="typecast_datetime"),
 *      "deleted_at": @Cycle\Column(type="timestamp", nullable=true, typecast="typecast_datetime")
 *     }
 * )
 */
class ChildModel
{
    public ?int $version = null;
    public ?int $id = null;
    public ?string $name = null;

    public ?Chronos $created_at = null;
    public ?Chronos $updated_at = null;
    public ?Chronos $deleted_at = null;
}
