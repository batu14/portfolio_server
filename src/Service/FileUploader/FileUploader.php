<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;
    private $safeExtensions; 

    public function __construct(string $targetDirectory,  array $safeExtensions) 
    {
        $this->targetDirectory = $targetDirectory;
        $this->safeExtensions = $safeExtensions;
    }


    public function uploadMulti(array $files): array
    {
        $fileNames = [];
        foreach ($files as $file) {
            $fileNames[] = $this->upload($file);
        }
        return $fileNames;
    }


    public function upload(UploadedFile $file): ?string
    {
       
        $extension = $file->guessExtension();
        if (!$extension || !in_array($extension, $this->safeExtensions)) {
           
            throw new \InvalidArgumentException('Desteklenmeyen dosya uzantısı.');
        }

        $newFilename =uniqid() . '.' . $extension;

        try {
           
            $file->move($this->getTargetDirectory(), $newFilename);
        } catch (FileException $e) {
            throw new \Exception('Dosya yüklenirken bir hata oluştu: ' . $e->getMessage());
           
        }

        
        return $newFilename;
    }


    public function delete(string $filename): bool
    {
        $filePath = $this->getTargetDirectory() . '/' . $filename;

        if (file_exists($filePath)) {
            try {
                unlink($filePath);
                return true;
            } catch (\Exception $e) {
                throw new \Exception('Dosya silinirken bir hata oluştu: ' . $e->getMessage());
               
            }
        }

        return false; 
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
