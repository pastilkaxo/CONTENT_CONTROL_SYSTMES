const itemNames = document.querySelectorAll(".item-name");
itemNames.forEach((name) => {
  name.addEventListener("click", () => {
    const itemName = name.textContent;
    const itemPrice =
      name.nextElementSibling.querySelector(".price").textContent;
    const itemImgSrc = name.previousElementSibling.src;
    const url = `/описание товара`;
    window.location.href = url;
  });
});
