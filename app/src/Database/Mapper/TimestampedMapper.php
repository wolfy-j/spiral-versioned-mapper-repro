<?php

declare(strict_types=1);

namespace App\Database\Mapper;

use Cake\Chronos\Chronos;
use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Command\Database\Insert;
use Cycle\ORM\Command\Database\Update;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\Mapper\Mapper as BaseMapper;

class TimestampedMapper extends BaseMapper
{
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
        /** @var Update $cmd */
        $cmd = parent::queueUpdate($entity, $node, $state);

        $state->register('updated_at', Chronos::now(), true);
        $cmd->registerAppendix('updated_at', Chronos::now());

        return $cmd;
    }
}
