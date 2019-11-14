<?php

namespace Chrometoaster\SiteHost\API;

final class Constants
{
    /**
     * Get info based on the API key
     *
     * see https://docs.sitehost.nz/api/v1/?path=/api/get_info&action=GET
     */
    const ENDPOINT_API_GETINFO = '/1.0/api/get_info.json';

    /**
     * Get cloud stack information
     *
     * see https://docs.sitehost.nz/api/v1/?path=/cloud/stack/get&action=GET
     */
    const ENDPOINT_CLOUD_STACK_GET = '/1.0/cloud/stack/get.json';

    /**
     * Update cloud stack information
     *
     * see https://docs.sitehost.nz/api/v1/?path=/cloud/stack/update&action=POST
     */
    const ENDPOINT_CLOUD_STACK_UPDATE = '/1.0/cloud/stack/update.json';

    /**
     * Get job information
     *
     * see https://docs.sitehost.nz/api/v1/?path=/job/get&action=GET
     */
    const ENDPOINT_JOB_GET = '/1.0/job/get.json';

    /**
     * Job type - scheduler
     */
    const JOB_TYPE_SCHEDULER = 'scheduler';

    /**
     * Job type - daemon
     */
    const JOB_TYPE_DAEMON = 'daemon';

    /**
     * Job state - pending, waiting for execution
     */
    const JOB_STATE_PENDING = 'Pending';

    /**
     * Job state - running, in progress
     */
    const JOB_STATE_RUNNING = 'Running';

    /**
     * Job state - finished with an error
     */
    const JOB_STATE_FAILED = 'Failed';

    /**
     * Job state - finished successfully
     */
    const JOB_STATE_COMPLETED = 'Completed';

    /**
     * List prefix - vhosts within container labels section
     */
    const VHOSTS_KEY_LABELS = 'nz.sitehost.container.website.vhosts';

    /**
     * List prefix - vhosts within container environments section
     */
    const VHOSTS_KEY_ENVIRONMENTS = 'VIRTUAL_HOST';
}
