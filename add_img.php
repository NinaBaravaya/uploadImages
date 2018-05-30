<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href='css/style.css' rel='stylesheet' type='text/css'>
    <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/script.js"></script>
</head>
<body>
<div class="content">
    <?php if ($images): ?>
        <table class="table" border="1" cellspacing="0" cellpadding="0">
            <thead>
            <tr>
                <th scope="col">id</th>
                <th scope="col">Название</th>
                <th scope="col">Размер (байт)</th>
                <th scope="col">Превью</th>
                <th scope="col">Ссылка</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($images as $key => $value): ?>
                <tr>
                    <td><?php echo $value['img_id'] ?></td>
                    <th scope="row"><?php echo $value['name'] ?></th>
                    <td><?php echo $value['size'] ?></td>
                    <td><img src="img/thumbs/<?php echo $value['img'] ?>"></td>
                    <td><a href="img/normal/<?php echo $value['img'] ?>"><?php echo $value['img'] ?></a></td>
                </tr>

            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
   <br/>
    <br/>
    <?php
    if (isset($_SESSION['add_img']['res'])):?>
        <div class='error'><?php echo $_SESSION['add_img']['res'];?></div>
    <?php endif;?>

    <?php
    if (isset($_SESSION['answer']['error'])):?>
        <div class='error'><?php echo $_SESSION['answer']['error']; unset($_SESSION['answer']['error'])?></div>
    <?php endif;?>

    <?php
    if (isset($_SESSION['answer']['success'])):?>
        <div class='success'><?php echo $_SESSION['answer']['success']; unset($_SESSION['answer']['success'])?></div>
    <?php endif;?>
    <form action="" method="post" enctype="multipart/form-data">
        <table class="add_edit_page" cellspacing="0" cellpadding="0">
            <tr>
                <td>Добавить изображение:</td>
                <td class="add-edit-txt">Название изображения:</td>
            </tr>
            <tr>
                <td id="btnimg">
                    <div><input type="file" name="galleryimg[]"/></div>
                </td>
                <td id="nameimg">
                    <div><input class="head-text" type="text" name="name[]"/></div>
                </td>
            </tr>
            <tr>
                <td>
                    <br/>
                    <input type="button" id="add" value="Добавить поле"/>
                    <input type="button" id="del" value="Удалить поле"/>
                    <p>Максимальное кол-во полей 5 (задается в script.js)</p>
                </td>
            </tr>
        </table>
        <input type="button" id="save_img" value="сохранить"/>
    </form>
    <?php unset($_SESSION['add_img']); ?>
</div>
</body>
</html>
