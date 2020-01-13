<?php

declare(strict_types=1);

namespace App\Database;

use Cake\Chronos\Chronos;
use Cycle\Annotated\Annotation as Cycle;
use App\Database\Mapper\TimestampedMapper;

/**
 * @Cycle\Entity(
 *     table="notes",
 *     mapper=TimestampedMapper::class
 * )
 * @Cycle\Table(
 *     columns={
 *      "id": @Cycle\Column(type="primary"),
 *      "text": @Cycle\Column(type="string"),
 *      "created_at": @Cycle\Column(type="timestamp", typecast="typecast_datetime"),
 *      "updated_at": @Cycle\Column(type="timestamp", nullable=true, typecast="typecast_datetime")
 *     }
 * )
 */
class NoteModel
{
    public ?int $id = null;
    public ?string $text = null;

    /**
     * @Cycle\Relation\BelongsTo(target=ParentModel::class, innerKey="parent_id", fkCreate=false, indexCreate=false)
     * @var ParentModel
     */
    public $parent = null;

    public ?Chronos $created_at = null;
    public ?Chronos $updated_at = null;
    public ?Chronos $deleted_at = null;
}
