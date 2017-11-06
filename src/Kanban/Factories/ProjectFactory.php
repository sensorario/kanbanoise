<?php

namespace Kanban\Factories;

use AppBundle\Entity\Project;

class ProjectFactory
{
    public static function buildWithNameAndOwner(string $name, string $owner) : Project
    {
        $project = new Project();

        $project->setName($name);
        $project->setOwner($owner);

        return $project;
    }
}
