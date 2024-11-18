<?php
defined('_JEXEC') or die;
?>

<style>
    .contact-form-container {
        max-width: 400px;
        margin: 20px auto;
        text-align: center;
        font-family: Arial, sans-serif;
        color:white;
    }
    .toggle-button, .contact-form button {
        background-color: lime;
        color: black;
        padding: 10px 15px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .toggle-button:hover, .contact-form button:hover {
        background-color: red;
        color:white;
    }
    .contact-form {
        display: none;
        margin-top: 15px;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: green;
    }
    .contact-form input, .contact-form textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }
    .contact-form input:focus, .contact-form textarea:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    }
    .contact-form label {
        font-weight: bold;
        margin-right: 5px;
    }
</style>

<div class="contact-form-container">
    <button class="toggle-button" onclick="toggleForm()">Contact Us</button>
    <div class="contact-form" id="contactForm">
        <form id="myForm">
            <input type="text" name="name" id="name" placeholder="Ваше имя" required>
            <input type="text" name="subject" placeholder="Тема" required>
            <textarea name="message" id="message" placeholder="Сообщение" rows="4" required></textarea>
            <div>
                <label>Вычислите сумму:</label>
                <input type="text" id="num1" value="5" readonly/>
                +
                <input type="text" id="num2" value="2" readonly/>
                =
                <input type="number" id="res" required/>
            </div>
            <button type="submit" class="toggle-button">Отправить</button>
        </form>
    </div>
</div>

<script>
    function toggleForm() {
        var form = document.getElementById('contactForm');
        form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }

    function validateCaptcha() {
        var num1 = parseInt(document.getElementById("num1").value);
        var num2 = parseInt(document.getElementById("num2").value);
        var res = parseInt(document.getElementById("res").value);
        var name = document.getElementById("name").value;
        var message = document.getElementById("message").value;

        if ((num1 + num2) !== res) {
            alert("Неправильно решена капча!");
            return false;
        }

        console.log("Имя:", name);
        console.log("Сообщение:", message);
        
        document.getElementById('myForm').reset();
        toggleForm(); 

        return true;
    }


document.getElementById('myForm').addEventListener('submit', function(event) {
    event.preventDefault();
    return validateCaptcha();
});

</script>