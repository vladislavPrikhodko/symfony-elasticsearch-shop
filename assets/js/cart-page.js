document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.cart-row').forEach(row => {

        const id = row.dataset.id;

        const qtyValue = row.querySelector('.qty-value');

        row.querySelector('.qty-plus').addEventListener('click', () => {
            updateQty(id, parseInt(qtyValue.innerText) + 1);
        });

        row.querySelector('.qty-minus').addEventListener('click', () => {
            updateQty(id, parseInt(qtyValue.innerText) - 1);
        });

        row.querySelector('.remove-item').addEventListener('click', () => {
            removeItem(id, row);
        });

    });

});

async function updateQty(id, quantity)
{
    const response = await fetch('/cart/update/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'quantity=' + quantity
    });

    const data = await response.json();

    location.reload();
}

async function removeItem(id, row)
{
    const response = await fetch('/cart/remove/' + id, {
        method: 'POST'
    });

    const data = await response.json();

    row.remove();

    document.getElementById('cart-total').innerText =
        data.cartTotal;

    document.getElementById('cart-count').innerText =
        data.cartCount;
}