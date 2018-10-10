const express = require('express');
const router = express.Router();

const externalApiEndpoint = process.env.API_ENDPOINT || 'http://localhost:8989/status.txt';
const externalApiResponse = process.env.API_RESPONSE || 'OK';
const rp = require('request-promise');

/* GET apis listing. */
router.get('/', function(req, res, next) {
  res.send('respond with a resource!!');
});

router.all('*', function(req, res, next) {
  if (externalApiEndpoint == "-") {
    return next();
  }
  rp({url: externalApiEndpoint, forever: true})
    .then((body) => {
      if (String(body).trim() == externalApiResponse) {
        next();
      } else {
        res.send(500, `Status is not ${externalApiResponse}, but ${body}`);
      }
    })
    .catch((err) => next(err))
  ;
});

router.get("/status", (req, res) => res.send("OK\n"));

router.use('/users', require('./users'));
router.use('/articles', require('./articles'));


module.exports = router;
