<?php
// Variables
$pngarray  = [];
$firsttime = false;
$firsttimepic = false;
$imgname = "";
$secondimgname = "";
$output = imagecreate(10,10);
$outputpic;
$widthimg = "";
$heightimg = "";
$heightcoor = 0;
$widthcoor = 0;
$totalwidth = 0;
$height3 = 0;
$recursive = false;
$outputimgname = "sprite.png";
$cssname = "style.css";
static $j = 0;
$k = 0;
$resizeheight = 0;
$resizewidth = 0;
$resize = false;
$padding = 0;
// End Variables

// Checking and avoiding errors
if($argc == 1){
    echo "Vous n'avez pas renseigné d'arguments.\n Souhaitez vous voir le manuel pour vous aider ?\n Y/N\n";
    $help = readline();
    if($help == "y" || $help == "Y"){
        getHelp();
    }
    elseif($help == "n" || $help == "N"){
        return false;
    }
    else{
        echo "Vous n'avez pas rentré d'arguments valable, si vous souhaitez recevoir de l'aide, rappelez la fonction en rajoutant l'argument '-h' ou '--help'.\n";
        return false;
    }
}

else{
    test_arguments();
}

// Function to check option passed as argument
function test_arguments(){
    global $argv, $k;
    global $recursive, $outputimgname, $cssname, $resize, $resizeheight, $resizewidth, $padding;
    // Creating loop to go through argv
    for($i = 1; $i < count($argv); $i++){
        // Checking if it's an option
        if(str_contains($argv[$i], "-")){
            switch($argv[$i]){
                // Checking recursive
                case "-r" :
                case "--recursive" :
                    echo "Récursivité Activée.\n";
                    $recursive = true;
                    break;
                // Checking if png name change
                case str_contains($argv[$i], "-i"):
                case str_contains($argv[$i], "--output-image"):
                    $outputimgname = substr($argv[$i], strpos($argv[$i], "=") + 1);
                    if(!str_contains($outputimgname, "png")){
                        $outputimgname .= ".png";
                    }
                    echo "Le nom de l'image a bien été pris en compte.\n";
                    break;
                // Checking if css name change
                case str_contains($argv[$i], "-s") :
                case str_contains($argv[$i], "--output-style") :
                    $cssname = substr($argv[$i], strpos($argv[$i], "=") + 1);
                    if(!str_contains($cssname, "css")){
                        $cssname .= ".css";
                    }
                    echo "Le nom de votre fichier css a bien été pris en compte.\n";
                    break;
                // Checking if padding
                case str_contains($argv[$i], "-p") :
                case str_contains($argv[$i], "--padding") :
                    $paddint = substr($argv[$i], strpos($argv[$i], "=") + 1);
                    $padding = intval($paddint);
                    echo "Votre valeur de padding a bien été pris en compte.\n";
                break;
                // Checking if resize
                case str_contains($argv[$i], "-o") :
                case str_contains($argv[$i], "--override-size") :
                    $width = substr($argv[$i], strpos($argv[$i], "=") + 1);
                    $height = $width;
                    $resizewidth = intval($width);
                    $resizeheight = intval($height);
                    $resize = true;
                    echo "Les images seront bien redimensionnées selon les arguments donnés.\n";
                    break;
                // Checking if columns
                case str_contains($argv[$i], "-c") :
                case str_contains($argv[$i], "--column_number") :
                    $rownumber = substr($argv[$i], strpos($argv[$i], "=") + 1);
                    $k = intval($rownumber);
                    echo "Votre nombre de colonnes choisis a bien été pris en compte.\n";
                    break;
                // Checking if need help
                case "-h":
                case "--help":
                    getHelp();
                    die;
            }
        }
        // Checking if is dir
        elseif(is_dir($argv[$i])){
            if(count(glob($argv[$i])) === 0){
                echo "Votre dossier est vide, veuillez renseigner un dossier contenant des images.\n";
            }
            else {
                scanDirectory($argv[$i]);
            }
        }

        // If not dir error
        else{
            echo "Vous n'avez pas rentré d'arguments valable, si vous souhaitez recevoir de l'aide, rappelez la fonction en rajoutant l'argument '-h' ou '--help'.\n";
            return false;
        }
        
    }
}

// Scan function
function scanDirectory($pathname){
    global $pngarray, $recursive;
    if($dir = opendir($pathname)) {
        while (false !== ($scan = readdir($dir))){
            if ($scan != "." && $scan != ".."){
                $fullpath=realpath($pathname.DIRECTORY_SEPARATOR.$scan);
                $filetype = pathinfo($scan, PATHINFO_EXTENSION);
                if($filetype == "png"){
                    $pngarray[] = $fullpath;
                }
                elseif(is_dir($fullpath) && $recursive == true){
                    scanDirectory($fullpath);
                }
            }
        }
    }
}

// Checking if pngarray exists
if (!empty($pngarray)){
    addToPngFirst();
} 

// Function that send first value to first_image function
function addToPngFirst(){
    global $pngarray;
    $value = $pngarray[0];
    array_shift($pngarray);
    my_first_image($value);
}

// Function to create first output
function my_first_image($img){
    global $output, $imgname, $resize, $resizeheight, $resizewidth, $padding;

    $paddingcoor = $padding/2;

    // If Resize is True
    if($resize == true){
        // Get Pic Sizes
        $first = getimagesize($img);
        $imgname = pathinfo($img, PATHINFO_FILENAME);

        // Width/Height of First Pic
        $width1 = $first[0];
        $height1 = $first[1];

        // Resize
        $firstpic = imagecreatefrompng($img);
        // $color = imagecolorallocatealpha($firstpic, 0, 0, 0, 127);
        // imagesavealpha($firstpic, true);
        // imagefill($firstpic, 0, 0, $color);
        
        // Creating transparent background
        $output = imagecreatetruecolor($resizewidth+$padding, $resizeheight+$padding);
        $color = imagecolorallocatealpha($output, 0, 0, 0, 127);
        imagecolortransparent($output, $color); 

        // Creating Image
        imagecopyresampled($output, $firstpic, $paddingcoor, $paddingcoor, 0, 0, $resizewidth, $resizeheight, $width1, $height1);
    }
    else{
        // Get Pic Sizes
        $first = getimagesize($img);
        $imgname = pathinfo($img, PATHINFO_FILENAME);

        // Create Img and max Height
        $firstpic = imagecreatefrompng($img);
        // $color = imagecolorallocatealpha($firstpic, 0, 0, 0, 127);
        // imagesavealpha($firstpic, true);
        // imagefill($firstpic, 0, 0, $color);

        // Creating transparent background
        $output = imagecreatetruecolor($first[0]+$padding, $first[1]+$padding);
        $color = imagecolorallocatealpha($output, 0, 0, 0, 127);
        imagecolortransparent($output, $color); 

        // Width/Height of First Pic
        $width1 = $first[0];
        $height1 = $first[1];
        // Creating Image
        imagecopyresampled($output, $firstpic, $paddingcoor, $paddingcoor, 0, 0, $width1, $height1, $width1, $height1);
    }
    // Destroying temp and recalling addToPng
    imagedestroy($firstpic);
    my_generate_css_first();
}

// Function that iterates through the array to create the final output
function addToPng(){
    global $pngarray;

    if(empty($pngarray)){
        create_image();
    }

    else{
        $value = $pngarray[0];
        array_shift($pngarray);
        my_merge_image($value);
    }
}

// Function to merge the rest of the image contained in pngarray
function my_merge_image($img){
    global $output, $outputpic, $firsttimepic, $widthimg, $heightimg, $imgname, $heightcoor, $firsttime, $totalwidth, $height3, $widthcoor, $resize, $resizeheight, $resizewidth, $padding;
    global $j, $k;
    $j++;
    $paddingcoor = $padding/2;


    // Checking if it's the first time we're calling the function
    if ($firsttimepic == true){
        $output = $outputpic;
    }
    else {
        $firsttimepic = true;
    }

    if($resize ==  true){
        // Get Pic Sizes
        $second = getimagesize($img);
        $imgname = pathinfo($img, PATHINFO_FILENAME);

        // // Width/Height of First Pic
        $width1 = imagesx($output);
        $height1 = imagesy($output);

        // Width/Height of Second Pic
        $width2 = $second[0];
        $height2 = $second[1];
        
        // Resize // Create Img and max width
        $firstpic = $output;
        $secondpic = imagecreatefrompng($img);
        // $color = imagecolorallocatealpha($secondpic, 0, 0, 0, 127);
        // imagesavealpha($secondpic, true);
        // imagefill($secondpic, 0, 0, $color);

        // Creating transparent background
        $outputresize = imagecreatetruecolor($resizewidth+$padding, $resizeheight+$padding);
        $color = imagecolorallocatealpha($outputresize, 0, 0, 0, 127);
        imagecolortransparent($outputresize, $color); 

        // Creating Image
        imagecopyresampled($outputresize, $secondpic, 0, 0, 0, 0, $resizewidth, $resizeheight, $width2, $height2);
        $secondpic = $outputresize;

        // Width/Height of Second Pic
        $width2 = $resizewidth;
        $height2 = $resizeheight;
        $widthimg = $width2;
        $heightimg = $height2;
    }
    else {
        // Get Pic Sizes
        $second = getimagesize($img);
        $imgname = pathinfo($img, PATHINFO_FILENAME);
        // // Width/Height of First Pic
        $width1 = imagesx($output);
        $height1 = imagesy($output);

        // Width/Height of Second Pic
        $width2 = $second[0];
        $height2 = $second[1];
        $widthimg = $width2;
        $heightimg = $height2;

        // Create Img and max width

        $firstpic = $output;
        $secondpic = imagecreatefrompng($img);
        // $color = imagecolorallocatealpha($secondpic, 0, 0, 0, 127);
        // imagesavealpha($secondpic, true);
        // imagefill($secondpic, 0, 0, $color);
    }

    if($j == $k){
        $firsttime = true;
    }

    // Conditions
    if($firsttime == false) {
        // Creating coordinates
        $widthcoor = $width1 + $paddingcoor;
        $maxwidth = $width1 + $width2 + $padding;
        $heightcoor = $paddingcoor;
        if($height1 >= $height2 + $padding){
            // Creating Output Image
            $outputpic = imagecreatetruecolor($maxwidth, $height1);
            $color = imagecolorallocatealpha($outputpic, 0, 0, 0, 127);
            imagecolortransparent($outputpic, $color); 
            // Creating Image
            imagecopyresampled($outputpic, $firstpic, 0, 0, 0, 0, $width1, $height1, $width1, $height1);
            imagecopyresampled($outputpic, $secondpic, $widthcoor, $heightcoor, 0, 0, $width2, $height2, $width2, $height2);
        }
        else {
            // Creating Output Image
            $outputpic = imagecreatetruecolor($maxwidth, $height2 + $padding);
            $color = imagecolorallocatealpha($outputpic, 0, 0, 0, 127);
            imagecolortransparent($outputpic, $color);
            // Creating Image
            imagecopyresampled($outputpic, $firstpic, 0, 0, 0, 0, $width1, $height1, $width1, $height1);
            imagecopyresampled($outputpic, $secondpic, $widthcoor, $heightcoor, 0, 0, $width2, $height2, $width2, $height2);
        }
    }

    else{
        // Checking number of columns
        if($j == $k){
            if($width1 + $padding < $totalwidth){
                $maxwidth = $totalwidth + $padding;
            }
            else {
                $maxwidth = $width1;
            }
            $totalwidth = 0;
            $widthcoor = $paddingcoor;
            $heightcoor = 0;
            $heightcoor = $height1 + $paddingcoor;
            $maxheight = $height1 + $height2 + $padding;
            $j = 0;
            $totalwidth += $width2 + $padding;
            $height3 = $height2;
        }
        else{
            if ($height3 >= $height2){
                $maxheight = $height1;
            }
            else{
                $difference = $height3 - $height2;
                $maxheight = $height1 + $difference + $padding;
            }
            $height3 = $height2;
            $maxwidth = $width1;
            $widthcoor = $totalwidth + $paddingcoor;
            $totalwidth += $width2 + $padding;
        }
        // Creating Output Image
        $outputpic = imagecreatetruecolor($maxwidth, $maxheight);
        $color = imagecolorallocatealpha($outputpic, 0, 0, 0, 127);
        imagecolortransparent($outputpic, $color);
        // Creating Image
        imagecopyresampled($outputpic, $firstpic, 0, 0, 0, 0, $width1, $height1, $width1, $height1);
        imagecopyresampled($outputpic, $secondpic, $widthcoor, $heightcoor, 0, 0, $width2, $height2, $width2, $height2);    
    }

    // Outputting Image and Destroying temp
    imagedestroy($firstpic);
    imagedestroy($secondpic);
    my_generate_css();
}

// Final function to create the output image
function create_image(){
    global $outputpic, $output, $outputimgname;

    if(empty($outputpic)){
        imagepng($output, $outputimgname);
    }
    else{
        imagepng($outputpic, $outputimgname);
    }
    echo "Votre sprite et votre CSS ont été créé avec succès !\n";
    end_html();
}

// Creating the base of the css
function my_generate_css_first(){
    global $output, $imgname, $outputimgname, $cssname, $padding;
    $paddingcoor = $padding/2;

    // Width/Height of Output
    $width1 = imagesx($output);
    $height1 = imagesy($output);
    $width1 -= $padding;
    $height1 -= $padding;
    $outputname = pathinfo($outputimgname, PATHINFO_FILENAME);

    // Creation base css
    file_put_contents($cssname, "
    .$outputname{
        background-image: url($outputimgname);
        background-repeat: no-repeat;
        display: block;
        margin: 2%;
        border: black 2px solid;
    }

    .wrapper {
        display: flex;
        flex-wrap: wrap;
    }
    
    .$outputname-$imgname {
        width: {$width1}px;
        height: {$height1}px;
        background-position: -{$paddingcoor}px -{$paddingcoor}px;
    }");
    create_first_html();;
}

// Keep adding values to css
function my_generate_css(){
    global $imgname, $widthimg, $heightimg, $outputimgname, $cssname, $heightcoor, $widthcoor;

    // Output name
    $outputname = pathinfo($outputimgname, PATHINFO_FILENAME);

    // Adding to css    
    file_put_contents($cssname, "
        
    .$outputname-$imgname {
        width: {$widthimg}px;
        height: {$heightimg}px;
        background-position: -{$widthcoor}px -{$heightcoor}px;
    }", FILE_APPEND);

    create_html();
}

// Create Base HTML
function create_first_html(){
    global $imgname, $outputimgname, $cssname;

    // Output name
    $outputname = pathinfo($outputimgname, PATHINFO_FILENAME);

    file_put_contents("index.html", '<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href='.$cssname.'>
    <meta charset="UTF-8">
    <title>'.$outputname.'</title>
</head>
<body>
    <div class="wrapper">
        <i class="'.$outputname. ' ' .$outputname.'-'.$imgname.'"></i>');
    addToPng();
}

// Create HTML
function create_html(){
    global $imgname, $outputimgname;

    // Output name
    $outputname = pathinfo($outputimgname, PATHINFO_FILENAME);

    file_put_contents("index.html", '
        <i class="'.$outputname. ' ' .$outputname.'-'.$imgname.'"></i>', FILE_APPEND);

    addToPng();
}

// Ending HTML
function end_html(){
    file_put_contents("index.html", '
    </div>
</body>
</html>', FILE_APPEND);

    echo "Un fichier index.html a été créé.\n";

    return false;
}

function getHelp(){
    echo "
DESCRIPTION\n
Concatenate all images inside a folder in one sprite and write a style sheet ready to use.
Mandatory arguments to long options are mandatory for short options too.\n\n
-r, --recursive
Look for images into the assets_folder passed as arguement and all of its subdirectories.\n\n
-i, --output-image=IMAGE
Name of the generated image. If blank, the default name is « sprite.png ».\n\n
-s, --output-style=STYLE
Name of the generated stylesheet. If blank, the default name is « style.css ».\n\n
-p, --padding=NUMBER
Add padding between images of NUMBER pixels.\n\n
-o, --override-size=SIZE
Force each images of the sprite to fit a size of SIZExSIZE pixels.\n\n
-c, --columns_number=NUMBER
The maximum number of elements to be generated horizontally.\n
-h, --help 
Display this manual.\n";
}
?>