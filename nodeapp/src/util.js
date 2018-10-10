/**
 * Created by ken on 2016/10/09.
 */


function wrap_promise(next, pr) {
    pr.catch(next);
}

function fetch_keys(obj, keys) {
    const ret = {};
    for (let k of keys) {
        ret[k] = obj[k];
    }
    return ret;
}

module.exports.wrap_promise = wrap_promise;
module.exports.fetch_keys = fetch_keys;
