<?php

    /*Script which takes a set of small images and merges them to
      one big image. (usefull when converting a set of renders to one big sheet)

      Tomas Kubes
      2005
    */


    /* This file takes a set of images and merges them to one and returns them. */

    header ("Content-type: image/png");

    /* At first get a list of files to prepare correct image. */
    $target_dir = "./join/";
    $dh  = opendir($target_dir);
    while (false !== ($filename = readdir($dh)))
    {
        //store the names
        if (($filename != ".") and ($filename != "..") and (substr_count (strtolower($filename), ".png") > 0))
            $files[] = $filename;
    }
    closedir($dh);
    sort($files);

    //stop when no files were found
    if (count ($files) == 0)
        die ("Read 0 files!");

    //get unber of required rows
    $number_of_rows = ceil(count($files) / 8);

    //open first image to get its dimensions
    $image_info = getimagesize($target_dir . $files[0]);

    //stop when no files were found
    if ($image_info == FALSE)
        die ("Cannot retrieve image info from first file:" . $target_dir . $files[0] . "!");

    //extract the data
    $width  = $image_info[0];
    $height = $image_info[1];

    $target_im = @imagecreatetruecolor(9*$width, $number_of_rows * $height)
        or die("Cannot Initialize new GD image stream " .  9*$width . "x" . $number_of_rows * $height. "px");

    /*Copy a part of src_im onto dst_im starting at the x,y coordinates src_x, src_y
    with a width of src_w and a height of src_h.
    The portion defined will be copied onto the x,y coordinates, dst_x and dst_y. */
    for ($b = 0; $b < $number_of_rows; $b ++)
    {
        $y = $b * $height;

        for ($a = 0; $a < 8; $a++)
        {
            $source_image_path = $target_dir . $files[($b*8) + $a];

            /* Attempt to open */
            $source_im = @imagecreatefrompng($source_image_path);

            /* See if it failed */
            if (!$source_im)
                continue;
                //die ("Cannot open the image" . $source_image_path . ".");

            /*copy the image block to the target image */
            @ imagecopy ($target_im, $source_im, ($a * $width), $y, 0, 0, $width, $height)
                or die("Cannot copy the image" . $source_image_path . "to the target.");

            imagedestroy($source_im);
        }
    }

    /* Now add an empty white square */
    imagefilledrectangle ($target_im, 8*$width, 0, 9*$width, ($number_of_rows * $height) - 1, 0xFFFFFF);

    /* For sure make a flood fiill with  dummy transparency color.
       Include a small transparent rectangle for future processing.*/
    imagefill ($target_im, 0, 0, 0xFFAADD);
    imagefilledrectangle ($target_im, 0, 0, 10, 10, 0xE7FFFF);

    /*output */
    imagepng($target_im);
    imagedestroy($target_im);

?>
