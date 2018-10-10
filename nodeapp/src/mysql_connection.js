/**
 * Created by ken on 2016/10/09.
 */


// get the client
const mysql = require('mysql2/promise');
const config = require('./config');
let pool = null;

class Connection {
    constructor() {
        if (!pool) {
            pool = mysql.createPool(config.db_connection);
        }
        this.pool = pool;
    }

    execute(sql, params) {
        return this.pool.query(sql, params);
    }

    transaction(prm) {
        return this.pool.getConnection()
          .then((conn) =>
            conn.query("START TRANSACTION", [])
              .then(() => prm(conn))
              .then((result) => {
                  conn.release();
                  return result;
              })
              .catch((err) => {
                  conn.release();
                  return Promise.reject(err);
              })
          )
          ;
    }
}

module.exports = Connection;
