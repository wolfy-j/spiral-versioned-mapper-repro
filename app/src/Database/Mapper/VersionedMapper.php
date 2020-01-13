<?php

declare(strict_types=1);

namespace App\Database\Mapper;

use Cake\Chronos\Chronos;
use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Command\Database\Insert;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\Mapper\Mapper as BaseMapper;

class VersionedMapper extends BaseMapper
{
    protected function isDirty($entity, Node $node, State $state)
    {
        $changes = array_udiff_assoc($this->fetchFields($entity), $state->getData(), [static::class, 'compare']);

        // One of the base fields is dirty
        if (!empty($changes)) {
            return true;
        }

        return false;
    }

    public function queueCreate($entity, Node $node, State $state): ContextCarrierInterface
    {
        /** @var Insert $cmd */
        $cmd = parent::queueCreate($entity, $node, $state);

        $state->register('created_at', Chronos::now(), true);
        $cmd->register('created_at', Chronos::now(), true);

        return $cmd;
    }


    public function queueUpdate($entity, Node $node, State $state): ContextCarrierInterface
    {
        if (!$this->isDirty($entity, $node, $state)) {
            return new \Cycle\ORM\Command\Branch\Nil();
        }

        $updateData = $node->getData();

        unset($updateData['version']);

        $updateNode = new Node(
            $node->getStatus(),
            $updateData,
            $node->getRole(),
        );

        /** @var Insert $cmd */
        $cmd = $this->queueImmutableUpdate($entity, $updateNode, $state);

        $state->register('updated_at', Chronos::now(), true);
        $cmd->register('updated_at', Chronos::now(), true);

        return $cmd;
    }

    public function queueDelete($entity, Node $node, State $state): CommandInterface
    {
        $deleteData = $node->getData();

        unset($deleteData['version']);

        $deleteNode = new Node(
            $node->getStatus(),
            $deleteData,
            $node->getRole(),
        );

        /** @var Insert $cmd */
        $cmd = $this->queueImmutableUpdate($entity, $deleteNode, $state);

        $state->register('deleted_at', Chronos::now(), true);
        $cmd->register('deleted_at', Chronos::now(), true);

        return $cmd;
    }

    protected function queueImmutableUpdate($entity, Node $node, State $state): ContextCarrierInterface
    {
        $columns = $this->fetchFields($entity);
        unset($columns[$this->primaryKey]);

        // sync the state
        $state->setStatus(Node::SCHEDULED_INSERT);
        $state->setData($columns);

        $insert = new Insert(
            $this->source->getDatabase(),
            $this->source->getTable(),
            $this->mapColumns($columns)
        );

        $insert->forward(Insert::INSERT_ID, $state, $this->primaryKey);

        return $insert;
    }

}
