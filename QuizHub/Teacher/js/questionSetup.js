// questionSetup.js
// Loads/add/edits/deletes quiz questions using AJAX calls to the Control layer.

document.addEventListener("DOMContentLoaded", function () {
    const quizTitleInput = document.getElementById("quizTitle");
    const questionForm = document.getElementById("questionForm");
    const questionIdInput = document.getElementById("questionId");
    const questionText = document.getElementById("questionText");
    const optionA = document.getElementById("optionA");
    const optionB = document.getElementById("optionB");
    const optionC = document.getElementById("optionC");
    const optionD = document.getElementById("optionD");
    const correctAnswer = document.getElementById("correctAnswer");
    const marks = document.getElementById("marks");
    const addBtn = document.getElementById("addBtn");
    const clearBtn = document.getElementById("clearBtn");
    const formMsg = document.getElementById("formMsg");
    const questionsList = document.getElementById("questionsList");
    const questionCountText = document.getElementById("questionCountText");
    const totalMarksBadge = document.getElementById("totalMarksBadge");

    let currentQuestions = [];

    function renderQuestions() {
        if (currentQuestions.length === 0) {
            questionsList.innerHTML = '<p class="empty-note">No questions added yet. Enter a Quiz Title above and add your first question.</p>';
        } else {
            let html = "";
            currentQuestions.forEach(function (q, index) {
                const opts = { A: q.option_a, B: q.option_b, C: q.option_c, D: q.option_d };
                html += '<div class="q-card" data-id="' + q.id + '">';
                html += '<div class="q-top">';
                html += '<span class="q-number">' + (index + 1) + '</span>';
                html += '<span class="q-text">' + escapeHtml(q.question_text) + '</span>';
                html += '<div class="q-actions">';
                html += '<button type="button" class="edit-btn" title="Edit">&#9998;</button>';
                html += '<button type="button" class="delete delete-btn" title="Delete">&#128465;</button>';
                html += '</div></div>';

                html += '<div class="q-options">';
                ["A", "B", "C", "D"].forEach(function (letter) {
                    const isCorrect = q.correct_option === letter;
                    html += '<div class="q-option' + (isCorrect ? ' correct' : '') + '">';
                    html += '<span><span class="letter">' + letter + '.</span>' + escapeHtml(opts[letter]) + '</span>';
                    if (isCorrect) html += '<span>&#10003;</span>';
                    html += '</div>';
                });
                html += '</div>';

                html += '<div class="q-footer">';
                html += '<span>Correct: ' + q.correct_option + '. ' + escapeHtml(opts[q.correct_option]) + '</span>';
                html += '<span class="marks">' + q.marks + (q.marks == 1 ? ' mark' : ' marks') + '</span>';
                html += '</div></div>';
            });
            questionsList.innerHTML = html;
        }

        const totalMarks = currentQuestions.reduce(function (sum, q) { return sum + parseInt(q.marks); }, 0);
        questionCountText.textContent = currentQuestions.length + (currentQuestions.length == 1 ? " question added" : " questions added");
        totalMarksBadge.textContent = totalMarks + " total marks";

        document.querySelectorAll(".delete-btn").forEach(function (btn) {
            btn.addEventListener("click", function () {
                const id = btn.closest(".q-card").getAttribute("data-id");
                deleteQuestion(id);
            });
        });

        document.querySelectorAll(".edit-btn").forEach(function (btn) {
            btn.addEventListener("click", function () {
                const id = btn.closest(".q-card").getAttribute("data-id");
                loadQuestionIntoForm(id);
            });
        });
    }

    function escapeHtml(str) {
        const div = document.createElement("div");
        div.textContent = str;
        return div.innerHTML;
    }

    function loadQuestionsForTitle(title) {
        if (title.trim() === "") {
            currentQuestions = [];
            renderQuestions();
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../Control/GetQuestions.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                currentQuestions = response.questions || [];
                renderQuestions();
            }
        };
        xhr.send("quiz_title=" + encodeURIComponent(title));
    }

    quizTitleInput.addEventListener("blur", function () {
        loadQuestionsForTitle(quizTitleInput.value);
    });

    function loadQuestionIntoForm(id) {
        const q = currentQuestions.find(function (item) { return String(item.id) === String(id); });
        if (!q) return;
        questionIdInput.value = q.id;
        questionText.value = q.question_text;
        optionA.value = q.option_a;
        optionB.value = q.option_b;
        optionC.value = q.option_c;
        optionD.value = q.option_d;
        correctAnswer.value = q.correct_option;
        marks.value = q.marks;
        addBtn.textContent = "Save Changes";
        window.scrollTo({ top: 0, behavior: "smooth" });
    }

    function resetForm() {
        questionIdInput.value = "";
        questionForm.reset();
        marks.value = 1;
        addBtn.textContent = "Add Question";
        formMsg.textContent = "";
        // Keep the quiz title so more questions can be added to the same quiz
        quizTitleInput.value = quizTitleInput.value;
    }

    clearBtn.addEventListener("click", function () {
        resetForm();
    });

    function deleteQuestion(id) {
        if (!confirm("Delete this question?")) return;

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../Control/DeleteQuestion.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    currentQuestions = currentQuestions.filter(function (q) { return String(q.id) !== String(id); });
                    renderQuestions();
                }
            }
        };
        xhr.send("id=" + encodeURIComponent(id));
    }

    questionForm.addEventListener("submit", function (e) {
        e.preventDefault();
        formMsg.textContent = "";

        const quizTitle = quizTitleInput.value.trim();
        if (quizTitle === "") {
            formMsg.textContent = "Please enter a Quiz Title.";
            return;
        }

        const isEdit = questionIdInput.value !== "";
        const url = isEdit ? "../Control/EditQuestion.php" : "../Control/AddQuestion.php";

        const params = "id=" + encodeURIComponent(questionIdInput.value) +
            "&quiz_title=" + encodeURIComponent(quizTitle) +
            "&question_text=" + encodeURIComponent(questionText.value.trim()) +
            "&option_a=" + encodeURIComponent(optionA.value.trim()) +
            "&option_b=" + encodeURIComponent(optionB.value.trim()) +
            "&option_c=" + encodeURIComponent(optionC.value.trim()) +
            "&option_d=" + encodeURIComponent(optionD.value.trim()) +
            "&correct_option=" + encodeURIComponent(correctAnswer.value) +
            "&marks=" + encodeURIComponent(marks.value);

        const xhr = new XMLHttpRequest();
        xhr.open("POST", url, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    if (isEdit) {
                        const idx = currentQuestions.findIndex(function (q) { return String(q.id) === String(response.question.id); });
                        if (idx !== -1) currentQuestions[idx] = response.question;
                    } else {
                        currentQuestions.push(response.question);
                    }
                    renderQuestions();
                    resetForm();
                } else {
                    formMsg.textContent = response.message;
                }
            }
        };
        xhr.send(params);
    });
});
