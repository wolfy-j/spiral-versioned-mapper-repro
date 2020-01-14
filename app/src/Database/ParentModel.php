<?php

declare(strict_types=1);

namespace App\Database;

use App\Database\Mapper\ParentMapper;
use Cycle\Annotated\Annotation as Cycle;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Cycle\Entity(
 *     table="parents",
 *     mapper=ParentMapper::class
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
class ParentModel
{
    public $version = null;
    public $id = null;
    public $name = null;

    /**
     * @Cycle\Relation\BelongsTo(target=ChildModel::class, innerKey="child_id", fkCreate=false, indexCreate=false)
     * @var ChildModel
     */
    public $child = null;

    /**
     * @Cycle\Relation\HasMany(target=NoteModel::class, outerKey="parent_id", fkCreate=false, indexCreate=false)
     * @var ArrayCollection
     */
    public $notes;

    public $created_at = null;
    public $updated_at = null;
    public $deleted_at = null;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }
}
