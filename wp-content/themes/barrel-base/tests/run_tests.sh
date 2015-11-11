#! /bin/sh 

# Simpler test script for running tests iteratively. 
# You must have wordpress-testing and wordpress installed already.

export WP_TESTS_DIR=/tmp/wordpress-testing
export WP_DIR=/tmp/wordpress
SCRIPTS_DIR=`dirname $0`

rsync --recursive --quiet --ignore-times --exclude=.git --exclude=node_modules $SCRIPTS_DIR/.. $WP_DIR/wp-content/themes/knowledgebase
phpunit -c $WP_DIR/wp-content/themes/knowledgebase/tests/phpunit.xml