  Phalcon micro로 작성
    https://docs.phalconphp.com/ja/latest/reference/tutorial-rest.html

    export BASE_URL=http://virtualbox1
    export BASE_URL=http://localhost
    export BASE_URL=http://52.198.47.153
    export BASE_URL=http://taru8test-1284199666.ap-northeast-1.elb.amazonaws.com

    curl -i -X GET ${BASE_URL}/helloworld

    curl -i -X POST -d '{"name":"user1"}' ${BASE_URL}/api/users
    curl -i -X POST -d '{"name":"user2"}' ${BASE_URL}/api/users
    curl -i -X POST -d '{"name":"user3"}' ${BASE_URL}/api/users

    curl -i -X POST -d '{"author_id":1 ,"title":"article_title1", "content":"content1"}' ${BASE_URL}/api/articles
    curl -i -X POST -d '{"author_id":1 ,"title":"article_title2", "content":"content2"}' ${BASE_URL}/api/articles
    curl -i -X POST -d '{"author_id":1 ,"title":"article_title3", "content":"content3"}' ${BASE_URL}/api/articles
    curl -i -X POST -d '{"author_id":1 ,"title":"article_title4", "content":"content4"}' ${BASE_URL}/api/articles
    curl -i -X POST -d '{"author_id":1 ,"title":"article_title5", "content":"content5"}' ${BASE_URL}/api/articles
    curl -i -X POST -d '{"author_id":1 ,"title":"article_title6", "content":"content6"}' ${BASE_URL}/api/articles
    curl -i -X POST -d '{"author_id":2 ,"title":"article_title1", "content":"content1"}' ${BASE_URL}/api/articles
    curl -i -X POST -d '{"author_id":2 ,"title":"article_title2", "content":"content2"}' ${BASE_URL}/api/articles
    curl -i -X POST -d '{"author_id":2 ,"title":"article_title3", "content":"content3"}' ${BASE_URL}/api/articles
    curl -i -X POST -d '{"author_id":2 ,"title":"article_title4", "content":"content4"}' ${BASE_URL}/api/articles
    curl -i -X POST -d '{"author_id":2 ,"title":"article_title5", "content":"content5"}' ${BASE_URL}/api/articles
    curl -i -X POST -d '{"author_id":2 ,"title":"article_title6", "content":"content6"}' ${BASE_URL}/api/articles

    //curl -i -X GET "${BASE_URL}/articles/5?limit=1"

    curl -i -X PUT -d '{"title":"article_title1-2", "content":"content1-2"}' ${BASE_URL}/api/articles/1

    curl -i -X GET "${BASE_URL}/articles/?limit=100"

    curl -i -X PUT ${BASE_URL}/api/articles/1/likes/1
    curl -i -X PUT ${BASE_URL}/api/articles/2/likes/1
    curl -i -X PUT ${BASE_URL}/api/articles/3/likes/1
    curl -i -X PUT ${BASE_URL}/api/articles/4/likes/1
    curl -i -X PUT ${BASE_URL}/api/articles/5/likes/1

    curl -i -X PUT ${BASE_URL}/api/articles/1/likes/2
    curl -i -X PUT ${BASE_URL}/api/articles/2/likes/2
    curl -i -X PUT ${BASE_URL}/api/articles/3/likes/2
    curl -i -X PUT ${BASE_URL}/api/articles/4/likes/2
    curl -i -X PUT ${BASE_URL}/api/articles/5/likes/2

    curl -i -X DELETE ${BASE_URL}/api/users/2

    curl -i -X DELETE ${BASE_URL}/api/articles/1/likes/1
