<?php

namespace App\Tests\Units;

use App\Entity\Media;
use App\Entity\User;
use App\Entity\Album;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaTest extends TestCase
{
    public function testMediaUser() : void
    {
        $user = new User();

        $dataMedia = [
            'user' => $user,
            'title' => 'title',
            'path' => 'path'
        ];

        $media = new Media();
        $media->setUser($dataMedia['user']);
        $media->setTitle($dataMedia['title']);
        $media->setPath($dataMedia['path']);

        $this->assertEquals($dataMedia['user'], $media->getUser());
        $this->assertEquals($dataMedia['title'], $media->getTitle());
        $this->assertEquals($dataMedia['path'], $media->getPath());
    }

    public function testMediaUpload() : void
    {
        $file = $this->createMock(UploadedFile::class);
        $file->method('getClientOriginalName')->willReturn('image.jpg');
        $file->method('guessExtension')->willReturn('image/jpg');
        $file->method('isValid')->willReturn(true);

        $album = new Album();

        $media = new Media();
        $media->setFile($file);
        $media->setAlbum($album);

        $this->assertEquals($file, $media->getFile());
        $this->assertEquals($album, $media->getAlbum());
    }
}