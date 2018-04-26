# Curve Technical Challenge

## About me

Alexander Cheprasov
- email: acheprasov84@gmail.com
- phone: 07490 216907
- linkedin: https://uk.linkedin.com/in/alexandercheprasov/
- CV: http://cheprasov.com/cv.pdf
- London, UK

## About Technical Challenge

Description of the task in the file `Curve_Technical_Challenge_Software_Developer.pdf`

### About algorithm of searching find the shortest contribution path.

We have 2 users: `user1` and `user2`
1. It looks like I compile 2 trees with users as root nodes.
2. I start from an user with less count of repos, it helps to find faster second user, because we will check less repos.
3. I added to one of them by turns a new nodes (user) with height +1, that have nearest contributors with 1 hop path. (and skip users who already exists in the tree, and check intersection).
4. Check intersect of the trees for their the last levels. If there is no intersect, got to step 3, else go to step 5.
5. Return count of added levels.

Also, I will remove parents nodes of the trees on step 4, because they I not interested for me anymore, if I get the nodes' children. And, I have a special hash for skipping already checked nodes (users).

#### Result:

- `-1` - Nothing found
- `0` - The same users
- `1` and more  - Count of hops between the users

### What need to do, and what I have not done, because it is just a test.
- We should to use queues (like separate service) for execution requests to GitHub.
- We cant allow users wait for a response long time (for example, ask clients to repeat a request later), otherwise worker pools can be frozen and service is crashed
- I did not want to use MySql, Redis in the test task, therefore I just save raw responses from GitHub to files.
- I did not write for all classes, just covered the main functional of algorithm.
- I left a lot small `todo` in the code.

### How to run

- Requires PHP >= 7.0
- Configure connection settings (`user` and `token`) for GitHub in file `./src/Config/Config.php`, create token you can here: https://github.com/settings/tokens
- Allow to write/read for dir `./cache`
- Check and include to nginx config file `./.conf/nginx/80.conf`
- Add host `tc-curve.lh` to `/etc/hosts`
- Open the link in browser 'tc-curve.lh/github/hops/<user1>/<user2>'

### How to run tests

- Update composer `composer update`;
- Run `./vendor/bin/phpunit`
