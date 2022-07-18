<?php

declare(strict_types=1);

namespace App\Infrastructure\ApiClient;

use App\Domain\Entity\File;
use App\Domain\Enum\ScanFileEnum;
use Doctrine\Common\Collections\Collection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * @psalm-suppress MixedMethodCall
 */
class DebrickedClient implements DebrickedClientInterface
{
    private Client $httpClient;

    private array $httpHeaders = [];

    public function __construct(
        private readonly StorageInterface $storage,
        private readonly string $projectDir,
        private readonly string $userName,
        private readonly string $password,
        string $apiUrl
    ) {
        $this->httpClient = new Client([
            'base_uri' => $apiUrl . '/1.0/'
        ]);
        $this->generateNewAccessToken();
    }

    public function setHttpHeaders(array $httpHeaders): void
    {
        $this->httpHeaders = $httpHeaders;
    }

    public function getHttpHeaders(): array
    {
        return $this->httpHeaders;
    }

    public function buildHttpHeaders(array $httpHeaders = []): array
    {
        return array_merge_recursive(
            $this->getHttpHeaders(),
            $httpHeaders
        );
    }

    public function generateNewAccessToken(): void
    {
        $this->setHttpHeaders(
            $this->buildHttpHeaders(
                $this->buildAuthHeaders(
                    $this->getJWTToken()
                )
            )
        );
    }

    public function checkStatusFileScanning(string $ciUploadId): array
    {
        $query = http_build_query([
            'ciUploadId' => $ciUploadId,
            'extendedOutput' => '*'
        ]);
        $response = null;

        try {
            $response = $this->httpClient->get('open/ci/upload/status?' . $query, $this->getHttpHeaders());
            $responseStatusCode = $response->getStatusCode();
        } catch (GuzzleException $e) {
            $responseStatusCode = $e->getCode();
        }

        switch ($responseStatusCode) {
            case 200:
                $data = [
                    'status' => ScanFileEnum::COMPLETED,
                    'stats' => \json_decode(
                        $response?->getBody()?->getContents() ?? '',
                        true,
                        512,
                        JSON_THROW_ON_ERROR
                    )
                ];
                break;
            case 201:
                $data = [
                    'status' => ScanFileEnum::NOT_COMPLETED
                ];
                break;
            case 202:
                $data = [
                    'status' => ScanFileEnum::IN_PROGRESS
                ];
                break;
            default:
                $data = [
                    'status' => ScanFileEnum::FAILED
                ];
                break;
        }

        return $data;
    }

    /**
     * @psalm-suppress MoreSpecificImplementedParamType
     * @param Collection<int, File> $files
     * @param string $repositoryName
     * @param string $commitName
     * @return string
     * @throws \JsonException
     */
    public function scanFiles(Collection $files, string $repositoryName, string $commitName): string
    {
        $ciUploadId = null;

        if ($files->isEmpty()) {
            throw new \DomainException('Job files cannot be empty');
        }

        foreach ($files as $file) {
            $newCIUploadId = $this->uploadFile($file, $repositoryName, $commitName, $ciUploadId);

            if (empty($ciUploadId)) {
                $ciUploadId = $newCIUploadId;
            }
        }

        if ($ciUploadId === null) {
            throw new \DomainException('CI upload ID cannot be empty');
        }

        $this->finishUploadFiles($repositoryName, $commitName, $ciUploadId);

        return $ciUploadId;
    }

    protected function getJWTToken(): string
    {
        $response = $this->httpClient->post(
            '/api/login_check',
            [
                'form_params' => [
                    '_username' => $this->userName,
                    '_password' => $this->password,
                ]
            ]
        );

        $data = \json_decode(
            $response->getBody()->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return (string) ($data['token'] ?? '');
    }

    protected function uploadFile(
        File $file,
        string $repositoryName,
        string $commitName,
        ?string $ciUploadId = null
    ): string {
        $filePath = $this->storage->resolveUri($file, 'file');
        if ($filePath === null) {
            throw new \RuntimeException('File cannot be null');
        }
        $fileFullPath = $this->projectDir . $filePath;

        $requestBody = [
            'multipart' => [
                [
                    'name' => 'fileData',
                    'contents' => file_get_contents($fileFullPath),
                    'filename' => $file->getFilePath()
                ],
                [
                    'name' => 'repositoryName',
                    'contents' => $repositoryName
                ],
                [
                    'name' => 'commitName',
                    'contents' => $commitName
                ],
            ]
        ];

        if (!empty($ciUploadId)) {
            $requestBody['multipart'] = array_merge(
                $requestBody['multipart'],
                [
                    [
                        'name' => 'ciUploadId',
                        'contents' => $ciUploadId
                    ]
                ]
            );
        }

        $response = $this->httpClient->post(
            'open/uploads/dependencies/files',
            array_merge($requestBody, $this->getHttpHeaders())
        );

        $data = \json_decode(
            $response->getBody()->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return (string) $data['ciUploadId'];
    }

    protected function finishUploadFiles(
        string $repositoryName,
        string $commitName,
        string $ciUploadId
    ): void {
        $this->httpClient->post(
            'open/finishes/dependencies/files/uploads',
            array_merge(
                [
                    'json' => [
                        'ciUploadId' => $ciUploadId,
                        'repositoryName' => $repositoryName,
                        'commitName' => $commitName,
                    ]
                ],
                $this->getHttpHeaders()
            )
        );
    }

    private function buildAuthHeaders(string $apiToken): array
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiToken
            ]
        ];
    }
}
