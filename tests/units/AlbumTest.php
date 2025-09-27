<?php 

namespace  App\tests\units;

use App\Entity\Album;
use PHPUnit\Framework\TestCase;

class AlbumTest extends TestCase
{
    public function testAlbum(): void
    {
        $dataAlbum = [
            'name' => 'test'
        ];

        $album = new Album();
        $album->setName($dataAlbum['name']);
        $this->assertEquals($dataAlbum['name'], $album->getName());
    }
}