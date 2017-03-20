function shortenPost(post,lengthLimit){
    if (post === null){
        return post;
    }
    if (post.length > lengthLimit){
        var spaceToSplice = null;

        for (var j = lengthLimit - 15; j < post.length; j++) {
            if (j > lengthLimit) {
                break;
            }
            else if (post[j] === " " && j){
                spaceToSplice = j;
            }
        }
        if (spaceToSplice === null){
            return post;
        }
        else{
            return post.slice(0,spaceToSplice) + "...";
        }
    }
    else{
        return post;
    }
}
function callApi(type,index,reset){
    globalIndex = globalIndex + index;

    if (type === "articles"){
        $(".articleBtn").css({'background-color': '#bb1313','color':'white'});
        $(".articleBtn").hover(function() {$(this).css("text-decoration","none");});
        $(".videoBtn").css({'background-color': 'white','color':'#bb1313'});
        $(".videoBtn").hover(
            function() {
                $(this).css("text-decoration","underline");
            },
            function() {
                $(this).css("text-decoration", "none");
            }
        );
    }
    else{
        $(".videoBtn").css({'background-color': '#bb1313','color':'white'});
        $(".videoBtn").hover(function() {$(this).css("text-decoration","none");});
        $(".articleBtn").css({'background-color': 'white','color':'#bb1313'});
        $(".articleBtn").hover(
            function() {
                $(this).css("text-decoration","underline");
            },
            function () {
                $(this).css("text-decoration", "none");
            }
        );
    }

    if (globalType !== type || reset === true){
        globalIndex = 0;
        globalType = type;
    }

    //expects "articles" or "videos" for type
    var url ="http://ign-apis.herokuapp.com/" + type + "?startIndex="+ globalIndex +"&count=10";

    $.ajax({
    url: url,
    // The name of the callback parameter, as specified by the YQL service
    jsonp: "callback",
    // Tell jQuery we're expecting JSONP
    dataType: "jsonp",
    // Tell YQL what we want and that we want JSON
    data: {
        format: "json"
    },
    // Work with the response
    success: function(jsonReturn) {
        var myNode = $(".newestPosts");
        myNode.empty();

        if (globalIndex >= 10){
            var previousDiv = $("<div></div>");
            previousDiv.addClass("previousDiv");
            previousDiv.append("<span id='previousText'>PREVIOUS</span>");
            previousDiv.append("<span id='toTopText'>BACK TO TOP</span>");

            $(".newestPosts").append(previousDiv);

            $('#previousText').on('click', function(){
                callApi(type,-10,false);
            });

            $('#toTopText').on('click', function(){
                callApi(type,10,true);
            });
        }

        for (var i = 0; i < jsonReturn.data.length; i++) {
            var postDiv =  $("<div></div>");
            postDiv.addClass("post");
            if (i === jsonReturn.data.length - 1){
                postDiv.attr('id', 'lastPost');
            }

                //create a blankdiv for the overlay.
                var blankDiv = $("<div></div>");
                blankDiv.addClass("blankDiv");

                var listCount = $("<div></div>");
                listCount.addClass("listCount");
                listCount.append(globalIndex + i + 1);

                var postDetails = $("<div></div>");
                postDetails.addClass("postDetails");

                    var postDetailsContent = $("<div></div>");
                    postDetailsContent.addClass("postDetailsContent")

                        var postDetailsContentTop = $("<div></div>");
                        postDetailsContentTop.addClass("postDetailsContentTop");

                        if (type === "articles"){
                            postDetailsContentTop.append(shortenPost(jsonReturn.data[i].metadata.headline,70));
                        }
                        else {
                            postDetailsContentTop.append(shortenPost(jsonReturn.data[i].metadata.name,70));
                        }

                        var postDetailsContentBottom = $("<div></div>");
                        postDetailsContentBottom.addClass("postDetailsContentBottom");

                        if (type === "articles"){
                            postDetailsContentBottom.append(shortenPost(jsonReturn.data[i].metadata.subHeadline,70));
                        }
                        else {
                            postDetailsContentBottom.append(shortenPost(jsonReturn.data[i].metadata.description,70));
                        }

                        postDetailsContent.append(postDetailsContentTop,postDetailsContentBottom);

                    postDetails.append(postDetailsContent);

                    var videoRunTime = $("<div></div>");
                    videoRunTime.addClass("runTime");

                    if(type === "videos"){
                        var time = jsonReturn.data[i].metadata.duration
                        var minutes = Math.floor(time / 60);
                        var seconds = time - minutes * 60;

                        if (seconds.toString().length < 2){
                            seconds = "0" + seconds;
                        }
                        videoRunTime.append(minutes + ":" + seconds);
                    }

                postDiv.append(blankDiv,listCount,videoRunTime,postDetails);

            $(".newestPosts").append(postDiv);

            var rowDivHidden = $("<div></div>");
            rowDivHidden.addClass("postHide");
            rowDivHidden.addClass('hidden');
            rowDivHidden.css({'background-image' : 'url('+jsonReturn.data[i].thumbnails[2].url+')','background-repeat': 'no-repeat'});
                if (type === "articles"){
                    var year = jsonReturn.data[i].metadata.publishDate.slice(0,4);
                    var month = jsonReturn.data[i].metadata.publishDate.slice(5,7);
                    var day = jsonReturn.data[i].metadata.publishDate.slice(8,10);
                    var slug = jsonReturn.data[i].metadata.slug;
                    var url = "http://www.ign.com/articles/"+ year +"/"+ month +"/"+ day +"/"+ slug;
                    var gotoIgnDiv = $("<a href="+url+"><div></div></a>");
                }
                else{
                    var gotoIgnDiv = $("<a href="+jsonReturn.data[i].metadata.url+"><div></div></a>");
                }
                gotoIgnDiv.addClass("ignTag");
                gotoIgnDiv.append("<span id='ignText'>GO TO IGN</span>");

                var articleNameHidden = $("<div></div>")
                articleNameHidden.addClass("articleNameHidden");

                if (type === "articles"){
                    articleNameHidden.append(jsonReturn.data[i].metadata.headline);
                }
                else {
                    articleNameHidden.append(jsonReturn.data[i].metadata.name);
                }

                rowDivHidden.append(gotoIgnDiv, articleNameHidden);

            $(".newestPosts").append(rowDivHidden);
        }
        if (globalIndex < 290) {
            var seeMoreDiv = $("<div></div>");
            seeMoreDiv.addClass("seeMore");
            seeMoreDiv.append("<span id='seeMoreText'>SEE MORE VIDEOS</span>");

            $(".newestPosts").append(seeMoreDiv);

            $('.seeMore').on('click', function(){
                callApi(type,10,false);
            });
        }


        $('.post').on('click', function (){
            $('.newestPosts .post').removeClass('hidden');
            $('.newestPosts .postHide').addClass('hidden');
            $(this).addClass('hidden');
            $(this.nextElementSibling).removeClass('hidden');
        });
        }
    });
}

var globalIndex = 0;

var globalType = null;

callApi("videos",0);
