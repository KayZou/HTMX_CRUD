<?php
$id = $_GET["id"];
$title = $_GET["title"];
$content = $_GET["content"];
$image = $_GET["image"];
?>

<form hx-post="./api.php?action=update_post" hx-target="#post-<?php echo $id?>" hx-vals=".form" hx-encoding="multipart/form-data">
    <div class="form">
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="text" class="form-control mb-3" name="title" value="<?php echo $title ?>">
        <textarea class="form-control mb-3" rows="3" name="content"><?php echo $content ?></textarea>
        <input type="file" name="file" class="form-control mb-3">
        <button class="btn btn-primary">Update post</button>
    </div>
</form>
