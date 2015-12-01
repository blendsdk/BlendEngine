<?php

namespace Blend\Service\UserManager\Database\Schema;

class SYS_USER_SCHEMA {

    /**
     * @var string the sys_user schema
     */
    const TABLE_NAME = 'sys_user';

    /**
     * @var uuid Column is Nullable. Defaults to uuid_generate_v4()
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

    /**
     * @var character_varying Column is Nullable. Defaults to 'ROLE_USER'::character varying
     */
    const USER_ROLE = 'user_role';

}
