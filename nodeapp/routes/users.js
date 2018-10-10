const express = require('express');
const router = express.Router();
const Connection = require('../src/mysql_connection');
const connection = new Connection();
const wrap_promise = require('./../src/util').wrap_promise;
const simpleApi = require('../src/simple_api');


router.get('/', function(req, res, next) {
  simpleApi.selectApi(connection, 'SELECT * FROM users', [], res, next);
});


router.post('/', function(req, res, next) {
  const name = req.body.name;
  if (!name) {
    return res.send(400, "name is required");
  }

  simpleApi.insertApi(connection, "INSERT INTO users(name) VALUES(?)", [name], res, next);
});


router.get('/:user_id', function(req, res, next) {
  const user_id = req.params.user_id;

  simpleApi.selectApi(connection, "SELECT * FROM users WHERE id = ?", [user_id], res, next, null, true);
});


router.patch('/:user_id', function(req, res, next) {
  const user_id = req.params.user_id;
  const name = req.body.name;
  if (!name) {
    return res.send(400, "name is required");
  }

  simpleApi.updateApi(connection, "UPDATE users SET name = ? WHERE id = ?", [name, user_id], res, next);
});


router.delete('/:user_id', function(req, res, next) {
  const user_id= req.params.user_id;
  simpleApi.deleteApi(connection, "DELETE FROM users WHERE id = ?", [user_id], res, next);
});

module.exports = router;
