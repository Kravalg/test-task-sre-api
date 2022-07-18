# Test task SRE API

## How to deploy a project on a local machine

- create infrastructure/docker/.env from infrastructure/docker/.env.dist and configure infrastructure if you need
```
cp infrastructure/docker/.env.dist infrastructure/docker/.env
```
- add the following line to the file `/etc/hosts`:
```
127.0.0.1 test-task.local
```
- execute bash script to run project's containers
```
./test_task up -d
```
- go inside the php fpm container
```
docker exec -it docker_test-task-php-fpm_1 bash
```
- install all project dependencies
```
composer install
```
- execute migrations to create database structure
```
bin/console d:m:m
```
- create .env.local and configure project
```
DEBRICKED_API_USERNAME=
DEBRICKED_API_PASSWORD=
SLACK_DSN=slack://TOKEN@default?channel=CHANNEL
```
- go to browser and open the link below to see docs
```
http://test-task.service/api/docs
```
- send a request to the endpoint `GET /api/actions` to retrieve available rule actions
- send a request to the endpoint `GET /api/triggers` to retrieve available rule triggers
- send a request to the endpoint `POST /api/files`  to upload files and receive an id for using in the next request
- send a request to the endpoint `POST /api/jobs` to create a job for scanning files with your rules, you can use example below:
```
{
  "files": [
    "/api/files/1",
    "/api/files/2"
  ],
  "rules": [
    {
      "trigger": "Amount of vulnerabilities found during a scan is greater than",
      "triggerValue": "0",
      "action": "Send an email to user",
      "actionValue": "your-email@test.com"
    },
    {
      "trigger": "Upload fails for some reason",
      "action": "Send a message to a Slack channel"
    }
  ],
  "repositoryName": "your-repository-name",
  "commitName": "your-commit-name"
}
```

## How to test emails locally
Go to browser and open the link `http://127.0.0.1:1080/`

## PHP Code sniffer

Automatically executed before every git push

`./vendor/bin/phpcs`

## PSALM

Automatically executed before every git push
 
`./vendor/bin/psalm`

## PHPUnit

Automatically executed before every git push
 
`./vendor/bin/phpunit`

## Contributing
Read about [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/)
Read about [Design a DDD-oriented microservice](https://docs.microsoft.com/en-us/dotnet/architecture/microservices/microservice-ddd-cqrs-patterns/ddd-oriented-microservice)
