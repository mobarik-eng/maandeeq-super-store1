document.addEventListener('DOMContentLoaded', function() {
    
    // POS System Logic
    const addItemBtn = document.getElementById('add-item-btn');
    if (addItemBtn) {
        addItemBtn.addEventListener('click', function() {
            const productSelect = document.getElementById('product-select');
            const quantityInput = document.getElementById('quantity-input');
            const cartTable = document.getElementById('cart-table-body');
            const totalDisplay = document.getElementById('total-amount-display');
            const totalInput = document.getElementById('total-amount-input');

            const productId = productSelect.value;
            const productName = productSelect.options[productSelect.selectedIndex].text;
            const price = parseFloat(productSelect.options[productSelect.selectedIndex].dataset.price);
            const stock = parseInt(productSelect.options[productSelect.selectedIndex].dataset.stock);
            const quantity = parseInt(quantityInput.value);

            if (!productId) {
                alert('Please select a product.');
                return;
            }

            if (quantity <= 0) {
                alert('Quantity must be greater than 0.');
                return;
            }

            if (quantity > stock) {
                alert('Insufficient stock. Available: ' + stock);
                return;
            }

            // Check if item already exists in cart
            let existingRow = document.getElementById('row-' + productId);
            if(existingRow) {
                alert('Item is already in the cart. Remove it to change quantity.');
                return;
            }

            const rowTotal = price * quantity;

            const row = `
                <tr id="row-${productId}">
                    <td>${productName} <input type="hidden" name="products[]" value="${productId}"></td>
                    <td>$${price.toFixed(2)}</td>
                    <td>${quantity} <input type="hidden" name="quantities[]" value="${quantity}"></td>
                    <td>$${rowTotal.toFixed(2)}</td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow('${productId}', ${rowTotal})">Remove</button></td>
                </tr>
            `;

            cartTable.insertAdjacentHTML('beforeend', row);

            // Update Total
            let currentTotal = parseFloat(totalInput.value);
            currentTotal += rowTotal;
            totalInput.value = currentTotal.toFixed(2);
            totalDisplay.innerText = '$' + currentTotal.toFixed(2);

            // Reset inputs
            productSelect.selectedIndex = 0;
            quantityInput.value = 1;
        });
    }
});

function removeRow(productId, rowTotal) {
    const row = document.getElementById('row-' + productId);
    row.remove();

    const totalDisplay = document.getElementById('total-amount-display');
    const totalInput = document.getElementById('total-amount-input');

    let currentTotal = parseFloat(totalInput.value);
    currentTotal -= rowTotal;
    totalInput.value = currentTotal.toFixed(2);
    totalDisplay.innerText = '$' + currentTotal.toFixed(2);
}
