import './style.css'
import './productGrid.css'

const products = [
  {
    name: "Smartphone",
    category: "electronics",
    price: 299,
    rating: 4.5,
    image: "./img/smartphone.png",
  },
  {
    name: "Laptop",
    category: "electronics",
    price: 899,
    rating: 4.7,
    image: "./img/laptop.png",
  },
  {
    name: "T-Shirt",
    category: "clothing",
    price: 25,
    rating: 4.2,
    image: "./img/tshirt.png",
  },
  {
    name: "Novel Book",
    category: "books",
    price: 15,
    rating: 4.8,
    image: "./img/novalbook.png",
  },
  {
    name: "Jeans",
    category: "clothing",
    price: 50,
    rating: 4.1,
    image: "./img/jean.png",
  },
  {
    name: "Tablet",
    category: "electronics",
    price: 199,
    rating: 4.3,
    image: "./img/tablet.png",
  },
  {
    name: "Backpack",
    category: "clothing",
    price: 60,
    rating: 4.4,
    image: "./img/backpack.png",
  },
  {
    name: "Science Book",
    category: "books",
    price: 40,
    rating: 4.6,
    image: "./img/scienceBook.png",
  },
];

const productGrid = document.getElementById("productGrid");
const categoryFilter = document.getElementById("categoryFilter");
const priceFilter = document.getElementById("priceFilter");
const sortBy = document.getElementById("sortBy");

function renderProducts(items) {
  productGrid.innerHTML = "";

  if (items.length === 0) {
    productGrid.innerHTML = "<p>No products found.</p>";
    return;
  }

  items.forEach((product) => {
    const card = document.createElement("div");
    card.className = "card";
    // ✅ Add data attributes for jQuery cart
    card.setAttribute("data-name", product.name);
    card.setAttribute("data-price", product.price);

    card.innerHTML = `
      <img src="${product.image}" alt="${product.name}" />
      <div class="card-body">
        <h3>${product.name}</h3>
        <p>Category: ${product.category}</p>
        <p>Price: ₹${product.price}</p>
        <p class="rating">Rating: ⭐ ${product.rating}</p>
      </div>
      <form class="add-to-cart" action="cart.html" method="post">
        <div>
          <label>Quantity</label>
          <input type="number" name="qty" class="qty" value="1" min="1" />
        </div>
        <p><input type="submit" value="Add to cart" class="btn" /></p>
      </form>
    `;

    // ✅ Prevent form refresh & manually call shopQuery
    card.querySelector("form").addEventListener("submit", (e) => {
      e.preventDefault(); // stop page reload
      const qty = parseInt(card.querySelector(".qty").value);
      const name = card.dataset.name;
      const price = parseFloat(card.dataset.price);

      // Save to sessionStorage (same way shopQuery expects)
      const cartName = "Furniture-cart";
      const storage = sessionStorage;
      const cart = JSON.parse(storage.getItem(cartName)) || { items: [] };

      cart.items.push({ product: name, price: price, qty: qty });
      storage.setItem(cartName, JSON.stringify(cart));

      // Optionally show alert or notification
      alert(`${name} added to cart!`);
    });

    productGrid.appendChild(card);
  });
}

function filterAndSort() {
  let filtered = [...products];

  const category = categoryFilter.value;
  const price = priceFilter.value;
  const sort = sortBy.value;

  if (category !== "all") {
    filtered = filtered.filter((p) => p.category === category);
  }

  if (price !== "all") {
    const [min, max] = price.split("-").map(Number);
    filtered = filtered.filter((p) => p.price >= min && p.price <= max);
  }

  switch (sort) {
    case "price-asc":
      filtered.sort((a, b) => a.price - b.price);
      break;
    case "price-desc":
      filtered.sort((a, b) => b.price - a.price);
      break;
    case "rating":
      filtered.sort((a, b) => b.rating - a.rating);
      break;
  }

  renderProducts(filtered);
}

categoryFilter.addEventListener("change", filterAndSort);
priceFilter.addEventListener("change", filterAndSort);
sortBy.addEventListener("change", filterAndSort);

renderProducts(products);
