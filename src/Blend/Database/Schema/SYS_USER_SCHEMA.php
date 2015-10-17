<?php

namespace Blend\Database\Schema;

class SYS_USER_SCHEMA {

    /**
     * @var string Schema for the sys_user table
     */
    const TABLE_NAME = 'sys_user';

    /**
     * @var integer Column is Nullable. Defaults to nextval('sys_user_user_id_seq'::regclass)
     */
    const USER_ID = 'user_id';

    /**
     * @var character_varying Column is Nullable. Defaults to NULL
     */
    const USERNAME = 'username';

    /**
     * @var character_varying Column is Nullable. Defaults to NULL
     */
    const PASSWORD = 'password';

    /**
     * @var character_varying Column is Nullable. Defaults to NULL
     */
    const USER_EMAIL = 'user_email';

    /**
     * @var timestamp_without_time_zone Column is Nullable. Defaults to now()
     */
    const USER_DATE_CREATED = 'user_date_created';

    /**
     * @var timestamp_without_time_zone Column is Nullable. Defaults to now()
     */
    const USER_DATE_CHANGED = 'user_date_changed';

    /**
     * @var boolean Column is Nullable. Defaults to true
     */
    const USER_IS_ACTIVE = 'user_is_active';

}