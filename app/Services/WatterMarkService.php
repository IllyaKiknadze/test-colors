<?php


namespace App\Services;

use League\ColorExtractor\Color;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;

class WatterMarkService
{
    const RED   = 'red';
    const GREEN = 'green';
    const BLUE  = 'blue';

    public function defineColor(string $image): string
    {
        $palette = Palette::fromFilename($image);
        $color   = (new ColorExtractor($palette))->extract()[0];
        $color   = str_replace('#', '', Color::fromIntToHex($color));
        $split   = str_split($color, 2);
        $r       = hexdec($split[0]);
        $g       = hexdec($split[1]);
        $b       = hexdec($split[2]);
        $arr     = [self::RED => $r, self::GREEN => $g, self::BLUE => $b];
        return array_keys($arr, max($arr))[0];
    }

    public function getWatterMark(string $mainColor): string
    {
        if ($mainColor == self::RED) {
            return public_path('images/test_black.jpg');
        } elseif ($mainColor == self::BLUE) {
            return public_path('images/test_yellow.jpg');
        }

        return public_path('images/test_red.jpg');
    }

    public function resize(string $imagePath, float $percent = 0.2)
    {
        list($width, $height) = getimagesize($imagePath);
        $newWidth   = $width * $percent;
        $newHeight  = $height * $percent;
        $thumb      = imagecreatetruecolor($newWidth, $newHeight);
        $watterMark = imagecreatefromjpeg($imagePath);
        imagecopyresized($thumb, $watterMark, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        return $thumb;
    }

    public function addWatterMark(string $mainColor, string $path): string
    {
        $mainImage  = imagecreatefromjpeg($path);
        $watterMark = $this->resize($this->getWatterMark($mainColor));

        imagecopy($mainImage,
                  $watterMark,
                  imagesx($mainImage) - imagesx($watterMark),
                  imagesy($mainImage) - imagesy($watterMark),
                  0,
                  0,
                  imagesx($watterMark),
                  imagesy($watterMark));

        $fileName = md5(microtime()) . '.jpg';
        imagepng($mainImage, storage_path('/app/public/' . $fileName), 0);
        return env("APP_URL") . "/storage/$fileName";
    }

    public function storeImage(UploadedFile $image): ?string
    {
        $filename = md5(microtime()) . '.png';
        if (Storage::disk('local')->put($filename, $image->getContent())) {
            return storage_path("app/$filename");
        }
        return null;
    }
}
