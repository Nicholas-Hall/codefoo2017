<?php
//backend.php will go through the API and pull every article.If a article is in the database with a matching Title and publish Date
//It will not add it.
//It will post what it adds.
//insert your database info here
$servername= "127.0.0.1";
$username = "homestead";
$password = "secret";
$dbname = "codefoo2017";
$conn = mysqli_connect($servername, $username, $password, $dbname);

function callApiForData($type,$conn){
    $index = 0;
    while ($index < 320) {
        $url = "http://ign-apis.herokuapp.com/{$type}?startIndex={$index}&count=20";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result, true);
        if ($type == "articles"){
            saveArticleToDatabase($obj,$conn);
        }
        else{
            saveVideoToDatabase($obj,$conn);
        }
        $index = $index + 20;
    }
}

function saveArticleToDatabase($obj,$conn){
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    for ($i=0; $i < $obj['count'] ; $i++) {
        // get Metadata for posts Table
        $headline = addslashes($obj['data'][$i]['metadata']['headline']);
        $subHeadline = addslashes($obj['data'][$i]['metadata']['subHeadline']);
        $slug = addslashes($obj['data'][$i]['metadata']['slug']);
        $publishDate = $obj['data'][$i]['metadata']['publishDate'];
        $state = $obj['data'][$i]['metadata']['state'];

        //Check to see if Article is already in the database
        $sqltest = "SELECT * FROM articles WHERE headline = '$headline' and publishDate = '$publishDate'";
        $result = mysqli_query($conn,$sqltest);

        if ($result-> num_rows == 0){

            //Make a entry into posts table to create a primary key.
            $type = "article";
            $sqlposts = "INSERT INTO posts (post_id, type)
                         VALUES ( Null, 'article');";

            if (mysqli_query($conn, $sqlposts)) {
                echo "New post created successfully<br>";
            } else {
                echo "Error: " . $sqlposts . "<br>" . $conn->error;die();
            }

            //push metadata to articles table with foreign key (post_id from posts)
            $sql = "INSERT INTO articles (post_id, headline, subHeadline, slug, publishDate, state)
                    VALUES ( LAST_INSERT_ID(), '$headline', '$subHeadline', '$slug', '$publishDate','$state');";

            if (mysqli_query($conn, $sql)) {
                echo "New article {$headline} created successfully<br>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            // grab thumbnail data and insert into database with foreign key (post_id from posts)
            for ($j=0; $j < count($obj['data'][$i]['thumbnails']); $j++) {

                $url = $obj['data'][$i]['thumbnails'][$j]['url'];
                $size = $obj['data'][$i]['thumbnails'][$j]['size'];
                $width = $obj['data'][$i]['thumbnails'][$j]['width'];
                $height = $obj['data'][$i]['thumbnails'][$j]['height'];

                $sqlThumbnails = "INSERT INTO thumbnails (post_id, url, size, width, height)
                                  VALUES ( LAST_INSERT_ID(), '$url', '$size', '$width', '$height');";
               if (mysqli_query($conn, $sqlThumbnails)) {
                  echo "thumbnails {$headline} {$j} added<br>";
               } else {
                  echo "Error: " . $sql . "<br>" . $conn->error;
               }
            }
        }
        else {
            continue;
        }

    }
}

function saveVideoToDatabase($obj,$conn){
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    for ($i=0; $i < $obj['count'] ; $i++) {
        // get Metadata for posts Table
        $name = addslashes($obj['data'][$i]['metadata']['name']);
        $description = addslashes($obj['data'][$i]['metadata']['description']);
        $duration = $obj['data'][$i]['metadata']['duration'];
        $url = $obj['data'][$i]['metadata']['url'];
        $slug = addslashes($obj['data'][$i]['metadata']['slug']);
        $publishDate = $obj['data'][$i]['metadata']['publishDate'];
        $state = $obj['data'][$i]['metadata']['state'];

        //Check to see if Article is already in the database
        $sqltest = "SELECT * FROM videos WHERE name = '$name' and publishDate = '$publishDate'";
        $result = mysqli_query($conn,$sqltest);

        if ($result-> num_rows == 0){

            //Make a entry into posts table to create a primary key.
            $type = "video";
            $sqlposts = "INSERT INTO posts (post_id, type)
                         VALUES ( Null, 'video');";

            if (mysqli_query($conn, $sqlposts)) {
                echo "New post created successfully<br>";
            } else {
                echo "Error: " . $sqlposts . "<br>" . $conn->error;
            }

            //push metadata to articles table with foreign key (post_id from posts)
            $sql = "INSERT INTO videos (post_id, name, description, duration, url, slug, publishDate, state)
                    VALUES ( LAST_INSERT_ID(), '$name', '$description', '$duration', '$url', '$slug','$publishDate','$state');";

            if (mysqli_query($conn, $sql)) {
                echo "New article {$name} created successfully<br>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            // grab thumbnail data and insert into database with foreign key (post_id from posts)
            for ($j=0; $j < count($obj['data'][$i]['thumbnails']); $j++) {

                $url = $obj['data'][$i]['thumbnails'][$j]['url'];
                $size = $obj['data'][$i]['thumbnails'][$j]['size'];
                $width = $obj['data'][$i]['thumbnails'][$j]['width'];
                $height = $obj['data'][$i]['thumbnails'][$j]['height'];

                $sqlThumbnails = "INSERT INTO thumbnails (post_id, url, size, width, height)
                                  VALUES ( LAST_INSERT_ID(), '$url', '$size', '$width', '$height');";
               if (mysqli_query($conn, $sqlThumbnails)) {
                  echo "thumbnails {$name} {$j} added<br>";
               } else {
                  echo "Error: " . $sql . "<br>" . $conn->error;
               }
            }
        }
        else {
            continue;
        }

    }
}

callApiForData("videos",$conn);
callApiForData("articles",$conn);

echo "<H1> Finished Updating </h1>"
?>
