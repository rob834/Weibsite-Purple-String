import './style.css';
import './productGrid.css';

(function ($) {
  $.Shop = function (element) {
    this.$element = $(element);
    this.init();
  };

  $.Shop.prototype = {
    init: function () {
      // 💡 Cart settings
      this.cartPrefix = "Furniture-";
      this.cartName = this.cartPrefix + "cart";
      this.shippingRates = this.cartPrefix + "shipping-rates";
      this.total = this.cartPrefix + "total";
      this.storage = sessionStorage;

      // 💡 Elements
      this.$formAddToCart = this.$element.find("form.add-to-cart");
      this.$formCart = this.$element.find("#shopping-cart");
      this.$subTotal = this.$element.find("#stotal");
      this.$updateCartBtn = this.$element.find("#update-cart");
      this.$emptyCartBtn = this.$element.find("#empty-cart");

      // 💡 Currency settings
      this.currency = "₱";
      this.currencyString = "₱";
      this.paypalCurrency = "PHP";

      // Initialize cart
      this.createCart();
      this.handleAddToCartForm();
      this.displayCart();
      this.updateCart();
      this.emptyCart();
      this.deleteProduct();
    },

    // 🛒 Create cart if not exists
    createCart: function () {
      if (this.storage.getItem(this.cartName) == null) {
        const cart = { items: [] };
        this.storage.setItem(this.cartName, JSON.stringify(cart));
        this.storage.setItem(this.shippingRates, "0");
        this.storage.setItem(this.total, "0");
      }
    },

    // 🛒 Add to cart
    handleAddToCartForm: function () {
      const self = this;
      self.$formAddToCart.each(function () {
        const $form = $(this);
        const $product = $form.parent();
        const price = parseFloat($product.data("price"));
        const name = $product.data("name");

        $form.on("submit", function (e) {
          e.preventDefault();
          const qty = parseInt($form.find(".qty").val());
          if (!qty || qty < 1) return;

          const cart = JSON.parse(self.storage.getItem(self.cartName));
          cart.items.push({ product: name, price: price, qty: qty });
          self.storage.setItem(self.cartName, JSON.stringify(cart));

          let total = parseFloat(self.storage.getItem(self.total)) || 0;
          total += price * qty;
          self.storage.setItem(self.total, total);

          alert(`${name} added to cart!`);
        });
      });
    },

    // 🛒 Display cart items on cart.html
    displayCart: function () {
      if (!this.$formCart.length) return;

      const cart = JSON.parse(this.storage.getItem(this.cartName)) || { items: [] };
      const items = cart.items || [];
      const $tbody = this.$formCart.find("tbody");
      $tbody.html("");

      if (items.length === 0) {
        $tbody.html("<tr><td colspan='4'>Your cart is empty.</td></tr>");
        this.$subTotal.text(this.currency + " 0.00");
        return;
      }

      let total = 0;
      items.forEach((item) => {
        const subtotal = item.price * item.qty;
        total += subtotal;
        $tbody.append(`
          <tr>
            <td class="pname">${item.product}</td>
            <td class="pqty"><input type="number" value="${item.qty}" class="qty" min="1" /></td>
            <td class="pprice">${this.currency} ${item.price}</td>
            <td class="pdelete"><a href="#" data-product="${item.product}">&times;</a></td>
          </tr>
        `);
      });

      this.$subTotal.text(this.currency + " " + total.toFixed(2));
    },

    // 🗑️ Delete single item
    deleteProduct: function () {
      const self = this;
      $(document).on("click", ".pdelete a", function (e) {
        e.preventDefault();
        const productName = $(this).data("product");
        const cart = JSON.parse(self.storage.getItem(self.cartName));
        const updatedItems = cart.items.filter((item) => item.product !== productName);

        self.storage.setItem(self.cartName, JSON.stringify({ items: updatedItems }));

        let newTotal = 0;
        updatedItems.forEach((item) => (newTotal += item.price * item.qty));
        self.storage.setItem(self.total, newTotal);

        self.displayCart();
      });
    },

    // 🔄 Update cart quantities
    updateCart: function () {
      const self = this;
      self.$updateCartBtn.on("click", function (e) {
        e.preventDefault();
        const $rows = self.$formCart.find("tbody tr");
        const updatedCart = { items: [] };
        let newTotal = 0;

        $rows.each(function () {
          const $row = $(this);
          const name = $row.find(".pname").text();
          const qty = parseInt($row.find(".qty").val());
          const priceText = $row.find(".pprice").text().replace(self.currencyString, "").trim();
          const price = parseFloat(priceText);

          if (qty > 0) {
            updatedCart.items.push({ product: name, price: price, qty: qty });
            newTotal += price * qty;
          }
        });

        self.storage.setItem(self.cartName, JSON.stringify(updatedCart));
        self.storage.setItem(self.total, newTotal);
        self.displayCart();
      });
    },

    // 🧹 Empty entire cart
    emptyCart: function () {
      const self = this;
      self.$emptyCartBtn.on("click", function (e) {
        e.preventDefault();
        self.storage.setItem(self.cartName, JSON.stringify({ items: [] }));
        self.storage.setItem(self.total, "0");
        self.displayCart();
      });
    },
  };

  // Initialize when page loads
  $(function () {
    new $.Shop("#site");
  });
})(jQuery);
