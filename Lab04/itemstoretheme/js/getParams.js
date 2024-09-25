const urlParams = new URLSearchParams(window.location.search);
const itemName = urlParams.get("name");
const itemPrice = urlParams.get("price");

const itemNameElement = document.querySelector(".item-name");
const itemPriceElement = document.querySelector(".item-price");

itemNameElement.textContent = itemName;
itemPriceElement.textContent = itemPrice;

const ch = document.getElementById("charact");

fetch("/src/catalog.xml")
  .then((response) => response.text())
  .then((data) => {
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(data, "text/xml");
    const items = xmlDoc.getElementsByTagName("item");
    for (let i = 0; i < items.length; i++) {
      const name = items[i].getAttribute("name");
      if (name === itemName) {
        const description =
          items[i].getElementsByTagName("description")[0].innerHTML;
        ch.innerHTML = description;
        break;
      }
    }
  })
  .catch((error) => console.error(error));
