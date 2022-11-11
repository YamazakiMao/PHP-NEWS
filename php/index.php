<?php

//HTMLタグの入力を無効にし、文字コードをutf-8にする
//（PHPのおまじないのようなもの）
function h($v){
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

//変数の準備
$FILE = 'news.txt'; //保存ファイル名

$BOARD = []; //全ての投稿の情報を入れる

//$FILEというファイルが存在しているとき
if(file_exists($FILE)) {
    //ファイルを読み込む
    $BOARD = (array)json_decode(file_get_contents($FILE));
}

//$_SERVERは送信されたサーバーの情報を得る
//REQUEST_METHODはフォームからのリクエストのメソッドがPOSTかGETか判断する
// var_dump($_GET);
// var_dump($_POST);
// var_dump($_REQUEST);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    //$_POSTはHTTPリクエストで渡された値を取得する
    //リクエストパラメーターが空でなければ
    if(!empty($_POST['title']) && !empty($_POST['content'])){
        //投稿ボタンが押された場合
        $id = uniqid(); //ユニークなIDを自動生成
        //$titleに送信されたタイトルとコンテンツを代入
        $title = $_POST['title'];
        $content = $_POST['content'];
        //新規データ
        $DATA = ["id" => $id, "title" => $title, "content" => $content];
        //新規データを全体配列に代入する
        $BOARD[] = $DATA;

        //全体配列をファイルに保存する
        file_put_contents($FILE, json_encode($BOARD));
        
    }
    //header()で指定したページにリダイレクト
    //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
    header('Location: '.$_SERVER['SCRIPT_NAME']);
    //プログラム終了
    exit;
}
?>

<!DOCTYPE html>
<html lang= "ja">
<head>
    <link rel="stylesheet" href="../style.css">
    <meta name= "viewport" content= "width=device-width, initial-scale= 1.0">
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
                <form action="index.php" method="post" class="news-form">
                    <div class="contact-form title">
                        <label for="name">タイトル：</label>
                        <input type= "text" name= "title">
                    </div>
                    <div class="contact-form news-sentence">
                        <label for="name">記事：</label>
                        <textarea rows="10" cols="50" name="content"></textarea>
                    </div>
                </form>
                <div class="contact-form submit-btn">
                        <input type= "submit" value= "投稿">
                </div>
            </div>
            

            
            <div>
            <!--tableの中でtr部分をループ-->
            <?php $i = 0; ?>
            <?php while($i < count($BOARD)): ?>
                <hr>
                <ul>
                    <form action="show.php" method= "get">
                        <li class="list-title">
                            <!--テキスト-->
                            <?php echo $BOARD[$i]->title; ?>
                        </li>
                        <li class="list-content">
                            <!--日時-->
                            <?php echo $BOARD[$i]->content; ?>
                        </li>
                        <li>
                            <!--削除-->
                            <!--この時その投稿のidがサーバーに送信される-->
                            <input type= "hidden" name= "index" value= "<?php echo $i; ?>">
                            <input type= "submit" value= "記事全文・コメントを見る">
                        </li>
                    </form>
                </ul>
                <?php $i++ ?>
            <?php endwhile; ?>
            </div>
        </section>
    </div>
</body>
