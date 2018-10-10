/**
 * Created by ken on 2016/10/09.
 */

const config = {
    db_connection: {
        host: process.env.DB_HOST || 'localhost',
        user: process.env.DB_USER || 'root',
        database: process.env.DB_NAME || 'nodeapp',
        connectionLimit: Number(process.env.CONNECTION_LIMIT || 5)
    }
};

module.exports = config;
