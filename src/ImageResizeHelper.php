<?php
declare(strict_types=1);

namespace ImageResizer;

class ImageResizeHelper
{
    /**
     * Scaling exactly with specified dimensions
     */
    const MODE_SCALE = 'scale';

    /**
     * Insert an image in the specified rectangle
     */
    const MODE_FIT = 'fit';

    /**
     * Insert an image in the specified rectangle, the rest inside the rectangle will be filled with white color
     */
    const MODE_FIT_PAD = 'fit-pad';

    /**
     * The image is placed on top of the specified rectangle and the outgoing edges are trimmed.
     * If the image is smaller than a rectangle, then empty spaces will be filled with white.
     */
    const MODE_CROP = 'crop';

    /**
     * The image is scaled so as to fill the rectangle, the outgoing edges will be cropped
     */
    const MODE_SCALE_CROP = 'scale-crop';

    /**
     * The threshold which allow to determine the quality of image
     */
    const QUALITY_THRESHOLD = 600;

    /**
     * Converts an image changing its size.
     * The source image doesn't touch.
     * If put source path to destination, result image will replaces the source.
     *
     * @param string $sourcePath
     * @param string $destinationPath
     * @param int $width
     * @param int $height
     * @param string $mode
     * @throws \Exception
     */
    public static function resize(string $sourcePath, string $destinationPath, int $width, int $height, string $mode): void
    {
        $quality = ($height + $width < self::QUALITY_THRESHOLD) ? 91 : 99;

        switch ($mode) {
            case self::MODE_SCALE:
                $cmd = 'convert ' . $sourcePath . ' -resize ' . $width . 'x' . $height . '! -strip -quality ' . $quality . ' ' . $destinationPath;
                break;
            case self::MODE_FIT:
                $cmd = 'convert ' . $sourcePath . ' -resize ' . $width . 'x' . $height . ' -strip -quality ' . $quality . ' ' . $destinationPath;
                break;
            case self::MODE_FIT_PAD:
                $cmd = 'convert ' . $sourcePath . ' -resize 300x300 -gravity center -background white -extent ' . $width . 'x' . $height . ' -strip -quality ' . $quality . ' ' . $destinationPath;
                break;
            case self::MODE_CROP:
                $cmd = 'convert ' . $sourcePath . ' -gravity center -background white -extent ' . $width . 'x' . $height . ' -strip -quality ' . $quality . ' ' . $destinationPath;
                break;
            case self::MODE_SCALE_CROP;
                $cmd = 'convert ' . $sourcePath . ' -resize ' . $width . 'x' . $height . '^ -gravity center -extent ' . $width . 'x' . $height . ' -strip -quality ' . $quality . ' ' . $destinationPath;
                break;
            default:
                throw new \Exception('Unexpected convert mode');
        }
        \exec($cmd, $outputArr, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception("During cmd execution error occurred.\nCommand: '" . $cmd . "'\nError code: " . $returnCode);
        }
    }

    /**
     * Returns image size as an array:
     * [
     *     0 => 300,    // width
     *     1 => 200     // height
     * ]
     *
     * @param string $sourcePath
     * @return array
     * @throws \Exception
     */
    public static function getImageSize(string $sourcePath): array
    {
        $cmd = 'identify -format "%[fx:w]x%[fx:h]" ' . $sourcePath;
        \exec($cmd, $outputArr, $returnCode);

        if ($returnCode !== 0 || !isset($outputArr[0])) {
            throw new \Exception("During cmd execution error occurred.\nCommand: '" . $cmd . "'\nError code: " . $returnCode);
        }

        return \array_map('intval', \explode('x', $outputArr[0], 2));
    }
}