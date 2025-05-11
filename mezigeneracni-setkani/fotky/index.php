<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <title>Galerie obrázků</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 1rem;
            background-color: #f5f5f5;
        }

        h1 {
            text-align: center;
        }

        .gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }

        .image-card {
            position: relative;
            max-width: 300px;
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .image-card:hover {
            transform: scale(1.02);
        }

        .image-card img {
            width: 100%;
            height: auto;
            cursor: pointer;
            display: block;
        }

        .download-btn {
            display: block;
            text-align: center;
            background-color: #007bff;
            color: white;
            padding: 0.5rem;
            text-decoration: none;
            border-top: 1px solid #eee;
        }

        .download-btn:hover {
            background-color: #0056b3;
        }

        /* Lightbox */
        .lightbox {
            display: none;
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
        }

        .lightbox img {
            max-width: 90%;
            max-height: 90%;
            box-shadow: 0 0 20px #fff;
        }

        .lightbox:target {
            display: flex;
        }
    </style>
</head>

<body>

    <h1>Galerie obrázků</h1>
    <div class="gallery">
        <?php
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $files = scandir('.');

        foreach ($files as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (is_file($file) && in_array($ext, $allowed_ext)) {
                $safeFile = htmlspecialchars($file);
                echo <<<HTML
        <div class="image-card">
            <a href="#img_$safeFile"><img src="$safeFile" alt=""></a>
            <a class="download-btn" href="$safeFile" download>Stáhnout</a>
        </div>

        <div class="lightbox" id="img_$safeFile" onclick="location.href='#'">
            <img src="$safeFile" alt="">
        </div>
HTML;
            }
        }
        ?>
    </div>

</body>

</html>