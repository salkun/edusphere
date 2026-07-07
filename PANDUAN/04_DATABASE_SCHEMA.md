# DATABASE SCHEMA

## users
id, name, email, password, role

## classes
id, name

## class_students
class_id, student_id

## subjects
id, class_id, teacher_id, name

## materials
id, subject_id, title, content, file_path

## assignments
id, subject_id, title, description, type, deadline

## submissions
id, assignment_id, student_id, content, file_path, status

## grades
id, submission_id, score, feedback

## notifications
id, user_id, title, message
