# ERD

users
- id
- name
- email
- password
- role

classes
- id
- name

subjects
- id
- class_id
- teacher_id

materials
- id
- subject_id
- title

assignments
- id
- subject_id
- type
- deadline

submissions
- id
- assignment_id
- student_id

grades
- id
- submission_id
- score
- feedback

portfolios
- id
- student_id
