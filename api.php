<?php

try {
    $db = new PDO("mysql:host=localhost;dbname=htmx", "ziko", "ziko");
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

function render_html($id, $title, $content, $image){
    echo "
        <div class=\"col\" id=\"post-${id}\">
            <div class=\"card mt-5\" style=\"width: 18rem\">
                <div class=\"card-body\">
                    <h5 class=\"card-title\"> {$title} </h5>
                    <h6 class=\"card-subtitle mb-2 text-body-secondary\">
                        {$content}
                    </h6>
                    <img src='{$image}' alt='{$title}'>
                    <a href=\"#\" class=\"btn btn-danger\" hx-delete='./api.php?action=delete_post&id={$id}'>Delete</a>
                    <a href=\"#\" class=\"btn btn-info\" hx-get='./edit.php?id={$id}&title={$title}&content={$content}&image={$image}' hx-target='#post-{$id}'>
                    Edit</a>
                </div>
            </div>
        </div>
    ";
}

function uploadImage(){
    $uploadDir = "uploads/";
    $imageSrc = $uploadDir . basename($_FILES["file"]["name"]);
    move_uploaded_file($_FILES["file"]["tmp_name"], $imageSrc);
    return $imageSrc;
}

switch ($_GET["action"]) {
    case "create_post":
        $imageSrc = uploadImage();
        $sql = $db->prepare("INSERT INTO posts (title, content, image) VALUES (:title, :content, :image)");
        $sql->execute([
            ":title" => $_POST["title"],
            ":content" => $_POST["content"],
            ":image" => $imageSrc,
        ]);
        render_html($db->lastInsertId(), $_POST["title"], $_POST["content"], $imageSrc);
        break;
    case "get_posts":
        $sql = $db->query("SELECT * FROM posts");
        $posts = $sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($posts as $post) {
            render_html($post["id"], $post["title"], $post["content"], $post['image']);
        }
        break;
    case "update_post":
        header("HX-Trigger:update_post");
        $imageSrc = uploadImage();
        $id = $_POST["id"];
        $title = $_POST["title"];
        $content = $_POST["content"];
        $imageUpdate = ($imageSrc !== null) ? "image=:image" : "";
        $sql = $db->prepare("UPDATE posts SET title=:title, content=:content, {$imageUpdate} WHERE id = :id LIMIT 1");
        $params = [
            "id" => $id,
            "title" => $title,
            "content" => $content,
        ];
        if ($imageSrc !== null) {
            $params["image"] = $imageSrc;
        }
        $sql->execute($params);
        break;
    case "delete_post":
        header("HX-Trigger:delete_post");
        $id = $_GET["id"];
        echo $id;
        $sql = $db->prepare("DELETE FROM posts WHERE id = :id");
        $sql->execute([
            ":id" => $id
        ]);
        break;
    default:
        echo "la yomkin";
}
