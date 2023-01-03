<?php

namespace ToDoPlanner\Task\Group;

class Collection implements \JsonSerializable
{

    /**
     * @var array
     */
    private array $list = [];

    public function addGroup(Group $group): void
    {
        $this->list[] = $group;
    }

    public function jsonSerialize(): array
    {
        $list_serialized = [];
        foreach ($this->list as $group)
        {
            $list_serialized[] = $group->jsonSerialize();
        }
        return $list_serialized;
    }

}