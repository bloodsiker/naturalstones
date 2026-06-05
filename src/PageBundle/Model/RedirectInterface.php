<?php

namespace PageBundle\Model;

interface RedirectInterface
{
    public function getId(): ?int;

    public function setIsActive(bool $isActive): self;

    public function getIsActive(): bool;

    public function setFromPath(?string $fromPath): self;

    public function getFromPath(): ?string;

    public function setToPath(?string $toPath): self;

    public function getToPath(): ?string;
}