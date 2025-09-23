<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * 이미지 최적화 서비스 클래스
 *
 * 이 클래스는 Japan24 애플리케이션에서 업로드되는 이미지들을 최적화하는 기능을 제공합니다.
 * 방문 인증 사진, 스탬프 사진 등을 효율적으로 처리하여 서버 용량과 대역폭을 절약합니다.
 *
 * 주요 기능:
 * - 이미지 리사이즈 및 압축 (최대 1200x1200, 품질 85%)
 * - 썸네일 자동 생성 (300x300)
 * - 다양한 이미지 포맷 지원 (JPEG, PNG, GIF)
 * - 투명도 유지 (PNG 이미지)
 * - 압축률 계산 및 모니터링
 * - 일괄 처리 지원
 *
 * 사용 예시:
 * - 성 방문 인증 사진 최적화
 * - 스탬프 북 사진 처리
 * - 사용자 프로필 이미지 처리
 *
 * @package App\Services
 * @author Japan24 Development Team
 * @version 1.0.0
 */
class ImageOptimizationService
{
    /**
     * 이미지를 최적화하여 저장합니다.
     *
     * 업로드된 이미지 파일을 최적화하여 서버에 저장합니다.
     * 자동으로 리사이즈, 압축, 썸네일 생성을 수행합니다.
     *
     * @param UploadedFile $file 업로드된 이미지 파일
     * @param string $folder 저장할 폴더 경로
     * @param array $options 최적화 옵션 배열
     *                      - max_width: 최대 너비 (기본: 1200px)
     *                      - max_height: 최대 높이 (기본: 1200px)
     *                      - quality: JPEG 품질 (기본: 85%)
     *                      - create_thumbnail: 썸네일 생성 여부 (기본: true)
     *                      - thumbnail_size: 썸네일 크기 (기본: 300px)
     * @return string 저장된 이미지 파일 경로
     * @throws \InvalidArgumentException 지원하지 않는 이미지 형식인 경우
     * @throws \RuntimeException 이미지 처리 실패 시
     */
    public function optimizeAndStore(UploadedFile $file, string $folder, array $options = []): string
    {
        // 기본 옵션 설정
        $options = array_merge([
            'max_width' => 1200,
            'max_height' => 1200,
            'quality' => 85,
            'create_thumbnail' => true,
            'thumbnail_size' => 300,
        ], $options);

        // 파일 확장자 검증
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower($file->getClientOriginalExtension());

        if (!in_array($fileExtension, $allowedTypes)) {
            throw new \InvalidArgumentException('지원하지 않는 이미지 형식입니다.');
        }

        // 파일명 생성 (중복 방지)
        $filename = uniqid() . '.jpg'; // JPEG로 통일하여 압축 효율 향상
        $thumbnailFilename = 'thumb_' . $filename;

        // 원본 이미지 로드
        $imageInfo = getimagesize($file->getRealPath());
        if (!$imageInfo) {
            throw new \InvalidArgumentException('유효하지 않은 이미지 파일입니다.');
        }

        // 이미지 타입에 따른 리소스 생성
        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($file->getRealPath());
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($file->getRealPath());
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($file->getRealPath());
                break;
            default:
                throw new \InvalidArgumentException('지원하지 않는 이미지 형식입니다.');
        }

        if (!$sourceImage) {
            throw new \RuntimeException('이미지를 처리할 수 없습니다.');
        }

        // 원본 크기
        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);

        // 리사이즈 계산 (비율 유지)
        $ratio = min($options['max_width'] / $originalWidth, $options['max_height'] / $originalHeight);
        $ratio = min($ratio, 1); // 확대하지 않음

        $newWidth = (int)($originalWidth * $ratio);
        $newHeight = (int)($originalHeight * $ratio);

        // 새로운 이미지 생성
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        // PNG 투명도 유지
        if ($imageInfo[2] == IMAGETYPE_PNG) {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagefill($resizedImage, 0, 0, $transparent);
        }

        // 이미지 리샘플링 (고품질 리사이즈)
        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // 임시 파일에 저장
        $tempPath = sys_get_temp_dir() . '/' . $filename;
        imagejpeg($resizedImage, $tempPath, $options['quality']);

        // 스토리지에 저장
        $path = $folder . '/' . $filename;
        Storage::disk('public')->put($path, file_get_contents($tempPath));

        // 썸네일 생성
        if ($options['create_thumbnail']) {
            $this->createThumbnail($sourceImage, $originalWidth, $originalHeight, $folder, $thumbnailFilename, $options);
        }

        // 메모리 정리
        imagedestroy($sourceImage);
        imagedestroy($resizedImage);
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        return $path;
    }

    /**
     * 썸네일을 생성합니다.
     */
    private function createThumbnail($sourceImage, $originalWidth, $originalHeight, $folder, $thumbnailFilename, $options)
    {
        $thumbnailSize = $options['thumbnail_size'];

        // 썸네일 크기 계산 (정사각형)
        $ratio = min($thumbnailSize / $originalWidth, $thumbnailSize / $originalHeight);
        $thumbWidth = (int)($originalWidth * $ratio);
        $thumbHeight = (int)($originalHeight * $ratio);

        // 썸네일 이미지 생성
        $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $originalWidth, $originalHeight);

        // 임시 파일에 저장
        $tempThumbPath = sys_get_temp_dir() . '/' . $thumbnailFilename;
        imagejpeg($thumbnail, $tempThumbPath, $options['quality']);

        // 스토리지에 저장
        $thumbnailPath = $folder . '/thumbnails/' . $thumbnailFilename;
        Storage::disk('public')->put($thumbnailPath, file_get_contents($tempThumbPath));

        // 메모리 정리
        imagedestroy($thumbnail);
        if (file_exists($tempThumbPath)) {
            unlink($tempThumbPath);
        }
    }

    /**
     * 여러 이미지를 최적화하여 저장합니다.
     */
    public function optimizeAndStoreMultiple(array $files, string $folder, array $options = []): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $this->optimizeAndStore($file, $folder, $options);
            }
        }

        return $paths;
    }

    /**
     * 이미지의 썸네일 경로를 반환합니다.
     */
    public function getThumbnailPath(string $imagePath): string
    {
        $pathInfo = pathinfo($imagePath);
        return $pathInfo['dirname'] . '/thumbnails/thumb_' . $pathInfo['basename'];
    }

    /**
     * 이미지가 존재하는지 확인합니다.
     */
    public function imageExists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }

    /**
     * 썸네일이 존재하는지 확인합니다.
     */
    public function thumbnailExists(string $imagePath): bool
    {
        $thumbnailPath = $this->getThumbnailPath($imagePath);
        return Storage::disk('public')->exists($thumbnailPath);
    }

    /**
     * 이미지와 관련 썸네일을 삭제합니다.
     */
    public function deleteImage(string $path): bool
    {
        $deleted = Storage::disk('public')->delete($path);

        // 썸네일도 함께 삭제
        $thumbnailPath = $this->getThumbnailPath($path);
        if ($this->thumbnailExists($path)) {
            Storage::disk('public')->delete($thumbnailPath);
        }

        return $deleted;
    }

    /**
     * 이미지 파일 크기를 반환합니다 (bytes).
     */
    public function getImageSize(string $path): int
    {
        if (!$this->imageExists($path)) {
            return 0;
        }

        return Storage::disk('public')->size($path);
    }

    /**
     * 압축률을 계산합니다.
     */
    public function calculateCompressionRatio(int $originalSize, int $compressedSize): float
    {
        if ($originalSize === 0) {
            return 0;
        }

        return round((($originalSize - $compressedSize) / $originalSize) * 100, 2);
    }
}