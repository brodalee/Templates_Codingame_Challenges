#!/usr/bin/env php
<?php

require './vendor/autoload.php';

use ElementaryFramework\FireFS\Listener\IFileSystemListener;
use ElementaryFramework\FireFS\Events\FileSystemEvent;
use ElementaryFramework\FireFS\Watcher\FileSystemWatcher;
use ElementaryFramework\FireFS\FireFS;

class WatcherCompiler implements IFileSystemListener {

    /**
     * Action executed on any event.
     * The returned boolean will define if the
     * specific event handler (onCreated, onModified, onDeleted)
     * have to be called after this call.
     */
    public function onAny(FileSystemEvent $event): bool
    {
        $eventType = $event->getEventType();
        $date = date("d/m/Y H:i:s");

        if ($eventType === FileSystemEvent::EVENT_UNKNOWN) return true;

        $type = "";
        switch ($eventType) {
            case FileSystemEvent::EVENT_CREATE: $type = "[Created]"; break;
            case FileSystemEvent::EVENT_MODIFY: $type = "[Updated]"; break;
            case FileSystemEvent::EVENT_DELETE: $type = "[Deleted]"; break;
        }

        print "{$date}  -  $type   {$event->getPath()}\n";
        exec('./composer.phar dumpautoload -o');
        exec("php compiler.php");
        print "Dist updated\n";

        return false;
    }

    /**
     * Action executed when a file/folder is created.
     */
    public function onCreated(FileSystemEvent $event)
    { }

    /**
     * Action executed when a file/folder is updated.
     */
    public function onModified(FileSystemEvent $event)
    { }

    /**
     * Action executed when a file/folder is deleted.
     */
    public function onDeleted(FileSystemEvent $event)
    { }
}

$fs = new FireFS();
$watcher = new FileSystemWatcher($fs);

$watcher
    ->setListener(new WatcherCompiler)
    ->setRecursive(true)
    ->setPath("./src/")
    ->setWatchInterval(250)
    ->build();

$watcher->start();