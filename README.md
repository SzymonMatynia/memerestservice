# memerestservice
This is my meme service
```batch
To install everything needed just:
1. Clone this repository
2. go to the project directory
3. run "composer install"
4. ./bin/console server:start
```


# Defined routes(for now)
```yaml
api_get_memes: [GET]
  path: /api/meme
  controller: App\Controller\MemeApiController::getMemes

api_get_meme: [GET]
  path: /api/meme/{id}
  controller: App\Controller\MemeApiController::getMeme

api_add_meme: [POST]
  path: /api/meme/add
  controller: App\Controller\MemeApiController::addMeme

api_update_meme: [PUT/PATCH]
  path: /api/meme/{id}
  controller: App\Controller\MemeApiController::updateMeme

api_delete_meme: [DELETE]
  path: /api/meme/{id}
  controller: app\controller\MemeApiController::deleteMeme
```
