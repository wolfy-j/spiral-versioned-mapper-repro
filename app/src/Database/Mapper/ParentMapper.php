<?php

declare(strict_types=1);

namespace App\Database\Mapper;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;

class ParentMapper extends VersionedMapper
{
    protected function isDirty($entity, Node $node, State $state)
    {
        $changes = array_udiff_assoc($this->fetchFields($entity), $state->getData(), [static::class, 'compare']);

        // One of the base fields is dirty
        $isDirty = !empty($changes);

        if ($isDirty) {
            return true;
        }

        // `child` relation is dirty
        $oldChild = $node->getRelations()['child'];

        $isRelationDirty = $oldChild->version !== $entity->child->version;
        if ($isRelationDirty) {
            return true;
        }

        // `notes` relation is dirty
        $oldNotes = $node->getRelations()['notes'];
        // TODO Check HasMany diff
        $isManyRelationDirty = true;
        if ($isManyRelationDirty) {
            return true;
        }

        return false;
    }
}
