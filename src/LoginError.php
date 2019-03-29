<?php

namespace rAthena\FluxCp;

class LoginError extends Error
{
    const UNEXPECTED = 0;
    const INVALID_SERVER = 1;
    const INVALID_LOGIN = 2;
    const BANNED = 3;
    const PERMABANNED = 4;
    const IPBANNED = 5;
    const INVALID_SECURITY_CODE = 6;
    const PENDING_CONFIRMATION = 7;
}
