<?php

//HTMLタグの入力を無効にし、文字コードをutf-8にする
//（PHPのおまじないのようなもの）
function h($v){
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

//変数の準備
$FILE_NEWS = 'news.txt'; //保存ファイル名

$FILE_COMMENTS = 'comments.txt'; //保存ファイル名

$NEWS = []; //全ての投稿の情報を入れる

$COMMENTS = []; //全てのコメントの情報を入れる

//$FILEというファイルが存在しているとき
if(file_exists($FILE_NEWS)) {
    //ファイルを読み込む
    $NEWS = (array)json_decode(file_get_contents($FILE_NEWS));
}
if(file_exists($FILE_COMMENTS)) {
    //ファイルを読み込む
    $COMMENTS = (array)json_decode(file_get_contents($FILE_COMMENTS));
}

//$_SERVERは送信されたサーバーの情報を得る
//REQUEST_METHODはフォームからのリクエストのメソッドがPOSTかGETか判断する
// var_dump($_GET);
// var_dump($_POST);

if($_SERVER['REQUEST_METHOD'] === 'GET'){
    //$_POSTはHTTPリクエストで渡された値を取得する
    //リクエストパラメーターが空でなければ
    $index = $_GET["index"];
}else if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['del'])){
        $index = $_POST["index"];
        //削除ボタンが押された場合
        
        //新しい全体配列を作る
        $NEW_COMMENTS = [];
        
        //削除ボタンが押されるとき、すでに$BOARDは存在している
        $i = 0;
        while($i < count($COMMENTS)){
            //$_POST['del']には各々のidが入っている
            //保存しようとしている$DATA[0]が送信されてきたidと等しくないときだけ配列に入れる
            if($COMMENTS[$i]->id !== $_POST['del']){
                $NEW_COMMENTS[] = $COMMENTS[$i];
            }
            $i++;
        }
        //全体配列をファイルに保存する
        file_put_contents($FILE_COMMENTS, json_encode($NEW_COMMENTS));
    } else if(isset($_POST['comment'])) {
        //投稿ボタンが押された場合
        $index = $_POST["index"];
        $id = uniqid(); //ユニークなIDを自動生成
        //$titleに送信されたタイトルとコンテンツを代入
        $comment = $_POST['comment'];
        //新規データ
        $DATA = ["id" => $id, "comment" => $comment];
        //新規データを全体配列に代入する
        $COMMENTS[] = $DATA;

        // 全体配列をファイルに保存する
        file_put_contents($FILE_COMMENTS, json_encode($COMMENTS));
    }

    // //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
    header('Location: '.$_SERVER['SCRIPT_NAME']."?index=".$index);
    // //プログラム終了
    exit;
}


    //header()で指定したページにリダイレクト
    //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
    // header('Location: '.$_SERVER['SCRIPT_NAME']);
    // //プログラム終了
    // exit;
    // }
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
                <h3>
                    <?php echo $NEWS[$index]->title; ?>
                </h3>
                <div class="list-content">
                    <?php echo $NEWS[$index]->content; ?>
                </div>
            </div>
            <hr>
            <form action="show.php" method= "post">
                <div class="post-it-cover">
                    <div class="news-sentence">
                        <label for="name">コメント</label>
                        <textarea name="comment" ></textarea>
                    </div>
                    <!--削除-->
                    <!--この時その投稿のidがサーバーに送信される-->
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
                <?php $i = 0; ?>
                <?php while($i < count($COMMENTS)): ?>
                    <hr>
                    <form action="show.php" method= "post">
                        <div class="news-sentence comment-sentence">
                            <?php echo $COMMENTS[$i]->comment; ?>
                        </div>
                        <!--削除-->
                        <div class="delete-btn">
                        <!--この時その投稿のidがサーバーに送信される-->
                            <input type= "hidden" name="index" value="<?php echo $index; ?>">
                            <input type= "hidden" name= "del" value= "<?php echo $COMMENTS[$i]->id; ?>">
                            <input type= "submit" value= "コメントを削除する">
                        </div>
                    </form>
                <?php $i++ ?>
                <?php endwhile; ?>
            </div>
        </section>
    </div>
</body>
