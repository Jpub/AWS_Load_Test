/**
 * Created by ken on 2016/10/10.
 */

const wrap_promise = require('./util').wrap_promise;


function selectApi(connection, sql, params, res, next, cb, first_row_only) {
  if (!cb) {
    cb = ([result, _]) => {
      if (result.length > 0) {
        if (first_row_only) {
          res.send(result[0]);
        } else {
          res.send(result);
        }
      } else {
        res.send(204);
      }
    };
  }
  return wrap_promise(next, connection.execute(sql, params).then(cb));
}


function insertApi(connection, sql, params, res, next, cb) {
  if (!cb) {
    cb = ([info, _]) => res.send(201, {id: info.insertId});
  }
  return wrap_promise(next, connection.execute(sql, params).then(cb));
}


function updateApi(connection, sql, params, res, next, cb) {
  if (!cb) {
    cb = ([info, _]) => {
      if (info.changedRows == 0) {
        res.send(404, "target update resource not found");
      } else {
        res.send(200);
      }
    };
  }
  return wrap_promise(next, connection.execute(sql, params).then(cb));
}


function deleteApi(connection, sql, params, res, next, cb) {
  if (!cb) {
    cb = ([info, _]) => {
      if (info.affectedRows == 0) {
        res.send(404, "target deletion resource not found");
      } else {
        res.send(200);
      }
    };
  }
  return wrap_promise(next, connection.execute(sql, params).then(cb));
}


module.exports.selectApi = selectApi;
module.exports.insertApi = insertApi;
module.exports.updateApi = updateApi;
module.exports.deleteApi = deleteApi;
