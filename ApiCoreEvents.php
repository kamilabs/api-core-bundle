<?php

namespace Kami\ApiCoreBundle;

class ApiCoreEvents
{
    const RESOURCE_INDEX_REQUEST  = 'kami.api_core.resource_index_request';
    const RESOURCE_INDEX_RESPONSE = 'kami.api_core.resource_index_response';

    const RESOURCE_REQUEST        = 'kami.api_core.resource_request';
    const RESOURCE_RESPONSE       = 'kami.api_core.resource_response';

    const RESOURCE_CREATE         = 'kami.api_core.resource_create';
    const RESOURCE_CREATED        = 'kami.api_core.resource_created';
    const RESOURCE_CREATE_FAILED  = 'kami.api_core.resource_create_failed';

    const RESOURCE_EDIT           = 'kami.api_core.resource_create';
    const RESOURCE_EDITED_FAILED  = 'kami.api_core.resource_created_failed';

    const RESOURCE_DELETE         = 'kami.api_core.resource_delete';
    const RESOURCE_DELETED        = 'kami.api_core.resource_deleted';
    const RESOURCE_DELETE_FAILED  = 'kami.api_core.resource_delete_failed';
}
