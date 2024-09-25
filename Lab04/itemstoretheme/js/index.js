const cart = {};

function addToCart(name, price, cart) {
  if (cart[name]) {
    // если товар уже есть в корзине
    cart[name].count++; // увеличиваем его количество
  } else {
    // если товара еще нет в корзине
    const item = { name, price, count: 1 };
    cart[name] = item; // добавляем товар в корзину
  }
  console.log(cart);
  updateCart(cart);
}

function updateCart(cart) {
  const cartElement = document.getElementById("namec");
  const count = Object.values(cart).reduce((acc, item) => acc + item.count, 0); // считаем общее количество товаров
  cartElement.innerHTML = `Корзина: ${count}`;

  const cartList = document.getElementById("cart-list");
  cartList.innerHTML = "";

  for (const name in cart) {
    // проходим по всем товарам в корзине
    const item = cart[name];
    const par = document.createElement("p");
    par.textContent = `${item.name}: ${item.price} x ${item.count}`; // выводим название, цену и количество товара
    cartList.appendChild(par);
  }
}

const buttons = document.querySelectorAll(".button");

buttons.forEach((button) => {
  button.addEventListener("click", () => {
    const card = button.closest(".card");
    const name = card.querySelector(".item-name").textContent;
    const price = card.querySelector(".price").textContent;
    addToCart(name, price, cart);
  });
});

// Открыть модальное окно при клике на кнопку "Корзина"
const cartButton = document.getElementById("cart-button");
const cartModal = document.getElementById("cart-modal");
const cartModalClose = document.getElementsByClassName("close")[0];

cartButton.addEventListener("click", () => {
  cartModal.style.display = "block";
});

// Закрыть модальное окно при клике на кнопку "X"
cartModalClose.addEventListener("click", () => {
  cartModal.style.display = "none";
});

// Закрыть модальное окно при клике вне его области
window.addEventListener("click", (event) => {
  if (event.target == cartModal) {
    cartModal.style.display = "none";
  }
});

const clearButton = document.getElementById("clear-button");

clearButton.addEventListener("click", () => {
  for (const name in cart) {
    delete cart[name]; // удаляем все товары из корзины
  }
  updateCart(cart); // вызываем функцию обновления корзины
});

// Find item ==>

let cardget = document.getElementById("get-card");

cardget.onclick = function () {
  let item_get_name = document.getElementById("get-name").value;
  let item_get_price = document.getElementById("get-cost").value;

  console.log(item_get_name + "  " + item_get_price + "$");
};

// modal #2

// Получаем ссылки на элементы страницы
var modal = document.getElementById("myModal");
var btn = document.getElementById("call");
var span = document.getElementsByClassName("modal-close")[0];

// Показываем модальное окно при нажатии на кнопку
btn.onclick = function () {
  modal.style.display = "block";
};

// Скрываем модальное окно при нажатии на крестик
span.onclick = function () {
  modal.style.display = "none";
};

// Скрываем модальное окно при нажатии за его пределами
window.onclick = function (event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
};
