<?php

declare(strict_types=1);

namespace App\Database;

use App\Database\Mapper\ChildMapper;
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
 *      "deleted_at": @Cycle\Column(type="timestamp", nullable=true, typecast="Cake\Chronos\Chronos::parse")
 *     }
 * )
 */
class ChildModel
{
    public $version = null;
    public $id = null;
    public $name = null;

    public $created_at = null;
    public $updated_at = null;
    public $deleted_at = null;
}
