How to use
============

http://docs.locust.io/en/latest/quickstart.html

```
API_HOST=http://localhost:3000
locust -f src/locustfile.py --host=$API_HOST StaticFileOnly
locust -f src/locustfile.py --host=$API_HOST StatusOnly
locust -f src/locustfile.py --host=$API_HOST ReadOnly
locust -f src/locustfile.py --host=$API_HOST WriteOnly
locust -f src/locustfile.py --host=$API_HOST Scenario
```

