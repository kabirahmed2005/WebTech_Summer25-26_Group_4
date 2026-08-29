// quiz.js
// Loads each quiz question via AJAX, tracks the student's selected answers,
// and submits everything to the server via AJAX when the quiz is finished.

document.addEventListener("DOMContentLoaded", function () {
    const quizCard = document.getElementById("quizCard");
    const questionOfText = document.getElementById("questionOfText");
    const progressFill = document.getElementById("progressFill");
    const progressLabel = document.getElementById("progressLabel");
    const dotsWrap = document.getElementById("dotsWrap");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");

    let currentIndex = 1;
    let totalQuestions = 0;
    const answers = {}; // { question_id: "A" }

    function fetchQuestion(index) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../Control/GetQuestion.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (!response.success) {
                    quizCard.innerHTML = '<p class="empty-note">' + response.message + '</p>';
                    return;
                }
                totalQuestions = response.total;
                renderQuestion(response.question, index, totalQuestions);
            }
        };
        xhr.send("quiz_id=" + encodeURIComponent(QUIZ_ID) + "&index=" + encodeURIComponent(index));
    }

    function renderQuestion(q, index, total) {
        currentIndex = index;

        questionOfText.textContent = "Question " + index + " of " + total;
        const percent = (index / total) * 100;
        progressFill.style.width = percent + "%";
        progressLabel.textContent = index + " / " + total + " Questions";

        let dotsHtml = "";
        for (let i = 1; i <= total; i++) {
            dotsHtml += '<span class="dot' + (i === index ? ' active' : '') + '"></span>';
        }
        dotsWrap.innerHTML = dotsHtml;

        const opts = { A: q.option_a, B: q.option_b, C: q.option_c, D: q.option_d };
        const selected = answers[q.id];

        let html = '<div class="q-top">';
        html += '<span class="q-number">' + index + '</span>';
        html += '<span class="q-text">' + escapeHtml(q.question_text) + '</span>';
        html += '</div>';

        ["A", "B", "C", "D"].forEach(function (letter) {
            html += '<div class="option' + (selected === letter ? ' selected' : '') + '" data-letter="' + letter + '">';
            html += '<span class="letter">' + letter + '</span><span>' + escapeHtml(opts[letter]) + '</span>';
            html += '</div>';
        });

        quizCard.innerHTML = html;

        document.querySelectorAll(".option").forEach(function (opt) {
            opt.addEventListener("click", function () {
                answers[q.id] = opt.getAttribute("data-letter");
                document.querySelectorAll(".option").forEach(function (o) { o.classList.remove("selected"); });
                opt.classList.add("selected");
            });
        });

        prevBtn.disabled = index === 1;
        nextBtn.textContent = index === total ? "Submit Quiz" : "Next \u203A";
    }

    function escapeHtml(str) {
        const div = document.createElement("div");
        div.textContent = str;
        return div.innerHTML;
    }

    prevBtn.addEventListener("click", function () {
        if (currentIndex > 1) fetchQuestion(currentIndex - 1);
    });

    nextBtn.addEventListener("click", function () {
        if (currentIndex < totalQuestions) {
            fetchQuestion(currentIndex + 1);
        } else {
            submitQuiz();
        }
    });

    function submitQuiz() {
        nextBtn.disabled = true;
        nextBtn.textContent = "Submitting...";

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../Control/SubmitQuiz.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    window.location.href = "Result.php?score=" + response.score + "&total=" + response.total_marks;
                } else {
                    alert(response.message);
                    nextBtn.disabled = false;
                    nextBtn.textContent = "Submit Quiz";
                }
            }
        };
        xhr.send("quiz_id=" + encodeURIComponent(QUIZ_ID) + "&answers=" + encodeURIComponent(JSON.stringify(answers)));
    }

    fetchQuestion(1);
});
