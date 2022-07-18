<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Infrastructure\Doctrine\Listener\NewJobListener;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(
    itemOperations: [
        'get',
        'delete',
    ],
    denormalizationContext: [
        'groups' => [Job::GROUP_WRITE, File::GROUP_WRITE, Rule::GROUP_WRITE]
    ],
    normalizationContext: [
        'groups' => [Job::GROUP_READ, File::GROUP_READ, Rule::GROUP_READ]
    ],
)]
#[ORM\EntityListeners([NewJobListener::class])]
class Job
{
    public const GROUP_READ = 'job:read';
    public const GROUP_WRITE = 'job:write';

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups([self::GROUP_READ])]
    private ?int $id = null;

    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(mappedBy: 'job', targetEntity: File::class, cascade: ['persist'])]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE, File::GROUP_READ, File::GROUP_WRITE])]
    #[Assert\NotBlank]
    private Collection $files;

    /**
     * @var Collection<int, Rule>
     */
    #[ORM\OneToMany(mappedBy: 'job', targetEntity: Rule::class, cascade: ['persist'])]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE, Rule::GROUP_READ, Rule::GROUP_WRITE])]
    #[Assert\NotBlank]
    private Collection $rules;

    #[ORM\Column(type: Types::STRING)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    #[Assert\NotBlank]
    private string $repositoryName;

    #[ORM\Column(type: Types::STRING)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    #[Assert\NotBlank]
    private string $commitName;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->rules = new ArrayCollection();
    }

    /**
     * @psalm-suppress InvalidNullableReturnType, NullableReturnStatement
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * @param array|Collection<int, File> $files
     */
    public function setFiles(array|Collection $files): void
    {
        if (is_array($files)) {
            $files = new ArrayCollection($files);
        }

        $this->files = $files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setJob($this);
        }
        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
        }
        return $this;
    }

    /**
     * @return Collection<int, Rule>
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    /**
     * @param array|Collection<int, Rule> $rules
     */
    public function setRules(array|Collection $rules): void
    {
        if (is_array($rules)) {
            $rules = new ArrayCollection($rules);
        }

        $this->rules = $rules;
    }

    public function addRule(Rule $rule): self
    {
        if (!$this->rules->contains($rule)) {
            $this->rules[] = $rule;
            $rule->setJob($this);
        }

        return $this;
    }

    public function removeRule(Rule $rule): self
    {
        if ($this->rules->contains($rule)) {
            $this->rules->removeElement($rule);
        }

        return $this;
    }

    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    public function setRepositoryName(string $repositoryName): void
    {
        $this->repositoryName = $repositoryName;
    }

    public function getCommitName(): string
    {
        return $this->commitName;
    }

    public function setCommitName(string $commitName): void
    {
        $this->commitName = $commitName;
    }
}
