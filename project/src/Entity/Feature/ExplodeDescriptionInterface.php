<?php

namespace App\Entity\Feature;

interface ExplodeDescriptionInterface
{
    public function getDescription(): string;

    public function setDescription(string $description): static;
}