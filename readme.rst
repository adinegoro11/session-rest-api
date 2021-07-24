#####################
REST API Session Data
#####################

This API using to create, update, read, and delete session data

*******************
Register
*******************
**[POST]** http://localhost/doogether/api/users/register

=======

Body:

- name
- email
- password
- confirm_password


**************************
Login
**************************
**[POST]** http://localhost/doogether/api/users/login

=======

Body:

- email
- password

Return value JWT

*******************
Show List Session
*******************
**[GET]** http://localhost/doogether/api/sessions?request={"search":{"session_name":"coaching clinic","duration":"60"},"order_by":{"session_id":"desc"}}

=======

**[HEADER]** Authentication JWT

=======

available search & order:

- user_id
- session_id
- session_name
- name (user name)
- description
- email
- duration

*******************
Create Session
*******************
**[POST]** http://localhost/doogether/api/sessions

=======

**[HEADER]** Authentication JWT

=======

Body:

- name
- description
- start
- duration

***************
Detail Session
***************
**[GET]** http://localhost/doogether/api/sessions/*(session_id)*

=======

**[HEADER]** Authentication JWT


***************
Update Session
***************
**[PUT]** http://localhost/doogether/api/sessions/*(session_id)*

=======

**[HEADER]** Authentication JWT

=======

Body:

- name
- description
- start
- duration

***************
Delete Session
***************
**[DELETE]** http://localhost/doogether/api/sessions/*(session_id)*

=======

**[HEADER]** Authentication JWT

