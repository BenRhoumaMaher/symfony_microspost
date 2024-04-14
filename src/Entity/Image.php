<?php

/**
 * Image.php
 *
 * This file contains the definition of the Image class, which represents an image
 * entity in the application.
 *
 * @category Entities
 * @package  App\Entity
 * @author   Your Name <your.email@example.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    PHP 8.2
 */

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]

/**
 * Image
 *
 * @category Entities
 *
 * @package App\Entity
 *
 * @author Your Name <your.email@example.com>
 *
 * @license No license (Personal project)
 *
 * @link https://symfony.com/doc/current/controller.html
 */
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $path = null;

    /**
     * Gets the ID of the image.
     *
     * @return int|null The ID of the image, or null if not set.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the path of the image.
     *
     * @return string|null The path of the image, or null if not set.
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Sets the path of the image.
     *
     * @param string|null $path The path to set.
     *
     * @return static The updated entity instance.
     */
    public function setPath(?string $path): static
    {
        $this->path = $path;

        return $this;
    }
}
