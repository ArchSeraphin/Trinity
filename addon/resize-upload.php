<?php
add_filter('wp_handle_upload_prefilter', function($file) {
    $max_width = 1920;
    $max_height = 1920;

    $image_info = getimagesize($file['tmp_name']);
    if ($image_info) {
        list($orig_width, $orig_height) = $image_info;
        $ratio = $orig_width / $orig_height;

        if ($orig_width > $max_width || $orig_height > $max_height) {
            if ($orig_width / $max_width > $orig_height / $max_height) {
                $new_width = $max_width;
                $new_height = intval($max_width / $ratio);
            } else {
                $new_height = $max_height;
                $new_width = intval($max_height * $ratio);
            }

            // Charger image selon type
            switch ($image_info['mime']) {
                case 'image/jpeg':
                    $src = imagecreatefromjpeg($file['tmp_name']);
                    break;
                case 'image/png':
                    $src = imagecreatefrompng($file['tmp_name']);
                    imagepalettetotruecolor($src);
                    imagealphablending($src, true);
                    imagesavealpha($src, true);
                    break;
                case 'image/webp':
                    $src = imagecreatefromwebp($file['tmp_name']);
                    imagepalettetotruecolor($src);
                    imagealphablending($src, true);
                    imagesavealpha($src, true);
                    break;
                default:
                    return $file; // ne rien faire si autre type
            }

            // Redimensionner
            $new_img = imagescale($src, $new_width, $new_height);

            // Sauvegarder selon le type original
            switch ($image_info['mime']) {
                case 'image/jpeg':
                    imagejpeg($new_img, $file['tmp_name'], 85);
                    break;
                case 'image/png':
                    imagepng($new_img, $file['tmp_name'], 8);
                    break;
                case 'image/webp':
                    imagewebp($new_img, $file['tmp_name'], 85);
                    break;
            }

            imagedestroy($src);
            imagedestroy($new_img);
        }
    }

    return $file;
});
