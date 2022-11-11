<?php
session_start();
mb_internal_encoding("utf8");

if (!isset($_SESSION['id'])) {
    header("Location:login.php");
}

//変数の初期化
$errors = array();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    //POST処理
    //エスケープ処理
    $input["title"] = htmlentities($_POST["title"]??"",ENT_QUOTES);
    $input["comments"] = htmlentities($_POST["comments"]??"",ENT_QUOTES);
    
    //バリデーションチェック
    //タイトルのバリデーション
    $input["title"] = trim($input["title"]??""); //$inputが設定ないときに備えてnull合体演算子で対応
    if(strlen($input["title"]) == 0){ //入力されているか確認
        $errors["title"] = "タイトルを入力して下さい";
    }
    
    //コメントのバリデーション
    $input["comments"] = trim($input["comments"]??""); //$inputが設定ないときに備えてnull合体演算子で対応
    if(strlen($input["comments"]) == 0){ //入力されているか確認
        $errors["comments"] = "コメントを入力して下さい";
    }

    if (empty($errors)) {
        try {
            //DBに接続
            $pdo = new PDO("mysql:dbname=php_jissen;host=localhost;", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //INSERTでDBに情報格納
            $stmt = $pdo->prepare(" INSERT INTO post(user_id,title,comments) VALUES(?,?,?) ");
            $stmt->execute(array($_SESSION["id"], $input["title"], $input["comments"]));
            $pdo = NULL;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }
}

try{
    //DB接続
    $pdo = new PDO("mysql:dbname=php_jissen;host=localhost;","root","");
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); 
    
    //SERECTで投稿を画面表示
    $posts = $pdo->query("SELECT * FROM post INNER JOIN user ON post.user_id = user.id ORDER BY posted_at DESC LIMIT 3");
    $pdo = null;
} catch(PDOException $e){
    $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>掲示板</title>
        <link rel="stylesheet" type="text/css" href="board.css">
    </head>
    <body>
        <div class="logo">
            <img src="4eachblog_logo.jpg">
            <div class="logname">
                <p>ようこそ<?php echo $_SESSION["name"];?>さん</p>
                <div class="logout">
                    <form action="logout.php">
                        <input type="submit" class="button1" value="ログアウト">
                    </form>
                </div>
            </div>
        </div>
            <header>
                
                <ul>
                    <li>トップ</li>
                    <li>プロフィール</li>
                    <li>4eachについて</li>
                    <li>登録フォーム</li>
                    <li>問い合わせ</li>
                    <li>その他</li>
                </ul>
                
            </header>
        <main>
            <div class="left">
                <h1>プログラミングに役立つ掲示板</h1>
                
                <form method="POST" action="">
                    
                    <h3 class="form_title">入力フォーム</h3>
                    
                    <div class="item">
                        <label>タイトル</label>
                        <input type="text" class="text" size="35" name="title" value="<?php echo $_input["title"]??"";?>">
                        <?php if (!empty($errors["title"])):?>
                            <p class="err_message"><?php echo $errors["title"];?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="item">
                        <label>コメント</label>
                        <textarea cols="65" rows="8" name="comments"><?php echo $_input["comments"]??"";?></textarea>
                        <?php if (!empty($errors["comments"])):?>
                            <p class="err_message"><?php echo $errors["comments"];?></p>
                        <?php endif; ?>
                    </div>
                    <div class="item">
                        <input type="submit" class="button2" value="送信する">
                    </div>

                </form>
                
                <?php
                foreach ($posts as $post) {
                    echo"<div class='article'>";
                        echo"<h3 class='title'>".$post['title']."</h3>";
                        echo"<p class='kiji'>".$post['comments']."</p>";
                    
                        echo"<div class='user_daytime'>";
                            echo"<p>"."投稿者:".$post['name']."</p>";
                            $date = date("投稿日時:".'Y年m月d日 H:i', strtotime($post['posted_at']));
                            echo "<P>".$date."</p>";
                        echo"</div>";

                    echo"</div>";
                    
                }
                ?>

            </div>

            <div class="right">
                <h3>人気の記事</h3>
                <ul>
                    <li>PHPのオススメ本</li>
                    <li>PHP Myadminの使い方</li>
                    <li>今人気のエディタ Top5</li>
                    <li>HTMLの基礎</li>
                </ul>
                <h3>オススメリンク</h3>
                <ul>
                    <li>インターノウス株式会社</li>
                    <li>XAMPPのダウンロード</li>
                    <li>Eclipseのダウンロード</li>
                    <li>Bracketsのダウンロード</li>
                </ul>
                <h3>カテゴリ</h3>
                <ul>
                    <li>HTML</li>
                    <li>PHP</li>
                    <li>MySQL</li>
                    <li>JavaScript</li>
                </ul>
            </div>  
        </main>    
    </body>
</html>