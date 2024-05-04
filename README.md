# audit-congress
[![Maintainability](https://api.codeclimate.com/v1/badges/17a5f75a6d1ff92adf90/maintainability)](https://codeclimate.com/github/JustZack/audit-congress/maintainability)

A project aiming to gather congressional data from several sources into one spot, with the ultimate goal being to make the actions of individual congress members transparent.

Ideal user experience: 
1. User visits site and location is automatically detected (with consent) or is asked for a zip code to locate their district & state
2. Site recomends the users senate and house members for viewing
3. User clicks their house member to see everything we know about the house member


If I ever complete this project, a public link will be hosted here. For now this is just for my own fun and portfolio. 

Note SQL Server properties changed for performance:

[mysqld]
max_connections = 500
max_allowed_packet=500M
innodb_buffer_pool_size=4096M
secure_file_priv="C:/Absolute/Path/To/Uploads"

[mysqldump]
max_allowed_packet=500M
