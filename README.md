# Full Stack Web Development Practice project using Vue.js and PHP

The purpose of this project was not to create something specific but to practice certain aspects of Web Development common to most appliations.
The project contains a Vue SPA frontend and a PHP API backend with a MySQL database served from the same origin using Nginx. 

## Features

- **Session based authentication** with email verification using PHP Mailer.
- **Custom session management.**
- **Data persistence** (including session data) to a MySQL database using PDO.
- **custom PHP API framework** with advanced routing and object-oriented design.
- **Responsive design**
- **Localization** support for multiple languages.
- **Vuex state management** for managing global application state in Vue.js.
- **Custom UI components** (including some complex ones) made in JavaScript.
- **Well-organized and structured code** for ease of understanding and future development.

## Installation
Navigate to the backend folder and run composer install.
Navigate to the frontend folder and run npm install.
To configure the backend go into the config folder and find the _db.php and _mailer.php where i have left a few empty values to replace with the desired configuration.

## Nginx configuration
I left some placeholders starting with '###' to makr out what to configure. The port numbers don't really matter either and they can be replaced with whatever may be suitable. 

Production
server {
        listen       80;
        server_name  ###The name of your server;
        set $my_root   ###root to PHP backend public folder;
        root   ###root to Vue.js dist folder;

        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Content-Type-Options "nosniff";

        location /app {
            try_files $uri $uri/ /index.html;
        }

         location ~ \.php$ {
           
           fastcgi_pass   127.0.0.1:9000;
           fastcgi_index  index.php;
           fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
           fastcgi_param  QUERY_STRING     $query_string;
           include        fastcgi_params;
        }

        location / {
            try_files $uri $uri/ /index.php;
        }

        error_page   500 502 503 504  /50x.html;
        location = /50x.html {
            root   html;
        }

       
    }
}

Development
For development we want to proxy the Vue development server to be able to use fast reloading.
server {
        listen       80;
        server_name  ###The name of your server;
        root   ###path to PHP backend public folder;

        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Content-Type-Options "nosniff";

        location /app {
            proxy_pass http://localhost:8080;  # Vue dev server running on port 8080
            proxy_http_version 1.1;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection 'upgrade';
            proxy_set_header Host $host;
            proxy_cache_bypass $http_upgrade;
        }

        location ~ \.php$ {
           
           fastcgi_pass   127.0.0.1:9000;
           fastcgi_index  index.php;
           fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
           fastcgi_param  QUERY_STRING     $query_string;
           include        fastcgi_params;
        }        

        location / {
            try_files $uri /index.php$is_args$args;
        }
        error_page   500 502 503 504  /50x.html;
        location = /50x.html {
            root   html;
        }
    }
}
