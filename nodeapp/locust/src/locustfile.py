# coding: utf8
from random import random, randint, sample

from flask import json
from locust import HttpLocust, TaskSet, task


MIN_WAIT = 1
MAX_WAIT = 1
MAX_USER_ID = 10000


class StaticFileTask(TaskSet):
    @task
    def static_file(self):
        self.client.get("/api/", name="/")


class StaticFileOnly(HttpLocust):
    task_set = StaticFileTask
    min_wait = MIN_WAIT
    max_wait = MAX_WAIT


class StatusTask(TaskSet):
    @task
    def status(self):
        self.client.get("/api/status", name="/status")


class StatusOnly(HttpLocust):
    task_set = StatusTask
    min_wait = MIN_WAIT
    max_wait = MAX_WAIT


class ReadTask(TaskSet):
    @task
    def get_user(self):
        user_id = randint(1, MAX_USER_ID)
        self.client.get("/api/users/%s" % user_id, name='/users/${user_id}')


class ReadOnly(HttpLocust):
    task_set = ReadTask
    min_wait = MIN_WAIT
    max_wait = MAX_WAIT


class WriteTask(TaskSet):
    @task
    def post_user(self):
        data = dict(name='mokemoke')
        self.client.post("/api/users/", data=data, name='/users/${user_id}')


class WriteOnly(HttpLocust):
    task_set = WriteTask
    min_wait = MIN_WAIT
    max_wait = MAX_WAIT


class ScenarioTask(TaskSet):
    BASE_PATH = "/api"
    user_id = None
    articles = None

    def on_start(self):
        self.user_id = None
        self.articles = None

    def create_user(self, name=None):
        name = name or "name-%s" % randint(1, 1000000000)
        data = dict(name=name)
        with self.client.post("%s/users/" % self.BASE_PATH, data=data, name='/users/', catch_response=True) as response:
            res = json.loads(response.content)
            self.user_id = res['id']

    def get_user(self, user_id=None):
        user_id = user_id or self.user_id
        self.client.get("%s/users/%s" % (self.BASE_PATH, user_id), name='/users/[ID]')

    def update_user(self, name, user_id=None):
        user_id = user_id or self.user_id
        data = dict(name=name)
        self.client.patch("%s/users/%s" % (self.BASE_PATH, user_id), data=data, name='/users/[ID]')

    def delete_user(self, user_id=None):
        user_id = user_id or self.user_id
        self.client.delete("%s/users/%s" % (self.BASE_PATH, user_id), name='/users/[ID]')

    def create_article(self, author_id=None, title=None, content=None):
        author_id = author_id or self.user_id
        data = dict(author_id=author_id, title=title, content=content)
        self.client.post("%s/articles/" % (self.BASE_PATH,), data=data, name='/articles/')

    def get_latest_article(self):
        self.client.get("%s/articles/" % (self.BASE_PATH,), name='/articles/')

    def get_latest_articles(self, limit=10):
        params = dict(limit=limit)
        with self.client.get("%s/articles/" % (self.BASE_PATH,), params=params, name='/articles/?limit=X',
                             catch_response=True) as response:
            self.articles = json.loads(response.content)
        return self.articles

    def update_article(self, article_id, author_id=None, title=None, content=None):
        author_id = author_id or self.user_id
        data = dict(author_id=author_id, title=title, content=content)
        self.client.patch("%s/articles/%s" % (self.BASE_PATH, article_id), data=data, name='/articles/[ID]')

    def delete_article(self, article_id):
        self.client.delete("%s/articles/%s" % (self.BASE_PATH, article_id), name='/articles/[ID]')

    def get_likes(self, article_id):
        self.client.get("%s/articles/%s/likes/" % (self.BASE_PATH, article_id), name='/articles/[ID]/likes/')

    def get_like_of_mine(self, article_id, user_id=None):
        user_id = user_id or self.user_id
        with self.client.get("%s/articles/%s/likes/%s" % (self.BASE_PATH, article_id, user_id),
                             name='/articles/[ID]/likes/[ID]', catch_response=True) as response:
            return response.status_code == 200

    def put_like(self, article_id, user_id=None):
        user_id = user_id or self.user_id
        self.client.put("%s/articles/%s/likes/%s" % (self.BASE_PATH, article_id, user_id),
                        name='/articles/[ID]/likes/[ID]')

    def delete_like(self, article_id, user_id=None):
        user_id = user_id or self.user_id
        self.client.delete("%s/articles/%s/likes/%s" % (self.BASE_PATH, article_id, user_id),
                           name='/articles/[ID]/likes/[ID]')

    @staticmethod
    def probability(p):
        return random() <= p

    @task(1)
    def scenario(self):
        self.create_user()
        self.get_user()
        if self.probability(0.01):
            self.update_user(str("name-%s" % random()))
        if self.probability(0.1):
            self.create_article(self.user_id, "title-%s" % self.user_id, "content!" * 1024)
        self.get_latest_article()

        for i in range(10):
            articles = self.get_latest_articles(10)
            article = sample(articles, 1)[0]
            article_id = article['id']
            self.get_likes(article_id)
            liked = self.get_like_of_mine(article_id, self.user_id)
            if not liked:
                self.put_like(article_id, self.user_id)
            if self.probability(0.1):
                self.delete_like(article_id, self.user_id)
            if self.probability(0.01):
                self.update_article(article_id, article['author_id'], "update-title:" + article['title'],
                                    "update-content:" + article['content'])
            if self.probability(0.01):
                self.delete_article(article_id)

        if self.probability(0.001):
            self.delete_user(self.user_id)


class Scenario(HttpLocust):
    task_set = ScenarioTask
    min_wait = MIN_WAIT
    max_wait = MAX_WAIT
