<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掲示板</title>
</head>
<body> 
    <?php


        // DB接続設定
        $dsn = 'mysql:dbname=データベース名;host=localhost';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        // データベース内にテーブルを作成
        $sql = "CREATE TABLE IF NOT EXISTS bbs"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date DATETIME,"
        . "password char(32)"
        .");";
        $stmt = $pdo->query($sql);

        

        // 新規投稿または編集（DB更新処理）
        if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"])){
            // 入力内容保存
            $id = NULL;
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $password = $_POST["password"];

            // 編集実行
            if(!empty($_POST["editNum"])) {
                
                $id = $_POST["editNum"];
                
                $sql = 'UPDATE bbs SET name=:name,comment=:comment,password=:password,date=NOW() WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->execute();
                    
                    
            // もし新規投稿だったらDB設定 
            } else {
            
                // //DB設定 
                $sql = $pdo -> prepare("INSERT INTO bbs (name, comment, date, password) VALUES (:name, :comment, NOW(), :password)");
                // プレースホルダーに値を設定
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                $sql -> execute();   
            }
    
        // 削除（DB更新）
        } elseif(!empty($_POST["delete"]) && !empty($_POST['delPass'])) {
            
            $id = $_POST["delete"];
            $delPass = $_POST["delPass"];

            $sql = 'SELECT * FROM bbs';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
                    
            foreach ($results as $row){
                if($row['id'] == $id && $row['password'] == $delPass){
                    $sql = 'delete from bbs where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }

        // 編集フォームが入力されたら、「名前」と「コメント」に表示
        // htmlのvalue属性と結びつける
        } elseif(!empty($_POST["edit"]) && !empty($_POST["editPass"])) {
           
            $edit = $_POST["edit"];
            $editPass = $_POST["editPass"]; 

            $sql = 'SELECT * FROM bbs';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
                    
            foreach ($results as $row){
                if($row['id'] == $edit && $row['password'] == $editPass){
                $editName = $row['name'];
                $editComment = $row['comment'];
                $password = $row['password'];
                $editNum = $row['id'];     
                }
            }
        }
    ?>
    <!--ブラウザ表示画面-->
    <form method = "post">
        <div class="flex" style="display: flex; justify-content: space-evenly;">
            <div>
                <input type="name" name="name" placeholder="名前" value="<?php if(isset($editName)) {
                echo $editName; }?>">
                <br>
                <input type="text" name="comment" placeholder="コメント" value="<?php if(isset($editComment)) {
                echo $editComment; }?>">
                <br>
                <input type="password" name="password" placeholder="パスワード">
                <input type="hidden" name="editNum" value="<?php if(isset($edit)) {
                echo $edit; }?>">
                <br>
                <button>送信</button>  
            </div>
            <div>
                <input type="text" name="delete" placeholder="削除番号(半角)" value="">
                <br>
                <input type="password" name="delPass" placeholder="パスワード">
                <br>
                <button>削除</button>
            </div>
            <div>
                <input type="text" name="edit" placeholder="編集番号(半角)">
                <br>
                <input type="password" name="editPass" placeholder="パスワード">
                <br>
                <button>編集</button>
            </div>
        </div>
    </form>
    <?php
               //表示機能
        $sql = 'SELECT * FROM bbs';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        
        foreach ($results as $row){
            //配列の中で使うのはテーブルのカラム名の物
            echo $row['id'].' ';
            echo $row['name'].' ';
            echo $row['comment'].' ';
            echo $row['date'].' ';
            echo "<hr>";
        }
    ?>
</body>
</html>
