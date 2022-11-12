<?php

function h($v){
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

$FILE_NEWS = '../data/news.txt';
$FILE_COMMENTS = '../data/comments.txt';

$NEWS = [];
$COMMENTS = [];

$index = $_REQUEST["index"];
if(file_exists($FILE_NEWS)) {
    $NEWS = json_decode(file_get_contents($FILE_NEWS));
}
if(file_exists($FILE_COMMENTS)) {
    $ALL_COMMENTS = json_decode(file_get_contents($FILE_COMMENTS));
    if (array_key_exists($index, $ALL_COMMENTS)) {
        $COMMENTS = (array)$ALL_COMMENTS[$index];
    }
}

// 一覧表示
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // コメント削除
    if(isset($_POST['del'])){
        $NEW_COMMENTS = [];

        // $i = 0;
        // while($i < count($COMMENTS)){
        foreach ($COMMENTS as $value) {
            if($value->id !== $_POST['del']){
                $NEW_COMMENTS[] = $value;
            }
        }
        $ALL_COMMENTS[$index] = $NEW_COMMENTS;
        
        file_put_contents($FILE_COMMENTS, json_encode($ALL_COMMENTS));

    // コメント投稿
    } else if(isset($_POST['comment'])) {
        $id = uniqid();
        $comment = $_POST['comment'];
        $DATA = ["id" => $id, "comment" => $comment];
        $ALL_COMMENTS[$index][] = $DATA;
        file_put_contents($FILE_COMMENTS, json_encode($ALL_COMMENTS));
    }

    // //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
    header('Location: '.$_SERVER['SCRIPT_NAME']."?index=".$index);
    exit;
}
?>

<!DOCTYPE html>
<html lang= "ja">
<head>
    <link rel="stylesheet" href="../style.css">
    <meta name= "viewport" content= "width=device-width, initial-scale= 1.0">
    <meta http-equiv= "content-type" charset= "utf-8">
    <title>Laravel News</title>
</head>
<body>
    <div class="cover">
        <?php 
        include('header.php');
        ?>
        
        <section class= "main">
            <div class="show-contents">
                <img src="images/<?php echo $NEWS[$index]->image; ?>" width="100%" class="posted-image">
                <h3>
                    <?php echo $NEWS[$index]->title; ?>
                </h3>
                <div class="list-content">
                    <?php echo $NEWS[$index]->content; ?>
                </div>
                <div class="list-content news-date">
                    <?php echo $NEWS[$index]->date; ?>
                </div>
            </div>
            <hr>
            <form action="show.php" method= "post">
                <div class="post-it-cover">
                    <div class="news-sentence">
                        <label for="name">コメント</label>
                        <textarea name="comment" ></textarea>
                    </div>
                    <!--　コメント投稿　-->
                    <div class="submit-btn">
                        <input type= "hidden" name="index" value="<?php echo $index; ?>">
                        <input type= "submit" value= "コメントを書く" class="comment-write">
                    </div>
                </div>    
            </form>

            <!-- <hr style="margin-top: 30px;"> -->
            <h3>コメント一覧</h3>
            <p>コメント数: <?php echo count($COMMENTS); ?></p>
            <div class="comment-wrote">
                <?php foreach ($COMMENTS as $value): ?>
                    <hr>
                    <!-- コメント削除 -->
                    <form action="show.php" method= "post">
                        <div class="news-sentence comment-sentence">
                            <?php echo $value->comment; ?>
                        </div>
                        <div class="show-news-btn">
                            <input type= "hidden" name="index" value="<?php echo $index; ?>">
                            <input type= "hidden" name= "del" value= "<?php echo $value->id; ?>">
                            <input type= "submit" value= "コメントを削除する" class="delete-btn">
                        </div>
                    </form>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</body>
