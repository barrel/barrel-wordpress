# ACF JSON Synchronization

Advanced Custom Fields Pro 5 for WordPress allows synchronization of all fields and field groups. To function properly, this folder should be writable by the webserver process user. Depending on the system, either the `_www` or `apache` user must be added to the system group that can modify this directory and it's contents. 

## Permissions and Troubleshooting

- If you make a change to ACF locally using the Custom Fields admin pages, then you should also see changes to the json files in the acf-json folder.
- If your web server is running inside of a container-based system, there could be issues with the file synchronization and permissions on Mac systems.
- Do not change permissions to 777 and commit those changes. If you have trouble with ACL, you can change permissions to 777 until you make the changes, but you should switch the permissions back to 644 before committing. 

`git -c core.fileMode=false commit -am "File changes without changing filemode"`

## Workflows

Non-developers will be using a multi-dev env on Pantheon while in sftp mode. On Pantheon, when you are in SFTP mode and make a changes to Custom Fields using its admin, the files get created the same was as the do locally and show up as files to be committed. These files should be committed and merged into `develop`.
