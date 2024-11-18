<?php
defined('_JEXEC') or die;
?>

<style>
    .contact-form-container {
        max-width: 300px;
        margin: 0 auto;
        text-align: center;
    }
    .contact-form {
        display: none;
        margin-top: 10px;
    }
    .contact-form input, .contact-form textarea {
        width: 100%;
        padding: 5px;
        margin-bottom: 10px;
    }
    .toggle-button {
        background-color: #007bff;
        color: white;
        padding: 10px;
        border: none;
        cursor: pointer;
    }
</style>

<div class="contact-form-container">
    <button class="toggle-button" onclick="toggleForm()">Связаться с нами</button>
    <div class="contact-form" id="contactForm">
        <form action="#" method="post">
            <input type="text" name="name" placeholder="Ваше имя" required>
            <input type="text" name="subject" placeholder="Тема" required>
            <textarea name="message" placeholder="Сообщение" rows="4" required></textarea>
            <button type="submit" class="toggle-button">Отправить</button>
        </form>
    </div>
</div>

<script>
    function toggleForm() {
        var form = document.getElementById('contactForm');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
</script>