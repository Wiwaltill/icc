<?php

namespace App\Filesystem;

use App\Entity\Document;
use App\Entity\DocumentAttachment;
use App\Http\FlysystemFileResponse;
use League\Flysystem\FilesystemInterface;
use Mimey\MimeTypes;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class DocumentFilesystem implements DirectoryNamerInterface {

    private $filesystem;
    private $mimeTypes;
    private $logger;

    public function __construct(FilesystemInterface $filesystem, MimeTypes $mimeTypes, LoggerInterface $logger = null) {
        $this->filesystem = $filesystem;
        $this->mimeTypes = $mimeTypes;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param DocumentAttachment $attachment
     * @return Response
     * @throws FileNotFoundException
     */
    public function getDownloadResponse(DocumentAttachment $attachment): Response {
        $path = $this->getAttachmentPath($attachment);

        if(!$this->filesystem->has($path)) {
            $this->logger->alert('Cannot send document attachment as file does not exist on the filesystem', [
                'file' => $path
            ]);

            throw new FileNotFoundException();
        }

        $extension = pathinfo($attachment->getFilename(), PATHINFO_EXTENSION);
        $mimeType = $this->mimeTypes->getMimeType($extension);

        return new FlysystemFileResponse($this->filesystem, $this->getAttachmentPath($attachment), $attachment->getFilename(), $mimeType);
    }

    public function removeDocumentAttachment(DocumentAttachment $attachment): void {
        $path = $this->getAttachmentPath($attachment);

        if($attachment->getId() !== null && $attachment->getDocument() !== null && $attachment->getDocument()->getId() !== null && $this->filesystem->has($path)) {
            $this->filesystem->delete($this->getAttachmentPath($attachment));
        }
    }

    public function removeDocumentDirectory(Document $document): void {
        if($document->getId() !== null) {
            $this->filesystem->deleteDir($this->getAttachmentsDirectory($document));
        }
    }

    private function getAttachmentsDirectory(Document $document): string {
        return sprintf('/%s/', $document->getUuid());
    }

    private function getAttachmentPath(DocumentAttachment $attachment): string {
        return sprintf('/%s/%s', $attachment->getDocument()->getUuid(), $attachment->getPath());
    }

    /**
     * @param DocumentAttachment $object
     * @param PropertyMapping $mapping
     * @return string
     */
    public function directoryName($object, PropertyMapping $mapping): string {
        return $this->getAttachmentsDirectory($object->getDocument());
    }
}