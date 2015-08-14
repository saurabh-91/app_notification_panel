<?php
/**
 * Created by PhpStorm.
 * User: saurabh
 * Date: 14/8/15
 * Time: 1:04 PM
 */
// ################################### redis constants #########################################################
    define("REDIS_SCHEME", "tcp");
    define("REDIS_HOST", "127.0.0.1");
    define("REDIS_PORT", 6379);
    define("REDIS_DB", 0);
    define("REDIS_HASH","notification_hash");
// #############################################################################################################


// ######################################### mongo client constants ####################################################

    define("MONGO_HOST_PORT", "127.0.0.1:27017");
    //define(MONGO_DB, '+test+');

// #####################################################################################################################

    define("PRODUCT_NAME", "product name");  // product name is a field in mongo db collection
    define("DAILY_NOTIFICATION_COUNT", 3);

?>