<?php

namespace Kami\ApiCoreBundle;

class ApiCoreEvents
{
    const RESOURCE_INDEX_REQUEST   = 'kami.api_core.resource_index_request';
    const RESOURCE_INDEX_RESPONSE  = 'kami.api_core.resource_index_response';

    const RESOURCE_REQUEST         = 'kami.api_core.resource_request';
    const RESOURCE_RESPONSE        = 'kami.api_core.resource_response';

    const RESOURCE_FILTER_REQUEST  = 'kami.api_core.resource_filter_request';
    const RESOURCE_FILTER_RESPONSE = 'kami.api_core.resource_filter_request';

    const RESOURCE_CREATE          = 'kami.api_core.resource_create';
    const RESOURCE_CREATED         = 'kami.api_core.resource_created';
    const RESOURCE_CREATE_FAILED   = 'kami.api_core.resource_create_failed';

    const RESOURCE_EDIT            = 'kami.api_core.resource_edit';
    const RESOURCE_EDITED          = 'kami.api_core.resource_edited';
    const RESOURCE_EDIT_FAILED     = 'kami.api_core.resource_edit_failed';

    const RESOURCE_DELETE          = 'kami.api_core.resource_delete';
    const RESOURCE_DELETED         = 'kami.api_core.resource_deleted';
    const RESOURCE_DELETE_FAILED   = 'kami.api_core.resource_delete_failed';
}
