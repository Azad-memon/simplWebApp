  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <!--Dashboard Scripts -->
  <script>
      //   let cart = [];
      let currentProduct = {};
      let currentCategory = '';

      // Category filter logic
      // Apply category filters
      $(document).on('change', '.category-checkbox', applyCategoryFilters);

      function applyCategoryFilters() {
          const activeCategories = $(".category-checkbox:checked").map(function() {
              return $(this).val();
          }).get();

          if (activeCategories.length === 0) {
              $(".products[data-category]").removeClass('d-none');
          } else {
              $(".products[data-category]").each(function() {
                  const cat = $(this).data('category');
                  $(this).toggleClass('d-none', !activeCategories.includes(cat));
              });
          }

          applySearch();
      }




      // Search functionality
      function applySearch() {
          const searchTerm = $("#product-search").val().toLowerCase().trim();

          if (searchTerm === '') {
              // Show all products and their containers in visible categories
              $(".products:not(.d-none) .col-6").show();
              $(".products:not(.d-none) .product-card").show();
          } else {
              // Hide all product containers first
              $(".products .col-6").hide();
              // Show only containers with matching products in visible categories
              $(".products:not(.d-none) .col-6").each(function() {
                  const productName = $(this).find('.product-card').data('name').toLowerCase();
                  if (productName && productName.includes(searchTerm)) {
                      $(this).show();
                  }
              });
          }
      }

      $(document).on('change', '.category-filter', applyCategoryFilters);
      $(document).on('input', '#product-search', applySearch);
      // Initial apply in case defaults change
      $(function() {
          applyCategoryFilters();
      });

      // Remove item from cart (main script section)

      function loadVariantOptions($el, preselectedData = null) {
          if (!$el || $el.length === 0) return;

          const product_id = $el.data("product_id");
          const variant_id = $el.val();
          const basePrice = $el.data("price");
          $("#productModal").find("input[type='checkbox']").prop("checked", false);


          //console.log(preselectedData);
          $.ajax({
              url: "{{ route('variants.options') }}",
              type: "GET",
              data: {
                  variant_id,
                  product_id
              },
              success: function(res) {
                  if (res.html && res.html.trim() !== "") {
                      $("#addons-ingredients-section").html(res.html);


                      if (preselectedData) {
                          let ingredients = preselectedData.ingredients;
                          // Preselect Addons
                          preselectedData.addons?.forEach(addon => {
                              let addonId = addon.id ??
                                  addon;
                              $(`.ingredient-${addonId}`).prop("checked", true);
                          });


                          if (typeof ingredients === "string") {
                              try {
                                  ingredients = JSON.parse(ingredients);
                              } catch (e) {
                                  // console.error("Invalid JSON for ingredients:", ingredients);
                                  ingredients = [];
                              }
                          }

                          //   if (Array.isArray(ingredients)) {
                          //       ingredients.forEach(ing => {
                          //           let ingId = ing.ing_id ?? ing;
                          //           //console.log("Preselecting ingredient:", ingId);
                          //           $(`.ingredient-${ingId}`).prop("checked", true);
                          //       });
                          //   }
                          if (preselectedData.qty) {
                              $("#product-qty").val(preselectedData.qty);
                          }
                      }
                  } else {
                      $("#addons-ingredients-section").html(
                          "<p class='text-muted'>No addons or ingredients available</p>"
                      );
                  }

                  $("#product-total-price").attr("data-base-price", basePrice);
                  $("#product-price").text("Rs: " + basePrice.toFixed(2));
                  calculateTotal();
              },
              error: function() {
                  $("#addons-ingredients-section").html(
                      "<p class='text-danger'>Failed to load.</p>"
                  );
              }
          });
      }



      // Show Product Modal
      $(".product-card").click(function() {
          // $("#addons-ingredients-section").html('');

          let product = {
              name: $(this).data("name"),
              imgSrc: $(this).data("image"),
              id: $(this).data("id"),
              productSizes: $(this).data("productsizes") || [],
              price: parseFloat($(this).data("price")),
              qty: 1
          };

          loadProductModal(product);
          // ------------------ VARIANT CHANGE HANDLER ------------------
          // variant change event


          // ------------------ SHOW MODAL ------------------
          $("#addons-ingredients-section").html(""); // reset addons & ingredients section
          $("#product-total-price").attr("data-base-price", product.price); // reset base price
          calculateTotal();
          const productModal = new bootstrap.Modal(document.getElementById("productModal"));
          productModal.show();
          // load default variant addons/ingredients
          const $default = $(".variant-radio:checked").first();
          if ($default.length) {
              loadVariantOptions($default);
          }
      });

      function loadProductModal(product, variant_id = null, addons = null, ingredients = null, size_id = null, resHtml =
          null) {
          currentProduct = {
              imgSrc: product.imgSrc,
              qty: product.qty || 1,
              productSizes: product.productSizes || [],
              id: product.id,
              price: product.price,
          };
          const addToCartBtn = document.getElementById('add-to-cart-btn');
          const parentDiv = addToCartBtn.parentElement; // parent div mil gaya

          if (product.price == 0) {
              addToCartBtn.setAttribute('style', 'pointer-events: none; opacity: 0.6;');
              parentDiv.classList.add('align-items-end', 'h-100', 'py-5');
          } else {
              addToCartBtn.removeAttribute('style');
              parentDiv.classList.remove('align-items-end', 'h-100', 'py-5');
          }

          // Set details
          $("#product-name").text(product.name);
          $("#product-price").text("Rs:" + product.price.toFixed(2));
          $("#product-image").attr("src", product.imgSrc);
          $("#product-qty").val(product.qty || 1);

          const totalPriceEl = document.getElementById("product-total-price");
          if (totalPriceEl) {
              totalPriceEl.dataset.basePrice = product.price;
              totalPriceEl.innerText = "Rs: " + product.price.toFixed(2);
          }

          // Variants render
          let variantHtml = "";
          const product_sizes = currentProduct.productSizes || [];
          if (product_sizes.length > 0) {

              variantHtml += `
    <div class="mb-3">
      <h6 class="fw-bold mb-2">Available Sizes</h6>
      <div class="d-flex flex-wrap gap-2">
  `;
              product_sizes.forEach((ps, index) => {
                  const sizeCode = ps.code || "N/A";
                  const price = Number(ps.price) || 0;
                  const id = `variant-${ps.variant_id}-${ps.size_id}`;
                  const sataddon = (size_id == ps.size_id) ? encodeURIComponent(JSON.stringify(addons || [])) :
                      "";
                  const satingredients = (size_id == ps.size_id) ? encodeURIComponent(JSON.stringify(
                      ingredients || [])) : "";


                  variantHtml += `
                <div class="form-check me-3 mb-2">
                    <input class="form-check-input variant-radio size-${ps.size_id}"
                        data-addons="${sataddon || ''}"
                        data-ingredients="${satingredients || ''}"
                        type="radio"
                        name="product-variant"
                        id="${id}"
                        value="${ps.variant_id}"
                        data-size-id="${ps.size_id}"
                        data-code="${sizeCode}"
                        data-price="${price}"
                        data-product_id="${currentProduct.id}"
                        ${(variant_id && size_id && ps.variant_id == variant_id && ps.size_id == size_id) ? "checked" : (index === 0 ? "checked" : "")}>
                    <label class="form-check-label sizeclss" for="${id}">
                        ${sizeCode}
                    </label>
                </div>`;
              });
              variantHtml += `</div> </div>`;
          } else {
              variantHtml = `<p class="text-muted">No sizes available</p>`;
          }
          $(".product-variants").html(variantHtml);

          // Addons / ingredients section
          if (resHtml) {
              $("#addons-ingredients-section").html(resHtml);
          }
          //console.log('Preselected size_id:', size_id);
          $(document).off("change", ".variant-radio").on("change", ".variant-radio", function() {
              let addons = $(this).attr("data-addons") || "[]";
              let ingredients = $(this).attr("data-ingredients") || "[]";

              try {
                  addons = JSON.parse(decodeURIComponent(addons));
              } catch (e) {
                  addons = [];
              }
              try {
                  ingredients = JSON.parse(decodeURIComponent(ingredients));
              } catch (e) {
                  ingredients = [];
              }


              // console.log("Addons:", addons);
              // console.log("Ingredients:", ingredients);
              loadVariantOptions($(this), {
                  addons: addons,
                  ingredients: ingredients,
                  qty: currentProduct.qty
              });

              if (!addons.length) {
                  $(".addon-checkbox").prop("checked", false);
              }

              // ✅ Reset ingredients if empty
              if (!ingredients.length) {
                  $(".ingredient-radio").prop("checked", false);
                  $(".ingredient-radio[is_default='1']").prop("checked", true);
              }
          });


      }


      function calculateTotal() {
          const totalEl = document.getElementById("product-total-price");
          if (!totalEl) return;

          let basePrice = parseFloat(totalEl.dataset.basePrice || 0);
          let qty = parseInt($("#product-qty").val()) || 1;

          let total = basePrice;

          // addons
          document.querySelectorAll(".addon-checkbox:checked").forEach(cb => {
              total += parseFloat(cb.dataset.price) || 0;
          });

          // ingredients
          document.querySelectorAll(".ingredient-radio:checked").forEach(rb => {
              total += parseFloat(rb.dataset.price) || 0;
          });

          // multiply by qty
          total = total * qty;

          totalEl.innerText = "Rs: " + total.toFixed(2);
      }

      // recalc on change
      document.addEventListener("change", function(e) {
          if (e.target.classList.contains("addon-checkbox") || e.target.classList.contains("ingredient-radio")) {
              calculateTotal();
          }
      });

      // when popup opens → recalc once so defaults show
      document.addEventListener("shown.bs.modal", function(e) {
          if (e.target.id === "yourPopupModalId") { // change with actual modal id
              calculateTotal();
          }
      });

      // optional: call once immediately if popup is already visible
      calculateTotal();



      // Product quantity buttons
      $("#increase-qty").click(function() {
          let qty = parseInt($("#product-qty").val()) + 1;
          $("#product-qty").val(qty);
          currentProduct.qty = qty;
          calculateTotal();
      });

      $("#decrease-qty").click(function() {
          let qty = parseInt($("#product-qty").val());
          if (qty > 1) {
              qty -= 1;
              $("#product-qty").val(qty);
              currentProduct.qty = qty;
              calculateTotal();
          }
      });


      //   Add into cart

      let cart = [];
      document.getElementById("add-to-cart-btn").addEventListener("click", function() {
          const productName = document.getElementById("product-name").innerText;
          const qty = parseInt(document.getElementById("product-qty").value);
          // const basePrice = parseFloat(document.getElementById("product-total-price").dataset.basePrice) || 0;
          let basePrice = 0;
          const priceEl = document.getElementById("product-total-price");

          if (priceEl && priceEl.dataset && priceEl.dataset.basePrice) {
              basePrice = parseFloat(priceEl.dataset.basePrice) || 0;
          }
          // Addons collect
          let selectedAddons = [];
          document.querySelectorAll(".addon-checkbox:checked").forEach(cb => {
              const price = parseFloat(cb.dataset.price) || 0;
              if (price > 0) { // only add if price > 0
                  selectedAddons.push({
                      id: cb.value,
                      name: cb.nextElementSibling.innerText.trim(),
                      quantity: parseInt(cb.dataset.quantity) || 1,
                      cat_id: parseInt(cb.dataset.addon_cat_id) || 0,
                      replace: parseInt(cb.dataset.replace) || 0,
                      price: price
                  });
              }
          });
          let formattedAddons = selectedAddons.map(addon => {
              return {
                  addon_id: parseInt(addon.id),
                  quantity: parseInt(addon.quantity) || 1,
                  size_id: parseInt(addon.id),
                  ing_id: parseInt(addon.id),
                  cat_id: parseInt(addon.cat_id) || 0,
                  replace: parseInt(addon.replace) || 0,
              };
          });
          // console.log(formattedAddons);


          // Ingredients collect
          let selectedIngredients = [];
          document.querySelectorAll(".ingredient-radio:checked, .ingredient-input:checked").forEach(cb => {
              const price = parseFloat(cb.dataset.price) || 0;
              //   console.log(
              //   "cb",cb
              //   )
              //   console.log(
              //   "cb.dataset.quantity",cb.dataset.quantity
              //   )

              is_default = cb.dataset.is_default ?? 0;
              console.log(is_default);
              // if (price > 0) {
              if (is_default != 1) {
                  selectedIngredients.push({
                      id: cb.value,
                      quantity: parseFloat(cb.dataset.quantity) ?? 0,
                      name: cb.nextElementSibling.innerText.trim(),
                      price: price
                  });
              }
              //  }
          });

          let removeIngredients = [];
          document.querySelectorAll(".ingredient-radio[data-is_default='1']").forEach(cb => {
              if (!cb.checked) {
                  removeIngredients.push({
                      quantity: parseFloat(cb.dataset.quantity) ?? 0,
                      size_id: 1,
                      ing_id: parseInt(cb.value)
                  });
              }
          });
          //console.log(JSON.stringify(removeIngredients));

          // Calculate total price for product (base + addons + ingredients)
          let extraPrice = selectedAddons.reduce((sum, a) => sum + a.price, 0) +
              selectedIngredients.reduce((sum, i) => sum + i.price, 0);


          let formattedIngredients = selectedIngredients.map(ingredient => {
              return {
                  addon_id: parseInt(ingredient.id),
                  quantity: parseFloat(ingredient.quantity) ?? 0,
                  size_id: parseInt(ingredient.id),
                  ing_id: parseInt(ingredient.id)
              };
          });
          // console.log(formattedIngredients);
          let price = basePrice;
          let subtotal = (price * qty) + (extraPrice * qty);
          let product_variant_id = $(".variant-radio:checked").val();
          var cartid = $('#cart-item-id').val() || '';
          var orderitemNote= $("#item-note-text-cart").val() || '';
          if (cartid != "") {
              $.ajax({
                  url: "{{ route('pos.cart.update') }}", //
                  method: "POST",
                  data: {
                      _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                      cart_id: cartid,
                      addons: JSON.stringify(formattedAddons),
                      ingredients: JSON.stringify(formattedIngredients),
                      remove_ingredients: JSON.stringify(removeIngredients),
                      product_variant_id: product_variant_id,
                      notes: orderitemNote,
                      qty: qty,
                  },
                  success: function(response) {
                      $('#cart-item-id').val('');
                      updateCartUI();
                  },
                  error: function(xhr) {
                      // console.error("Error:", xhr.responseText);
                  }
              });

          } else {
              $.ajax({
                  url: "{{ route('pos.cart.add') }}", //
                  method: "POST",
                  data: {
                      _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                      addons: JSON.stringify(formattedAddons),
                      ingredients: JSON.stringify(formattedIngredients),
                      remove_ingredients: JSON.stringify(removeIngredients),
                      product_variant_id: product_variant_id,
                      notes: orderitemNote,
                      qty: qty,
                  },
                  success: function(response) {
                      updateCartUI();
                  },
                  error: function(xhr) {
                      //console.error("Error:", xhr.responseText);
                  }
              });
          }

          if (price > 0) {
              // Push into cart
              cart.push({
                  name: productName,
                  qty: qty,
                  price: price,
                  subtotal: subtotal,
                  addons: selectedAddons,
                  ingredients: selectedIngredients
              });


              // Close modal
              bootstrap.Modal.getInstance(document.getElementById("productModal")).hide();
          }

      });

      function updateCartUI() {
          $.ajax({
              url: "{{ route('pos.cart.index') }}",
              method: "GET",
              beforeSend: function() {
                  $("#cart-loader").css("display", "flex"); // loader show
              },
              success: function(response) {

                  $("#cart-loader").css("display", "none"); // loader hide
                  // cart = response.cart;
                  $('#get-cart').html(response);
              },
              error: function(xhr) {
                  // console.error("Error:", xhr.responseText);
                  $("#cart-loader").css("display", "none"); // loader hide
              }
              // success: function(response) {
              //     // cart = response.cart;
              //     $('#get-cart').html(response);
              //     //updateCartUI();
              // }
          });

          //     const cartItems = document.getElementById("cart-items");
          //     cartItems.innerHTML = "";

          //     let subtotal = 0;

          //     cart.forEach((item, index) => {
          //         subtotal += item.subtotal;

          //         let row = `
        //     <tr>
        //         <td>
        //           ${item.name}
        //           ${item.addons.length ? `<br><small>Addons: ${item.addons.map(a => a.name).join(", ")}</small>` : ""}
        //           ${item.ingredients.length ? `<br><small>Ingredients: ${item.ingredients.map(i => i.name).join(", ")}</small>` : ""}
        //         </td>
        //         <td>${item.qty}</td>
        //         <td>Rs: ${item.price.toFixed(2)}</td>
        //         <td>Rs: ${item.subtotal.toFixed(2)}</td>
        //         <td>
        //             <button class="btn btn-sm btn-danger remove-item" data-index="${index}">
        //                 ✕
        //             </button>
        //         </td>
        //     </tr>
        // `;
          //         cartItems.insertAdjacentHTML("beforeend", row);
          //     });

          //     let tax = subtotal * 0.1; // 10% example
          //     let delivery = 200; // fixed delivery
          //     let total = subtotal + tax + delivery;

          //     document.getElementById("subtotal").innerText = "Rs: " + subtotal.toFixed(2);
          //     document.getElementById("tax").innerText = "Rs: " + tax.toFixed(2);
          //     document.getElementById("total").innerText = "Rs: " + total.toFixed(2);
          //     document.getElementById("delivery-charges").innerText = "Rs: " + delivery.toFixed(2);
      }

      $(document).on("click", ".remove-item", function() {

          const index = $(this).data("index");
          const cartId = $(this).data("cartId");

          if (cartId) {
              // Remove item from server
              $.ajax({
                  url: "{{ route('pos.cart.remove') }}",
                  method: "POST",
                  data: {
                      _token: $('meta[name="csrf-token"]').attr('content'),
                      cart_id: cartId
                  },
                  success: function(response) {
                      if (response.message) {
                          // cart = cart.filter(item => item.id !== cartId);
                          updateCartUI(); // Refresh UI
                      }
                  }
              });
          }

          // cart.splice(index, 1); // remove item from array
          // updateCartUI(); // refresh UI
      });
      $(document).on("click", "#proceed-checkout-btn", function(e) {
          $('#checkoutForm')[0].reset();
          let checkoutModal = new bootstrap.Modal($("#checkoutModal")[0]);
          checkoutModal.show();
      });
      $(document).on('click', '.tender-btn', function() {
          const value = $(this).data('value');
          const total = parseFloat($('#total-amount').val()) || 0;
          const $input = $('#amount-received');

          if (value === 'total') {
              $input.val(total);
          } else {
              $input.val(value);
          }

          $input.trigger('input');
      });

      $(document).on("click", "#print-invoice", function(e) {
          e.preventDefault();

          let invoicePreview = $("#invoice-preview-pos");
          if (invoicePreview.length === 0) {
              alert("Invoice preview is not available.");
              return;
          }

          // Clone and remove script tags
          let htmlContent = invoicePreview.clone();
          htmlContent.find("script").remove();
          htmlContent = htmlContent[0].outerHTML;

          let printWindow = window.open("", "_blank");
          printWindow.document.open();
          printWindow.document.write("<html><head><title>Invoice</title>");
          printWindow.document.write(`
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            h5 { text-align: center; }
        </style>
    `);

          printWindow.document.write(htmlContent);
          // printWindow.document.write("</body></html>");
          printWindow.document.close();


          printWindow.print();

      });
      $(document).on("click", ".increase-qty", function() {
          let index = $(this).data("index");
          let cartid = $(this).data("cart-id");
          // console.log(cartid);
          let input = $(".qty-input[data-index='" + index + "']");
          input.val(parseInt(input.val()) + 1);
          var qty = 1;
          $.ajax({
              url: "{{ route('pos.cart.updatequantity') }}",
              method: "POST",
              data: {
                  _token: $('meta[name="csrf-token"]').attr("content"),
                  cart_id: cartid,
                  qty: qty,
                  action: 'add'
              },
              success: function(response) {
                  updateCartUI();
                  // if (response.success) {
                  //     toastr.success("Quantity updated successfully!");
                  // } else {
                  //     toastr.error("Failed to update quantity!");
                  // }
              }
          });
      });

      $(document).on("click", ".decrease-qty-btn", function() {
          let index = $(this).data("index");
          let input = $(".qty-input[data-index='" + index + "']");
          let current = parseInt(input.val());
          let cartid = $(this).data("cart-id");
          var qty = 1;
          $.ajax({
              url: "{{ route('pos.cart.updatequantity') }}",
              method: "POST",
              data: {
                  _token: $('meta[name="csrf-token"]').attr("content"),
                  cart_id: cartid,
                  qty: qty,
                  action: 'remove'
              },
              success: function(response) {
                  updateCartUI();
                  // if (response.success) {
                  //     toastr.success("Quantity updated successfully!");
                  // } else {
                  //     toastr.error("Failed to update quantity!");
                  // }
              }
          });

      });
      $(document).on("click", ".edit-item", function() {
          let cartid = $(this).data("cart-id");
          let product_id = $(this).data("product-id");
          let variant_id = $(this).data("variant-id");
          let size_id = $(this).data("size_id");
          $("#cart-item-id").val(cartid);

          let addons = $(this).data("addon");
          let ingredients = JSON.parse($(this).attr("data-ingredients"));
          // console.log(addons);
          let qty = $(this).data("qty") || 1;

          $.ajax({
              url: "{{ route('pos.cart.edit') }}",
              method: "POST",
              data: {
                  _token: $('meta[name="csrf-token"]').attr("content"),
                  product_id: product_id,
                  variant_id: variant_id,
                  size_id: size_id,
                  addon: addons,
                  ingredients: ingredients,
                  cart_id: cartid,
              },
              success: function(res) {
                  let product = {
                      name: res.product.name,
                      imgSrc: res.product.image,
                      id: res.product.id,
                      productSizes: res.product.sizes || [],
                      price: parseFloat(res.product.price),
                      qty: res.product.qty || qty
                  };


                  loadProductModal(product, variant_id, addons, ingredients, size_id, res.html);


                  const $variant = $(".variant-radio[value='" + variant_id + "']");
                  $variant.prop("checked", true);

                  loadVariantOptions($variant, {
                      addons: addons,
                      ingredients: ingredients,
                      qty: product.qty
                  });


                  $("#productModal").modal("show");
                  calculateTotal();
              },
          });
      });

      $(document).ready(function() {
          $(".category-item").first().trigger("click");
      });


      $(document).on("click", ".category-item", function() {
          let selectedCategory = $(this).data("category");


          $(this).find(".category-radio").prop("checked", true);

          // Active class toggle
          $(".category-item").removeClass("active");
          $(this).addClass("active");

          // Filter products
          $(".products[data-category]").each(function() {
              const cat = $(this).data("category");
              $(this).toggleClass("d-none", cat !== selectedCategory);
          });
      });
      $('.save-item-note').click(function() {
          let cartid = $(".save-item-note-id").val();
          let note = $("#item-note-text").val();

          $.ajax({
              url: "{{ route('pos.cart.updateNote') }}",
              method: "POST",
              data: {
                  _token: $('meta[name="csrf-token"]').attr("content"),
                  cart_id: cartid,
                  note: note,
              },
              success: function(response) {
                  if (response) {
                      toastr.success("Note updated successfully!");
                      updateCartUI()
                      $("#item-note-text").val("");
                      $("#itemNoteModal").modal("hide");
                  } else {
                      toastr.error("Failed to update note!");
                  }
              }
          })

      })
      $(document).on("click", ".item-note", function() {
          let cartid = $(this).data("cart-id");
          let note = $(this).data("note");
          $(".save-item-note-id").val(cartid);
          $("#item-note-text").val(note);

          //  $("#itemNoteInput").val(note);
      });
      $(document).on("click", ".add-order-note", function() {
          let cartid = $(this).data("cart-id");
          $(".order-note-cart-id").val(cartid);
          let note = $(this).data("note");
          $("#order-note-text").text(note);
      })
      $(document).on("click", ".save-order-note", function() {
          let cartid = $(".order-note-cart-id").val();
          let note = $("#order-note-text").val();
          $.ajax({
              url: "{{ route('pos.cart.updateOrderNote') }}",
              method: "POST",
              data: {
                  _token: $('meta[name="csrf-token"]').attr("content"),
                  cart_id: cartid,
                  note: note,
              },
              success: function(response) {
                  if (response) {
                      toastr.success("Note updated successfully!");
                      updateCartUI()
                      $("#orderNoteModal").modal("hide");
                  } else {
                      toastr.error("Failed to update note!");
                  }
              }
          })
      })

      $('#checkoutForm').on('submit', function(e) {
          e.preventDefault();
          const amountReceived = parseFloat($('#amount-received').val()) || 0;
          const totalAmount = parseFloat($('#total-amount').val()) || 0;

          // Check if amount received is less than total
          if (amountReceived < totalAmount) {
              Swal.fire({
                  icon: 'error',
                  title: 'Insufficient Amount',
                  text: 'Amount received is less than total amount. Please enter full payment.'
              });
              return; // Stop further execution
          }


          Swal.fire({
              title: 'Confirm Checkout',
              text: 'Do you want to complete the payment and print receipt?',
              icon: 'question',
              showCancelButton: true,
              confirmButtonText: 'Yes, Proceed',
              cancelButtonText: 'Cancel'
          }).then((result) => {
              // loadReceipt(4);
              if (result.isConfirmed) {
                  // First complete the payment
                  completePayment()
                      .then((order_id) => {
                          // After payment success, load receipt
                          $('#checkoutModal').modal('hide');
                          loadReceipt(order_id);
                      })
                      .catch(() => {
                          toastr.error("Payment could not be completed!");
                      });
              }
          });
      });

      // =========================
      //  Complete Payment Function
      // =========================
      $(document).ready(function() {
          const maxLength = 30;

          $("#customer-name").on("keyup", function() {
              const value = $(this).val();
              if (value.length > maxLength) {
                  $(this).val(value.substring(0, maxLength));
                  $("#name-error").show();
              } else {
                  $("#name-error").hide();
              }
          });
      });

      function completePayment() {
          return new Promise((resolve, reject) => {
              let data = {
                  _token: $('meta[name="csrf-token"]').attr("content"), // CSRF token
                  customer_name: $("#customer-name").val(),
                  customer_phone: $("#customer-phone").val(),
                  customer_email: $("#customer-email").val(),
                  payment_method: $("#paymentType").val(),
                  change_return: $("#change-return").val(),
                  customer_id: $("#customer-id").val(),
                  order_type: $("input[name='order_type']:checked").val(),
                  card_number: $("#card-number").val(),



              };

              $.ajax({
                  url: "{{ route('pos.payment.complete') }}",
                  method: "POST",
                  data: data,
                  success: function(response) {
                      if (response.status == "success") {
                          updateCartUI();
                          $("#checkoutModal").modal("hide");
                          toastr.success("Payment completed successfully!");
                          $('#order-note-text').val('');
                          $('#item-note-text').val('');
                          $('#checkoutForm')[0].reset();
                          $('#paymentType').val('cash').trigger('change');
                           // Detect if device is NOT mobile/tablet
                        var isMobileOrTablet = /Android|iPhone|iPad|iPod|Tablet|Mobile/i.test(navigator.userAgent);

                        if (!isMobileOrTablet) {
                            // Desktop → redirect
                            // let url = "{{ route('staff.sticker.print', ':id') }}".replace(':id', response.order_id);
                            // window.location.href = url;
                        }
                         // loadStickers(response.order_id);
                          resolve(response.order_id); // return order ID
                      } else {
                          toastr.error("Payment failed!");
                          reject();
                      }
                  },
                  error: function(xhr) {
                      console.error(xhr.responseText);
                      toastr.error("Something went wrong!");
                      reject();
                  }
              });
          });
      }

      // =========================
      //  Load Receipt Function
      // =========================
function loadReceipt(order_id) {
    $.ajax({
        url: "{{ route('pos.cart.recipt') }}",
        method: "GET",
        data: { order_id: order_id },
        success: function(response) {

    $("#invoice-preview-pos").html("");

    let stickerUrl = "{{ route('staff.sticker.print', ':id') }}".replace(':id',order_id);

    // Bill Receipt Button
    let receiptForm = `
        <button id="btn-receipt" class="btn btn-primary btn-lg">
            <i class="bi bi-printer"></i> Bill Receipt
        </button>

        <form id="form-receipt" action="http://localhost/SimplePos/Billrecipt.php" method="POST" target="_blank" style="display:none;">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="order" value='${JSON.stringify(response.getreciptData)}'>
        </form>
    `;

    // Kitchen KOT Button
    let kotForm = `
        <button id="btn-kot" class="btn btn-warning text-white btn-lg">
            <i class="bi bi-printer-fill"></i> Kitchen KOT
        </button>

        <form id="form-kot" action="http://localhost/SimplePos/KotBill.php" method="POST" target="_blank" style="display:none;">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="order" value='${JSON.stringify(response.getKOT)}'>
        </form>
    `;

    // Sticker Button
    let stickerBtn = `
        <button id="btn-sticker" class="btn btn-success btn-lg">
            <i class="bi bi-upc"></i> Print Sticker
        </button>
    `;

    // Append
    $("#invoice-preview-pos").html(`
        <div class="d-flex flex-wrap gap-3 mt-3 justify-content-center">
            ${receiptForm}
            ${kotForm}
            ${stickerBtn}
        </div>
    `);

   // $('#posReceiptModal').modal('show');

    // -----------------------------
    // AUTO CLICK WITH DELAY LOGIC
    // -----------------------------
 var isMobileOrTablet = /Android|iPhone|iPad|iPod|Tablet|Mobile/i.test(navigator.userAgent);

     if (!isMobileOrTablet) {
    // 1️⃣ First click Bill Receipt after 1 second
    setTimeout(function () {
        $("#form-receipt").submit();
    }, 1000);

    // 2️⃣ Then click KOT after 3 seconds
    setTimeout(function () {
        $("#form-kot").submit();
    }, 3000);

    // 3️⃣ Then click Sticker after 5 seconds
    setTimeout(function () {
        window.open(stickerUrl, "_blank");
    }, 5000);
}

},
        error: function(xhr) {
            console.error(xhr.responseText);
            toastr.error("Failed to load receipt!");
        }
    });
}

        // =========================
      //  Load Stickers Function
      // =========================
        function loadStickers(id) {
          $.ajax({
              url: "{{ route('staff.sticker.print', ':id') }}".replace(':id', id),
              method: "GET",
              data: {
                  id: id,
              },
              success: function(response) {
                  $("#invoice-preview-pos").html(response);
                  // $('#posReceiptModal').modal('show');
              },
              error: function(xhr) {
                  console.error(xhr.responseText);
                  toastr.error("Failed to load receipt!");
              }
          });0
      }
      $(document).on('change', '#paymentType', function() {
          let tax = $(this).find(':selected').data('tax');
          var cart_id = $('#cart_id').val();


          if (cart_id != "") {
              $.ajax({
                  url: "{{ route('pos.cart.updateTax') }}",
                  method: "POST",
                  data: {
                      _token: "{{ csrf_token() }}",
                      tax: tax,
                      payment_type: $(this).val(),
                      cart_id: cart_id
                  },
                  beforeSend: function() {
                      $("#cart-loader").show();
                  },
                  success: function(res) {
                      //  $("#get-cart").html(res.html);
                      updateCartUI();
                  },
                  complete: function() {
                      $("#cart-loader").hide();
                  }
              })
          }
      });
      // =========================
      //  Apply Coupon Function
      // =========================
      $(document).on('click', '#apply_coupon_btn', function(e) {
          e.preventDefault();

          let couponCode = $('#coupon_code').val().trim();
          let cartId = $('#cart_id').val();
          let token = $('meta[name="csrf-token"]').attr('content');

          // Reset messages
          $('#coupon_success_msg').addClass('d-none');
          $('#coupon_error_msg').addClass('d-none');

          if (couponCode === '') {
              $('#coupon_error_msg').text('Please enter a coupon code.').removeClass('d-none');
              return;
          }

          // Disable button while applying
          let button = $(this);
          button.prop('disabled', true).html(
          '<span class="spinner-border spinner-border-sm"></span> Applying...');

          $.ajax({
              url: "{{ route('apply.coupon') }}", // 🔹 Make sure this route exists
              method: "POST",
              data: {
                  _token: token,
                  coupon_code: couponCode,
                  cart_id: cartId
              },
              success: function(response) {
                  button.prop('disabled', false).html('Apply');

                  if (response.message) {
                      $('#coupon_success_msg')
                          .text(response.message || 'Coupon applied successfully!')
                          .removeClass('d-none');

                      // ✅ Update totals dynamically
                      if (response.new_total) {
                          $('.text-success b').text('Rs: ' + parseFloat(response.new_total).toFixed(
                              2));
                      }
                      updateCartUI();
                  } else {
                      $('#coupon_error_msg')
                          .text(response.message || 'Invalid or expired coupon.')
                          .removeClass('d-none');
                  }
              },
              error: function() {
                  button.prop('disabled', false).html('Apply');
                  $('#coupon_error_msg')
                      .text('Something went wrong. Please try again.')
                      .removeClass('d-none');
              }
          });
      });
      $(document).on('click', '#remove_discount_btn', function() {
          $.ajax({
              url: '{{ route('pos.remove.coupon') }}',
              method: 'POST',
              data: {
                  _token: '{{ csrf_token() }}'
              },
              success: function(data) {
                  if (data.message) {
                      updateCartUI(); // refresh cart
                  }
              },
              error: function() {
                  alert('Failed to remove discount.');
              }
          });
      });
      $(document).on('change', '.ingredient-item input', function() {
          let $input = $(this);
          let $label = $input.siblings('label'); // current label

          if ($input.attr('type') === 'radio') {
              const groupName = $input.attr('name');
              $(`input[name="${groupName}"]`)
                  .siblings('label')
                  .removeClass('active');
              $label.addClass('active');
          } else {

              $label.toggleClass('active', $input.is(':checked'));
          }
      });
      $(document).on('change', '.addon-item input', function() {
          let $input = $(this);
          let $label = $input.siblings('label');
          const type = $input.attr('type');

          if (type === 'radio') {

              const groupName = $input.attr('name');

              $(`.addon-item input[name="${groupName}"]`)
                  .siblings('label')
                  .removeClass('active');

              $label.addClass('active');
          } else if (type === 'checkbox') {

              if ($input.is(':checked')) {
                  $label.addClass('active');
              } else {
                  $label.removeClass('active');
              }
          }
      });
      let isSyncing = false;

      $(document).on('change', '.addon-checkbox', function() {
          if (isSyncing) return; // recursion stop
          isSyncing = true;

          const ingId = $(this).val();
          const isChecked = $(this).is(':checked');
          const categoryId = $(this).data('category-id');
          const isReplace = $(this).data("replace");


          if (isChecked) {
              const others = $(`.addon-checkbox[data-category-id='${categoryId}']`).not(this);

              others.prop('checked', false);


              others.next('label').removeClass('active');



              // $(`.addon-checkbox[data-category-id='${categoryId}']`).not(this)
              //     .each(function() {
              //         const otherIngId = $(this).val();
              //         $(`.ingredient-${otherIngId}`).prop('checked', false);
              //         $(`.ingredient-${otherIngId}`).closest('.ingredient-item').removeClass('active');
              //          $(`.ingredient-${otherIngId}`).closest('label').removeClass('active');
              //     });

          }


          $(`.ingredient-${ingId}`).prop('checked', isChecked);

          if (isChecked) {
              $(`.ingredient-${ingId}`).closest('.ingredient-item').addClass('active');
          } else {
              $(`.ingredient-${ingId}`).closest('.ingredient-item').removeClass('active');
          }

          isSyncing = false;
      });
  </script>
  <!--End dashboard scripts--->
  <!-- order scripts -->
  <script>
      $(document).on("click", ".update-status", function(e) {
          e.preventDefault();

          let button = $(this);
          let url = button.data("url");
          let status = button.data("status");
          let token = $("meta[name='csrf-token']").attr("content");

          // Order ID from parent cell or row
          let orderId = button.closest("tr").data("id");
          let actionCell = $("#action-cell-" + orderId);

          button.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span>');

          $.ajax({
              url: url,
              type: "POST",
              data: {
                  _token: token,
                  status: status
              },
              success: function(response) {
                  if (response) {
                      if (status === "processing") {
                          loadReceipt(orderId); // All print data
                          fetchOrderscounter() // pending order Count
                          actionCell.html(
                              '<span class="status-badge status-processing">Processing</span>');
                      } else if (status === "cancelled") {
                          actionCell.html(
                              '<span class="status-badge status-cancelled">Cancelled</span>');
                      } else if (status === "completed") {
                          actionCell.html(
                              '<span class="status-badge status-completed">Completed</span>');
                      } else if (status === "paid") {
                          actionCell.html('<span class="status-badge status-paid">Paid</span>');
                      }
                  }
              },
              error: function(xhr) {
                  button.prop("disabled", false).text(status.charAt(0).toUpperCase() + status.slice(
                      1));
              }
          });
      });


      function reloadOrders() {
          let reloadBtn = $("#reloadBtn");
          let reloadIcon = $("#reloadIcon");

          // Add spinning effect
          reloadIcon.addClass("fa-spin");

          $.ajax({
              url: "{{ route('pos.orders.table') }}",
              type: "GET",
              success: function(response) {

                  $("#ordersTable tbody").html(response);

                  // Stop spinning
                  reloadIcon.removeClass("fa-spin");
              },
              error: function() {
                  alert("Failed to reload orders");
                  reloadIcon.removeClass("fa-spin");
              }
          });
      }

      // Click event
      $(document).on("click", "#reloadBtn", function() {
          reloadOrders();
      });

      $(document).on("change keyup", "#orderFilterForm input, #orderFilterForm select", function() {
          //  fetchOrders();
      });

      function fetchOrders() {
          let data = $("#orderFilterForm").serialize(); // sab filters ka data

          $.ajax({
              url: "{{ route('pos.orders.table') }}", // yeh ek route banani hogi
              type: "GET",
              data: data,
              beforeSend: function() {
                  $("#ordersTable tbody").html(
                      '<tr><td colspan="12" class="text-center">Loading...</td></tr>');
              },
              success: function(response) {
                  $("#ordersTable tbody").html(response); // partial view reload
              },
              error: function() {
                  alert("Something went wrong!");
              }
          });
      }

      function fetchOrderscounter() {
        console.log("Fetching orders counter...");
          $.ajax({
              url: "{{ route('pos.orders.counter') }}", // yeh ek route banani hogi
              type: "GET",
              success: function(response) {
                  $(".countnum").html(response.count); // partial view reload
              },
              error: function() {
                //  alert("Something went wrong!");
              }
          });
      }


  $(document).ready(function() {
fetchOrderscounter();

      // Auto reload har 60s

  });
       //setInterval(reloadOrders, 60000);
      setInterval(fetchOrderscounter, 10000);
  </script>
  <!--End order scripts --->
  <div class="modal fade" id="cashCountModal" tabindex="-1" aria-labelledby="cashCountModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="cashCountModalLabel">Cash Count Summary</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" id="cashCountContent">
                  <div class="text-center text-muted py-4">Loading...</div>
              </div>
          </div>
      </div>
  </div>
  <div class="modal fade" id="stockCountModal" tabindex="-1" aria-labelledby="stockCountLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header bg-light">
                  <h5 class="modal-title fw-bold" id="stockCountLabel">Stock Count Summary</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body" id="stockCountBody">
                  <div class="text-center py-4">
                      <div class="spinner-border text-warning" role="status"></div>
                      <p class="mt-2 text-muted">Loading...</p>
                  </div>
              </div>
          </div>
      </div>
  </div>


  <script>
      document.getElementById('logoutBtn').addEventListener('click', function() {
          if (confirm('Are you sure you want to logout?')) {
              document.getElementById('logoutForm').submit();
          }
      });
  </script>
  <script>
      const shiftStartTime = new Date("{{ getShiftStartTime() ?? now() }}").getTime();

      // function updateShiftTimer() {
      //     const now = new Date().getTime();
      //     const diff = now - shiftStartTime;

      //     const hours = Math.floor(diff / (1000 * 60 * 60));
      //     const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      //     const seconds = Math.floor((diff % (1000 * 60)) / 1000);

      //     document.getElementById("shiftTimer").textContent =
      //         `⏱ ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
      // }
      function updateShiftTimer() {
          const now = new Date().getTime();
          const diff = now - shiftStartTime;

          const hours = Math.floor(diff / (1000 * 60 * 60));
          const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
          const seconds = Math.floor((diff % (1000 * 60)) / 1000);

          const shiftTimerEl = document.getElementById("shiftTimer");
          if (!shiftTimerEl) return; //  prevents error if element is missing

          shiftTimerEl.textContent =
              `⏱ ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
      }
      setInterval(updateShiftTimer, 1000);

      $(document).ready(function() {

          $(document).on('click', '#cashCountLink', function() {
              $('#cashCountModal').modal('show');
              $('#cashCountContent').html('<div class="text-center text-muted py-4">Loading...</div>');

              $.ajax({
                  url: "{{ route('staff.cash-count') }}", // 👈 backend route
                  type: "GET",
                  success: function(response) {
                      $('#cashCountContent').html(response.html);
                  },
                  error: function() {
                      $('#cashCountContent').html(
                          '<div class="text-danger text-center">Failed to load data.</div>'
                      );
                  }
              });
          });

          $(document).on('click', '#stockCountLink', function() {
              $('#stockCountModal').modal('show');
              $('#stockCountBody').html('<div class="text-center text-muted py-4">Loading...</div>');

              $.ajax({
                  url: "{{ route('staff.stock-count') }}", // 👈 backend route
                  type: "GET",
                  success: function(response) {
                      $('#stockCountBody').html(response);
                  },
                  error: function() {
                      $('#stockCountBody').html(
                          '<div class="text-danger text-center">Failed to load data.</div>'
                      );
                  }
              });
          });
      });
      $(document).ready(function() {

          $('#checkoutModal').on('shown.bs.modal', function() {
              const hiddenTotal = $('#get-final-total').val();
              const $totalAmount = $('#total-amount');
              const $amountReceived = $('#amount-received');
              const $changeReturn = $('#change-return');



              $totalAmount.val(hiddenTotal);
              const totalAmount = parseFloat($('#total-amount').val()) || 0;
              $('.sameamount').text(totalAmount.toFixed(2));

              payment_method = $("#paymentType").val()
              if (payment_method == "card") {
                  setTimeout(function() {
                      $('#card-number-container').show();
                      $('.sameamount').trigger('click');
                  }, 200);
              }else {
                  $('#card-number-container').hide();
              }
              $amountReceived.on('input', function() {
                  const received = parseFloat($(this).val()) || 0;
                  const total = parseFloat(hiddenTotal) || 0;
                  const change = received - total;
                  $changeReturn.val(change > 0 ? change.toFixed(2) : 0);
              });
          });
      });
      $(document).ready(function() {
          $('#customer-phone').on('keyup', function() {
              let query = $(this).val();
              if (query.length >= 3) {
                  $.ajax({
                      url: "{{ route('pos.customers.search') }}",
                      type: "GET",
                      data: {
                          query: query
                      },
                      success: function(data) {
                          let suggestionBox = $('#customer-suggestions');
                          suggestionBox.empty();

                          if (data.length > 0) {
                              $.each(data, function(i, customer) {
                                  suggestionBox.append(
                                      `<a href="#" class="list-group-item list-group-item-action customer-item"
                                   data-id="${customer.id || ''}"
                                   data-name="${customer.first_name} ${customer.last_name || ''}"
                                   data-email="${customer.email || ''}"
                                   data-phone="${customer.phone || ''}">
                                   ${customer.phone || ''} - ${customer.first_name} ${customer.last_name || ''}
                                 </a>`
                                  );
                              });
                              suggestionBox.show();
                          } else {
                              $('#customer-id').val("");
                              suggestionBox.hide();
                          }
                      }
                  });
              } else {
                  $('#customer-suggestions').hide();
              }
          });

          // On selecting customer from suggestion list
          $(document).on('click', '.customer-item', function(e) {
              e.preventDefault();
              let name = $(this).data('name');
              let email = $(this).data('email');
              let phone = $(this).data('phone');
              let id = $(this).data('id');

              $('#customer-name').val(name);
              $('#customer-email').val(email);
              $('#customer-phone').val(phone);
              $('#customer-id').val(id);
              $('#customer-suggestions').hide();
          });

          // Hide suggestions if user clicks outside
          $(document).click(function(e) {
              if (!$(e.target).closest('#customer-phone, #customer-suggestions').length) {
                  $('#customer-suggestions').hide();
              }
          });
      });
      //  Order Cancel Modal Script
      $(document).on('click', '.btn-cancel', function() {
          let orderId = $(this).data('order-id');
          let url = $(this).data('url');

          $('#cancel-order-id').val(orderId);
          $('#cancel-order-url').val(url);
          $('#cancel-reason').val('');

          $('#cancelReasonModal').modal('show');
      });

      $(document).ready(function() {
          $(document).on('submit', '#cancelReasonForm', function(e) {
              e.preventDefault();

              let orderId = $('#cancel-order-id').val();
              let url = $('#cancel-order-url').val();
              let reason = $('#cancel-reason').val();
              let token = $('meta[name="csrf-token"]').attr('content');

              if (!reason.trim()) {
                  toastr.warning('Please provide a cancellation reason.');
                  return;
              }

              $.ajax({
                  url: url,
                  method: 'POST',
                  data: {
                      _token: token,
                      status: 'cancelled',
                      reason: reason
                  },
                  success: function(response) {
                      $('#cancelReasonModal').modal('hide');
                      $('#action-cell-' + orderId).html(
                          '<span class="status-badge status-cancelled">Cancelled</span>'
                      );
                      $("#reloadBtn").click();
                      toastr.success('Order cancelled successfully');
                  },
                  error: function(xhr) {
                      toastr.error('Failed to cancel order');
                  }
              });
          });
      });
  </script>
