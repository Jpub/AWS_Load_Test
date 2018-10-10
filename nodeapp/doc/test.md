# API test
# ======
API_BASE=http://localhost:3000/api

# user API
# ----------

ID=$(curl -X POST -d 'name=mokemoke' "${API_BASE}/users" | jq ".id")
echo $ID
curl -X GET "${API_BASE}/users"
curl -X GET "${API_BASE}/users/${ID}"
curl -X PATCH -d "name=mokemoke.id_${ID}" "${API_BASE}/users/${ID}"
curl -X GET "${API_BASE}/users/${ID}"
curl -X DELETE "${API_BASE}/users/${ID}"
curl -X GET "${API_BASE}/users/${ID}"
curl -X PATCH -d "name=mokemoke.id_${ID}" "${API_BASE}/users/${ID}"


# article API
# ------------

aid=$(curl -X POST -d 'author_id=2&title=TitleX&content=ContentX' "${API_BASE}/articles" | jq ".id")
echo $aid
curl -X GET "${API_BASE}/articles"
curl -X GET "${API_BASE}/articles?limit=2"

curl -X GET "${API_BASE}/articles/${aid}"
curl -X GET "${API_BASE}/articles/${aid}?limit=3"

curl -X PATCH -d "author_id=3&title=Title${aid}&content=ContentX" "${API_BASE}/articles/${aid}"
curl -X GET "${API_BASE}/articles/${aid}"

curl -X PATCH -d "content=Content-${aid}" "${API_BASE}/articles/${aid}"
curl -X GET "${API_BASE}/articles/${aid}"

curl -X DELETE "${API_BASE}/articles/${aid}"
curl -X GET "${API_BASE}/articles/${aid}"
curl -X PATCH -d "content=Content-${aid}" "${API_BASE}/articles/${aid}"

# like API
# -----------

# aid=4
uid=10
curl -X GET "${API_BASE}/articles/${aid}"
curl -X PUT "${API_BASE}/articles/${aid}/likes/${uid}"
curl -X GET "${API_BASE}/articles/${aid}"

curl -X GET "${API_BASE}/articles/${aid}/likes/"
curl -X GET "${API_BASE}/articles/${aid}/likes/${uid}"
curl -X DELETE "${API_BASE}/articles/${aid}/likes/${uid}"
curl -X GET "${API_BASE}/articles/${aid}/likes/${uid}"
curl -X GET "${API_BASE}/articles/${aid}/likes/"
curl -X GET "${API_BASE}/articles/${aid}"



