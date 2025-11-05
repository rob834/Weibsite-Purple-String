import './style.css'
import './productGrid.css'

const products = [
  {
    name: "2025 Calendar",
    category: "print",
    price: 3000,
    rating: 4.5,
    image: "./img/smartphone.png",
  },
  {
    name: "Trifold/Bifold Flyer/Brochure",
    category: "print",
    price: 850,
    rating: 4.9,
    image: "./img/laptop.png",
  },
  {
    name: "Crochet Mang Tae Keychain",
    category: "crochet",
    price: 185,
    rating: 5.0,
    image: "./img/tshirt.png",
  },
  {
    name: "Wall Decor Inspirational Syntra Board 3mm",
    category: "miscellanous",
    price: 120,
    rating: 5.0,
    image: "./img/novalbook.png",
  },
  {
    name: "Crochet Alcopouch Customized 30ml",
    category: "crochet",
    price: 29,
    rating: 1.0,
    image: "./img/jean.png",
  },
  {
    name: "2026 Calendar/Wall Calendar/Desk Calendar",
    category: "print",
    price: 18,
    rating: 5.0,
    image: "./img/tablet.png",
  },
  {
    name: "Coaster for Crochet Lovers(1 pc)",
    category: "crochet",
    price: 35,
    rating: 5.0,
    image: "./img/backpack.png",
  },
  {
    name: "Alcohol Bottle Refill with name (Heart Shaped)",
    category: "miscellanous",
    price: 65,
    rating: 4.8,
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
