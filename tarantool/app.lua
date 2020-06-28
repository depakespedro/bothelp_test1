box.cfg {listen = 3301}

queue = require('queue')
queue.create_tube('events', 'utubettl', {temporary = true, if_not_exists = true})
