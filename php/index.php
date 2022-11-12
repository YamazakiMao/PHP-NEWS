<?php

function h($v){
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

$FILE = '../data/news.txt';

date_default_timezone_set('Japan');
$date = date('Y/m/d H:i'); //日時（年/月/日/ 時:分）

$NEWS = [];

if(file_exists($FILE)) {
    $NEWS = (array)json_decode(file_get_contents($FILE));
}

// ニュース投稿
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    if(!empty($_POST['title']) && !empty($_POST['content'])){
        $id = uniqid();
        $title = $_POST['title'];
        $content = $_POST['content'];
        if (!empty($_FILES['image']['name'])) {//ファイルが選択されていれば$imageにファイル名を代入
            $image = uniqid(mt_rand(), true);//ファイル名をユニーク化
            $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得
            move_uploaded_file($_FILES['image']['tmp_name'], './images/' . $image);//imagesディレクトリにファイル保存
        } else {
            $image = "noimage.jpg";
        }
        $DATA = ["id" => $id, "date" => $date, "title" => $title, "content" => $content, "image" => $image];
        $NEWS[] = $DATA;

        file_put_contents($FILE, json_encode($NEWS));
    }

    header('Location: '.$_SERVER['SCRIPT_NAME']);
    exit;
}
?>

<!DOCTYPE html>
<html lang= "ja">
<head>
    <link rel="stylesheet" href="../style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, user-scalable=yes">
    <meta http-equiv= "content-type" charset= "utf-8">
    <title>php news</title>
</head>
<body>
    <div class="cover">
        <?php 
        include('header.php');
        ?>
        
        <section class= "main">
            <div class="news-main">
                <h2>最新のニュースをシェアしてください。</h2>
                <!--投稿-->
                <form action="index.php" method="post" class="news-form" enctype="multipart/form-data">
                    <div class="contact-form title">
                        <label for="name">タイトル</label>
                        <input type= "text" name= "title" class="title-width">
                    </div>
                    <div class="contact-form news-sentence">
                        <label for="name" class="news-text">記事</label>
                        <textarea name="content" class="sentence-width"></textarea>
                    </div>
                    <div class="contact-form image-file">
                        <input type="file" name="image" id="file">
                    </div>
                    <div class="contact-form submit-btn">
                        <input type= "submit" value= "投稿">
                    </div>
                </form>
            </div>
            
            <div class="posted">
                <h2>ニュース一覧</h2>
                <?php foreach ($NEWS as $index => $value): ?>
                    <hr>
                    <ul>
                        <!-- ニュース投稿フォーム -->
                        <form action="show.php" method= "get">
                            <li class="list-title">
                                <?php echo $value->title; ?>
                            </li>
                                <li class="list-content">
                                <?php echo mb_strimwidth( $value->content, 0, 200, '…', 'UTF-8' ); ?>
                            </li>
                            <li class="news-date">
                                <?php echo $value->date; ?>
                            </li>
                            <li class="show-news-btn">
                                <input type= "hidden" name= "index" value= "<?php echo $index; ?>">
                                <input type= "submit" value= "記事全文・コメントを見る" class="show-news">
                            </li>
                        </form>
                    </ul>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
    <?php 
        include('footer.php');
    ?>
</body>
