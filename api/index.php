<?php

// Vercel serverless entry point for the Laravel application.
// Runtime-writeable directories must live under /tmp because the deployed
// filesystem is read-only.

@mkdir('/tmp/views', 0777, true);

require __DIR__.'/../public/index.php';
