var express = require('express');
var router = express.Router();
const Connection = require('../src/mysql_connection');
const connection = new Connection();
const wrap_promise = require('./../src/util').wrap_promise;
const fetch_keys = require('./../src/util').fetch_keys;
const simpleApi = require('../src/simple_api');


/* GET apis listing. */
router.get('/', function(req, res, next) {
  const limit = Number(req.query.limit) || 100;
  simpleApi.selectApi(connection,
    "SELECT * FROM articles ORDER BY id DESC LIMIT ?", [limit],
    res, next
  );
});

router.post('/', function(req, res, next) {
  const obj = fetch_keys(req.body, ["author_id", "title", "content"]);
  simpleApi.insertApi(connection,
    "INSERT INTO articles(author_id, title, content) VALUES(?, ?, ?)",
    [obj["author_id"], obj["title"], obj["content"]],
    res, next
  );
});

router.get('/:article_id', function(req, res, next) { // 주의 : limit 유무에 따라 처리가 크게 변함.
  const article_id = req.params.article_id;
  if (req.query.limit) {
    const limit = Number(req.query.limit);
    const sql = "SELECT * FROM articles WHERE id <= ? ORDER BY id DESC LIMIT ?";
    simpleApi.selectApi(connection, sql, [article_id, limit], res, next);
  } else {
    const sql = "SELECT * FROM articles WHERE id = ?";
    simpleApi.selectApi(connection, sql, [article_id], res, next, null, true);
  }
});

router.patch('/:article_id', function(req, res, next) {
  const article_id = req.params.article_id;
  let values = [];
  let params = [];
  for (let k of ["author_id", "title", "content"]) {
    if (req.body[k]) {
      values.push(`${k} = ?`);
      params.push(req.body[k]);
    }
  }
  if (params.length > 0) {
    const value = values.join(", ");
    params.push(article_id);
    const sql = `UPDATE articles SET ${value} WHERE id = ?`;
    simpleApi.updateApi(connection, sql, params, res, next);
  } else {
    res.send(400);
  }
});

router.delete('/:article_id', function(req, res, next) {
  const article_id = req.params.article_id;
  const sql = "DELETE FROM articles WHERE id = ?";
  simpleApi.deleteApi(connection, sql, [article_id], res, next);
});


//
// Likes
//

router.get('/:article_id/likes', function(req, res, next) {
  const article_id = req.params.article_id;
  const sql = "SELECT user_id FROM likes WHERE article_id = ?";
  simpleApi.selectApi(connection, sql, [article_id], res, next);
});

router.put('/:article_id/likes/:user_id', function(req, res, next) {
  const article_id = req.params.article_id;
  const user_id = req.params.user_id;
  const sql = "SELECT COUNT(*) as cnt FROM likes WHERE article_id = ? AND user_id = ?";

  const prm = connection.execute(sql, [article_id, user_id]).then(([results, _ ]) => {
    if (results[0]["cnt"] > 0) {
      return res.send(200, "already liked");
    }

    return connection.transaction((conn) =>
      conn.execute("INSERT INTO likes(article_id, user_id) VALUES(?, ?)", [article_id, user_id])
        .then(([info, _]) =>
          conn.execute("UPDATE articles SET like_count = like_count + 1 WHERE id = ?", [article_id])
        )
        .then(([info, _]) =>
          conn.execute("COMMIT")
        )
        .catch(() => {
          conn.execute("ROLLBACK");
          return Promise.reject("rollback");
        })
    )
      .then(() => res.send(201))
      .catch((err) => res.send(500, err))
      ;
  });

  wrap_promise(next, prm);
});

router.get('/:article_id/likes/:user_id', function(req, res, next) {
  const article_id = req.params.article_id;
  const user_id = req.params.user_id;
  const sql = "SELECT * FROM likes WHERE article_id = ? AND user_id = ?";

  simpleApi.selectApi(connection, sql, [article_id, user_id], res, next, null, true);
});

router.delete('/:article_id/likes/:user_id', function(req, res, next) {
  const article_id = req.params.article_id;
  const user_id = req.params.user_id;
  const sql = "SELECT COUNT(*) as cnt FROM likes WHERE article_id = ? AND user_id = ?";

  const prm = connection.execute(sql, [article_id, user_id]).then(([results, _ ]) => {
    if (results[0]["cnt"] == 0) {
      return res.send(404, "target resource not found");
    }

    return connection.transaction((conn) =>
      conn.execute("DELETE FROM likes WHERE article_id = ? AND user_id = ?", [article_id, user_id])
        .then(([info, _]) =>
          conn.execute("UPDATE articles SET like_count = like_count - 1 WHERE id = ?", [article_id])
        )
        .then(([info, _]) =>
          conn.execute("COMMIT")
        )
        .catch(() => {
          conn.execute("ROLLBACK");
          return Promise.reject("rollback");
        })
    )
      .then(() => res.send(200))
      .catch((err) => res.send(500, err))
      ;
  });

  wrap_promise(next, prm);
});

module.exports = router;
