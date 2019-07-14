<?php
declare(strict_types=1);

namespace test;

use ImageResizer\ImageResizeHelper;
use PHPUnit\Framework\TestCase;

class ImageResizeHelperTest extends TestCase
{
    const SOURCE_FILE_PATH = __DIR__ . '/resize_test.jpg';
    const RESIZED_TEST_FILE_NAME = 'resized_img_test.jpg';

    /**
     * @var string Path to temp resized file
     */
    private $tmpResizedPath = '';

    protected function setUp(): void
    {
        if (\file_exists($this->getTmpPath())) {
            \unlink($this->getTmpPath());
        }
    }

    protected function tearDown(): void
    {
        if (\file_exists($this->getTmpPath())) {
            \unlink($this->getTmpPath());
        }
    }

    /**
     * Test of getting image size
     *
     * @throws \Exception
     */
    public function testGetImageSize(): void
    {
        $size = ImageResizeHelper::getImageSize(self::SOURCE_FILE_PATH);

        $this->assertEquals(500, $size[0]);
        $this->assertEquals(381, $size[1]);
    }

    /**
     * Resize test
     *
     * @depends testGetImageSize
     * @throws \Exception
     */
    public function testResize(): void
    {
        $sourceFile = __DIR__ . '/resize_test.jpg';
        $destinationFile = $this->getTmpPath();

        // MODE_SCALE
        ImageResizeHelper::resize($sourceFile, $destinationFile, 220, 220, ImageResizeHelper::MODE_SCALE);
        $this->assertFileExists($destinationFile);
        $size = ImageResizeHelper::getImageSize($destinationFile);
        $this->assertEquals(220, $size[0]);
        $this->assertEquals(220, $size[1]);
        \unlink($destinationFile);

        // MODE_FIT
        ImageResizeHelper::resize($sourceFile, $destinationFile, 220, 220, ImageResizeHelper::MODE_FIT);
        $this->assertFileExists($destinationFile);
        $size = ImageResizeHelper::getImageSize($destinationFile);
        $this->assertEquals(220, $size[0]);
        $this->assertEquals(168, $size[1]);
        \unlink($destinationFile);

        // MODE_FIT fit bigger
        ImageResizeHelper::resize($sourceFile, $destinationFile, 600, 600, ImageResizeHelper::MODE_FIT);
        $this->assertFileExists($destinationFile);
        $size = ImageResizeHelper::getImageSize($destinationFile);
        $this->assertEquals(600, $size[0]);
        $this->assertEquals(457, $size[1]);
        \unlink($destinationFile);

        // MODE_FIT_PAD
        ImageResizeHelper::resize($sourceFile, $destinationFile, 220, 220, ImageResizeHelper::MODE_FIT_PAD);
        $this->assertFileExists($destinationFile);
        $size = ImageResizeHelper::getImageSize($destinationFile);
        $this->assertEquals(220, $size[0]);
        $this->assertEquals(220, $size[1]);
        \unlink($destinationFile);

        // MODE_CROP
        ImageResizeHelper::resize($sourceFile, $destinationFile, 220, 220, ImageResizeHelper::MODE_CROP);
        $this->assertFileExists($destinationFile);
        $size = ImageResizeHelper::getImageSize($destinationFile);
        $this->assertEquals(220, $size[0]);
        $this->assertEquals(220, $size[1]);
        \unlink($destinationFile);

        // MODE_CROP crop bigger
        ImageResizeHelper::resize($sourceFile, $destinationFile, 400, 400, ImageResizeHelper::MODE_CROP);
        $this->assertFileExists($destinationFile);
        $size = ImageResizeHelper::getImageSize($destinationFile);
        $this->assertEquals(400, $size[0]);
        $this->assertEquals(400, $size[1]);
        \unlink($destinationFile);

        // MODE_SCALE_CROP
        ImageResizeHelper::resize($sourceFile, $destinationFile, 220, 220, ImageResizeHelper::MODE_CROP);
        $this->assertFileExists($destinationFile);
        $size = ImageResizeHelper::getImageSize($destinationFile);
        $this->assertEquals(220, $size[0]);
        $this->assertEquals(220, $size[1]);
        \unlink($destinationFile);

        // MODE_SCALE_CROP scale crop bigger
        ImageResizeHelper::resize($sourceFile, $destinationFile, 600, 600, ImageResizeHelper::MODE_CROP);
        $this->assertFileExists($destinationFile);
        $size = ImageResizeHelper::getImageSize($destinationFile);
        $this->assertEquals(600, $size[0]);
        $this->assertEquals(600, $size[1]);
        \unlink($destinationFile);
    }

    /**
     * Test of throwing an exception
     *
     * @throws \Exception
     */
    public function testResizeException(): void
    {
        $this->expectException(\Exception::class);
        ImageResizeHelper::resize('', '', 220, 220, 'unexpected mode');
    }

    /**
     * Returns a path to tmp file
     *
     * @return string
     */
    private function getTmpPath(): string
    {
        if (empty($this->tmpResizedPath)) {
            $this->tmpResizedPath = \sys_get_temp_dir() . '/' . self::RESIZED_TEST_FILE_NAME;
        }
        return $this->tmpResizedPath;
    }
}