create table sample_app_db.tasks
(
    id     int      not null primary key auto_increment,
    title  char(30) not null,
    status char(10) not null
);


desc sample_app_db.tasks;

INSERT INTO sample_app_db.tasks (title, status) VALUES
('title a', 'todo'),
('title b', 'doing'),
('title c', 'done');

SELECT * from sample_app_db.tasks;

/Applications/XAMPP/xamppfiles/htdocs/Self_framework/TaskApp/migrations/init_tasks_table.sql