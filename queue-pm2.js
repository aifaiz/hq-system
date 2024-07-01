module.exports = {
    apps : [{
      name   : "hqsystem-queue",
      script : "./php artisan",
      autorestart: true,
      max_memory_restart: "70M",
      interpreter: "php",
      args: ['queue:work','--tries=5','--sleep=1']
    }]
  }