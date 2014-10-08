<?php
/**
 * Author: Normann
 * Date: 7/08/14
 * Time: 1:45 PM
 */

class SitemapUpdator extends SiteTreeExtension {
    public function onAfterPublish(&$original) {
        $list = QueuedJobDescriptor::get()->filter(array(
            'JobStatus'		=> array(QueuedJob::STATUS_INIT, QueuedJob::STATUS_RUN), //Initialising and Running
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
                'JobStatus'		=> array(QueuedJob::STATUS_NEW), //New
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
            //first remove all the legacy job which might be in these status:
            /**
             * New (but Start data somehow is less than now)
             * Waiting
             * Completed
             * Paused
             * Cancelled
             * Broken
             */
            $list = QueuedJobDescriptor::get()->filter(array(
                'Implementation'=> 'GenerateGoogleSitemapJob',
            ));
            if($list && $list->count()) $list->removeAll();

            $job = new GenerateGoogleSitemapJob();
            singleton('QueuedJobService')->queueJob($job);
        }
    }
}