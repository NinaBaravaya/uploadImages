<?php
/*====изображения - получение массива===*/
function images()
{
    $db = require 'connect.php';
    $stmt = $db->query('SELECT * FROM images ORDER BY date');
    //массив категорий
    //  var_dump($stmt);die;
    $img = [];
    while ($row = $stmt->FETCH(PDO::FETCH_ASSOC)) {
        // var_dump($row);die;
        $img[] = $row;
    }
    return $img;
}
/*====изображения - получение массива===*/

/* ===Добавление изображения/-ий=== */
function add_img(){
    if ($_POST['name'][0]) {
        $name = [];
        foreach ($_POST['name'] as $key => $value) {
            if (empty(trim($value))) {
                $_SESSION['add_img']['res'] = "<div class='error'>У изображения должно быть название</div>";
                return false;
            } else {
                $name[$key] = trim($value);
            }
        }
    } else {
        $_SESSION['add_img']['res'] = "<div class='error'>У изображения должно быть название</div>";
        return false;
    }
    /////////картинка////////
    $galleryfiles = [];
    $types = array("image/gif", "image/png", "image/jpeg", "image/pjpeg", "image/x-png"); // массив допустимых расширений
    for ($i = 0; $i < count($_FILES['galleryimg']['name']); $i++) {
        if (empty($_FILES['galleryimg']['name'][$i])) {
            $_SESSION['add_img']['res'] = "<div class='error'>Загрузите файл изображения</div>";
            return false;
        }
        $error = "";
        if ($_FILES['galleryimg']['name'][$i]) {
            // если есть файл
            $galleryimgExt = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES['galleryimg']['name'][$i])); // расширение картинки
            $galleryimgName = uniqid() . ".{$galleryimgExt}"; // новое имя картинки
            $galleryimgTmpName = $_FILES['galleryimg']['tmp_name'][$i]; // временное имя файла
            $galleryimgSize = $_FILES['galleryimg']['size'][$i]; // вес файла
            $galleryimgType = $_FILES['galleryimg']['type'][$i]; // тип файла
            $galleryimgError = $_FILES['galleryimg']['error'][$i]; // 0 - OK, иначе - ошибка

            if (!in_array($galleryimgType, $types)) {
                $error .= "Допустимые расширения - .gif, .jpg, .png <br />";
                $_SESSION['answer'] .= "<div class='error'>Ошибка при загрузке картинки {$_FILES['galleryimg']['name'][$i]} <br /> {$error}</div>";
                continue;
            }

            if ($galleryimgSize > SIZE) {
                $error .= "Максимальный вес файла - 1 Мб";
                $_SESSION['answer'] .= "<div class='error'>Ошибка при загрузке картинки {$_FILES['galleryimg']['name'][$i]} <br /> {$error}</div>";
                continue;
            }

            if ($galleryimgError) {
                $error .= "Ошибка при загрузке файла. Возможно, файл слишком большой";
                $_SESSION['answer'] .= "<div class='error'>Ошибка при загрузке картинки {$_FILES['galleryimg']['name'][$i]} <br /> {$error}</div>";
                continue;
            }
            // если нет ошибок
            if (empty($error)) {
                if (@move_uploaded_file($galleryimgTmpName, "img/normal/$galleryimgName")) {
                    resize("img/normal/$galleryimgName", "img/normal/$galleryimgName", 500, 500, $galleryimgExt);
                    resize("img/normal/$galleryimgName", "img/thumbs/$galleryimgName", 100, 100, $galleryimgExt);
                    $galleryfiles[$i]['img'] = $galleryimgName;
                    $galleryfiles[$i]['size'] = filesize("img/normal/" . $galleryimgName);
                } else {
                    $_SESSION['answer'] .= "<div class='error'>Не удалось переместить загруженную картинку. Проверьте права на папки в каталоге</div>";
                }
            }
        }
    }
    /////////картинка////////
    if (count($name) !== count($galleryfiles)) {
        $_SESSION['add_img']['res'] = "<div class='error'>Загрузите файл изображения и укажите название</div>";
        return false;
    }
    $arrResult = array_combine($name, $galleryfiles);
    if ($arrResult) {
        $db = require 'connect.php';
        $date = date("Y-m-d  H:i:s");
        $val = '';
        foreach ($arrResult as $k => $value) {
            $val .= "('" . $k . "','" . $value['img'] . "'," . $value['size'] . ",'" . $date . "'),";
        }
        $val = substr($val, 0, -1);
        $query = "INSERT INTO images 
                     (name,img, size, date)
                     VALUES $val";

        $stmt = $db->query($query);
        if ($stmt->rowCount() > 0) {
            $_SESSION['answer'] .= "<div class='success'>Изображения добавлены</div>";
            mail_order($arrResult);
            return true;
        } else {
            $_SESSION['add_img']['res'] = "<div class='error'>Ошибка при добавлении изображений</div>";
            return false;
        }
    }
}
/* ===Добавление изображения/-ий=== */

/* ===Отправка уведомлений на email админа=== */
function mail_order($links)
{
    // тема письма
    $headers = '';
    $subject = "Ссылки на картинки";
    // заголовки
    $headers .= "Content-type: text/plain; charset=utf-8\r\n";
    $headers .= "From: creasept";
    // тело письма
    $mail_body = "Ссылки на картинки \r\n";
    foreach ($links as $k => $value) {
        $link = "<a href='http://creasept.testovoe/img/normal/{$value['img']}'>на изображение</a>";
        $mail_body .= "Название: {$k}, Ссылка: {$link}, Размер: {$value['size']} \r\n";
    }

    // отправка писем
    mail('admin@gmail.com', $subject, $mail_body, $headers);//письмо полетит менеджеру
}
/* ===Отправка уведомлений на email админа=== */


/* ===Ресайз картинок=== */
function resize($target, $dest, $wmax, $hmax, $ext)
{
    /*
    $target - путь к оригинальному файлу
    $dest - путь сохранения обработанного файла
    $wmax - максимальная ширина
    $hmax - максимальная высота
    $ext - расширение файла
    */
    list($w_orig, $h_orig) = getimagesize($target);
    $ratio = $w_orig / $h_orig; // =1 - квадрат, <1 - альбомная, >1 - книжная

    if (($wmax / $hmax) > $ratio) {
        $wmax = $hmax * $ratio;
    } else {
        $hmax = $wmax / $ratio;
    }

    $img = "";
    // imagecreatefromjpeg | imagecreatefromgif | imagecreatefrompng
    switch ($ext) {
        case("gif"):
            $img = imagecreatefromgif($target);
            break;
        case("png"):
            $img = imagecreatefrompng($target);
            break;
        default:
            $img = imagecreatefromjpeg($target);
    }
    $newImg = imagecreatetruecolor($wmax, $hmax); // создаем оболочку для новой картинки

    if ($ext == "png") {
        imagesavealpha($newImg, true); // сохранение альфа канала
        $transPng = imagecolorallocatealpha($newImg, 0, 0, 0, 127); // добавляем прозрачность
        imagefill($newImg, 0, 0, $transPng); // заливка
    }

    imagecopyresampled($newImg, $img, 0, 0, 0, 0, $wmax, $hmax, $w_orig, $h_orig); // копируем и ресайзим изображение
    switch ($ext) {
        case("gif"):
            imagegif($newImg, $dest);
            break;
        case("png"):
            imagepng($newImg, $dest);
            break;
        default:
            imagejpeg($newImg, $dest);
    }
    imagedestroy($newImg);
}
/* ===Ресайз картинок=== */