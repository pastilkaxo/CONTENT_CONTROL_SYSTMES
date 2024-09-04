const itemNames = document.querySelectorAll('.item-name');
itemNames.forEach(name => {
  name.addEventListener('click', () => {
    const itemName = name.textContent;
    const itemPrice = name.nextElementSibling.querySelector('.price').textContent;
    const itemImgSrc = name.previousElementSibling.src;
    const url = `description.html?name=${itemName}&price=${itemPrice}&img=${itemImgSrc}`;
    window.location.href = url;
  });
});

