<?php
/**
 * Author: Normann
 * Date: 7/08/14
 * Time: 1:45 PM
 */

class SitemapUpdator extends SiteTreeExtension {
    public function onAfterPublish(&$original) {
        $list = QueuedJobDescriptor::get()->filter(array(
            'JobStatus'		=> array(QueuedJob::STATUS_INIT, QueuedJob::STATUS_RUN),
            'Implementation'=> 'GenerateGoogleSitemapJob',
        ));
        $existingJob = $list->first();
        if ($existingJob && $existingJob->exists()) {
            // Doing nothing, there is one job for generating sitemap already running
        } else {
            $where = '"StartAfter" > \'' . date('Y-m-d H:i:s').'\'';
            $list = QueuedJobDescriptor::get()->where($where);
            $list = $list->filter(array(
                'Implementation'=> 'GenerateGoogleSitemapJob',
                'JobStatus'		=> array(QueuedJob::STATUS_NEW),
            ));
            $list = $list->sort('ID', 'ASC');
            if ($list && $list->Count()) {
                // make it to be executed immediately
                $existingJob = $list->first();
                $existingJob->StartAfter = date('Y-m-d H:i:s');
                $existingJob->write();
                return;
            }

            // if no such a job existing, create a new one for the first time, and run immediately
            $list = QueuedJobDescriptor::get()->filter(array(
                'Implementation'=> 'GenerateGoogleSitemapJob',
            ));
            $existingJob = $list->first();
            if (!$existingJob || !$existingJob->exists()) {
                $job = new GenerateGoogleSitemapJob();
                singleton('QueuedJobService')->queueJob($job);
            }
        }
    }
}