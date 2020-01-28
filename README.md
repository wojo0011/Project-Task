<h1>Tasks Project</h1>
<table>
    <tr>
        <td><strong>Author:</strong>Wojtek Matwiejczyk</td>
    </tr>
    <tr>
        <td><strong>Date:</strong> 2020-01-19</td>
    </tr>
</table>

<h4>Requirements:</h4>
<p>
    Create a very simple Laravel web application for task management: -Create task (info to save: task name, priority, timestamps) -Edit task -Delete task -Reorder tasks with drag and drop in the browser. Priority should automatically be updated based on this. #1 priority goes at top, #2 next down and so on. -Tasks should be saved to a mysql table.
    BONUS POINT: add project functionality to the tasks. User should be able to select a project from a dropdown and only view tasks associated with that project.
</p>

## Setting up Application
- Extract Zip File to Project-Task-master
- Goto Project-Task-master folder
- In CLI run command <strong>'cp .env.example .env'</strong>
- In CLI run command <strong>'composer update --no-scripts'</strong>.
- In CLI run command <strong>'composer dumpautoload'</strong>.
- More information about <strong>[composer](https://getcomposer.org/)</strong>.
- Generate Laravel Application Key:
  - In CLI run command <strong>'php artisan key:generate'</strong>.

 ## Setting up Database
- Setup and Run <strong>[WAMP Server](http://www.wampserver.com/en/)</strong>.
- Create Database named <strong>'project'</strong> in <strong>[localhost/phpmyadmin](localhost/phpmyadmin)</strong>
  - Username: <strong>root</strong>
  - Password: 
  Server Choice: <strong>MySQL</strong>
  
- Go to .env file and update database settings:
  - On LINE 12: <strong>'DB_DATABASE=laravel'</strong> replace with <strong>'DB_DATABASE=project'</strong>
    
- Create tables by running migration command <strong>'php artisan migrate'</strong>.
- Populate tables by running seeder command <strong>'php artisan db:seed'</strong>.

## Start Laravel Development Server
- Serve the website by running command <strong>'php artisan serve'</strong>.
- Laravel development server started: http://127.0.0.1:8000


## TO MAKE CHANGES TO FRONTEND
- You may have to install npm by running command <strong>'npm install'</strong>.

- Frontend Vue Packages Used:
https://sortablejs.github.io/Vue.Draggable/#/third-party
  - Run CLI Command to install <strong>'npm i -S vuedraggable'</strong>

- Build the FrontEnd by running command <strong>'npm run dev'</strong>.

- Questions or Comments Email: <a href="mailto:corewebmedia@gmail.com?subject=Mail from GitHub Repo"><strong>corewebmedia@gmail.com</strong></a>  
