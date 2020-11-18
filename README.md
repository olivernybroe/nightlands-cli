# Nightlands CLI

A CLI tool for the game Nightlands.


# Setup
Install composer packages and add an app key.

# Usage

## Authentication

### Adding a user (auth:add)
For adding a new user, run the following command
```bash
$ php nightlands auth:add
```
It will ask for the email and password of the user and save that in the database.  
The password is encrypted, not hashed, however everything is stored in a local sqlite database.

### Registering a new user (auth:register)
For registering a new user, run the following command
```bash
$ php nightlands auth:register
```

### Logging in (auth:login)
For logging in with any of the added users, run the following command
```bash
$ php nightlands auth:login
```

This will log in the selected users and save the new authentication token in the database.

### Refreshing authentication token (auth:refresh)
For refreshing the authentication tokens, run the following command
```bash
$ php nightlands auth:refresh
```

This will take all the users which have been logged in and refresh their authentication token.  
This command is also added to a daily scheduler, so the tokens won't expire.

## Scoreboard (scoreboard)
For showing the current scoreboard run the following command
```bash
$ php nightlands scoreboard
```

## Units

### Show all units (units)
For showing all units available, run the following command
```bash
$ php nightlands units
```

## Train a unit (units:train)
For training a new unit on a user, run the following command
```bash
$ php nightlands units:train
```
