<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Application\Controller\Action\CreateFileAction;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 */
#[ORM\Entity]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'controller' => CreateFileAction::class,
            'deserialize' => false,
            'validation_groups' => ['Default', 'file_create'],
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    iri: 'https://schema.org/MediaObject',
    itemOperations: ['get'],
    denormalizationContext: [
        'groups' => [File::GROUP_WRITE, Job::GROUP_WRITE]
    ],
    normalizationContext: [
        'groups' => [File::GROUP_READ, Job::GROUP_READ]
    ],
)]
class File
{
    public const GROUP_READ = 'file:read';
    public const GROUP_WRITE = 'file:write';

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups([self::GROUP_READ, Job::GROUP_READ])]
    private ?int $id = null;

    #[ApiProperty(iri: 'https://schema.org/contentUrl')]
    #[Groups([self::GROUP_READ, Job::GROUP_READ])]
    private ?string $contentUrl = null;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'files')]
    private Job $job;

    /**
     * @Vich\UploadableField(mapping="files", fileNameProperty="filePath")
     */
    #[Assert\NotNull(groups: ['file_create'])]
    private ?SymfonyFile $file = null;

    #[ORM\Column(nullable: true)]
    private ?string $filePath = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function setContentUrl(?string $contentUrl): void
    {
        $this->contentUrl = $contentUrl;
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function setJob(Job $job): void
    {
        $this->job = $job;
    }

    public function getFile(): ?SymfonyFile
    {
        return $this->file;
    }

    public function setFile(?SymfonyFile $file): void
    {
        $this->file = $file;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }
}
