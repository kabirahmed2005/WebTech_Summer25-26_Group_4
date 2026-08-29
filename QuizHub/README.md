# QuizHub - Simple Online Quiz System

A university web technology project. Built with plain **PHP (MySQLi)**, **MySQL**,
**vanilla JavaScript (AJAX / XMLHttpRequest)**, and plain **CSS** — no frameworks.

## Folder structure

```
QuizHub/
├── index.php                     Redirects to the login page
├── database/quizhub.sql          Database schema + seed data
├── Common/model/DatabaseConnection.php   Shared MySQLi connection class
│
├── Auth/                         Login & Registration (shared by Student/Teacher)
│   ├── View/      Login.php, Register.php
│   ├── Control/   LoginCheck.php, RegisterCheck.php, CheckUsername.php, Logout.php
│   ├── css/       auth.css
│   └── js/        LoginValidation.js, RegisterValidation.js
│
├── Teacher/
│   ├── View/      QuestionSetup.php, MyQuizzes.php
│   ├── Control/   AddQuestion.php, EditQuestion.php, DeleteQuestion.php, GetQuestions.php
│   ├── css/       teacher.css
│   └── js/        questionSetup.js
│
├── Student/
│   ├── View/      QuizList.php, TakeQuiz.php, Result.php
│   ├── Control/   GetQuestion.php, SubmitQuiz.php
│   ├── css/       student.css
│   └── js/        quiz.js
│
└── Admin/
    ├── View/      Dashboard.php
    ├── Control/   UpdateUserStatus.php
    ├── css/       admin.css
    └── js/        dashboard.js
```

This mirrors a standard `Control / View / model` split per user role, the same
pattern used across the class project this was modeled on.

## Setup

1. Start Apache + MySQL (e.g. via XAMPP/WAMP) and place this `QuizHub` folder
   inside your `htdocs` (or server root).
2. Open phpMyAdmin (or the MySQL CLI) and import `database/quizhub.sql`. This
   creates the `quizhub` database with tables `users`, `quizzes`, `questions`,
   `results`, plus some sample data.
3. Check `Common/model/DatabaseConnection.php` matches your local MySQL
   username/password (defaults: `root` / empty password).
4. Visit `index.php` in your browser — it redirects to the login page.

## Seeded accounts (password for all: `123456`)

| Username             | Role    | Status   |
|----------------------|---------|----------|
| admin                | admin   | approved |
| Prof. Sarah Ahmed    | teacher | approved |
| Mr. Usman Tariq      | teacher | approved |
| Ali Hassan           | student | approved |
| Ayesha Malik         | student | approved |
| Sara Khan            | teacher | pending  |
| ali.hassan2          | student | pending  |

## How it works

- **Register/Login** — Register as Student or Teacher; new accounts start as
  `pending` and need Admin approval before they can log in. Full Name is used
  as the username, and its availability is checked live via AJAX.
- **Teacher** — Adds questions to a quiz (identified by Quiz Title) from the
  "Question Setup" page. Adding, editing and deleting questions all happen via
  AJAX, without reloading the page. "My Quizzes" lists the teacher's quizzes.
- **Student** — Picks a quiz from "Available Quizzes" and answers one question
  at a time; each question is fetched via AJAX, and the whole quiz is
  submitted and scored via AJAX at the end.
- **Admin** — Reviews pending registrations and Accepts/Rejects them via AJAX
  (the row disappears instantly), and views read-only tables of student marks
  and quizzes conducted per teacher.
