document.addEventListener('DOMContentLoaded', () => {

    //document.querySelectorAll('.add-to-cart').forEach(btn => {

    document.addEventListener('click', async (e) => {

        var btn = e.target.closest('.add-to-cart');

        if (!btn) {
            return;
        }

        e.preventDefault();

        if (btn.disabled) {
            return;
        }

        btn.disabled = true;

        var originalText = btn.innerText;

        btn.innerText = 'Додавання...';

        try {

            var id = btn.dataset.id;

            var response = await fetch('/cart/add/' + id, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            var data = await response.json();

            if (data.success) {

                updateCartCount(data.count);

                showToast();

            }

        } catch (e) {

            console.error(e);

        } finally {

            btn.disabled = false;

            btn.innerText = originalText;

        }

    });

        

    //});

    document.querySelector('.cart-wrapper').addEventListener('mouseenter', () => {
        document.getElementById('mini-cart').classList.remove('hidden');
        loadMiniCart();
    });

    document.querySelector('.cart-wrapper').addEventListener('mouseleave', () => {
        document.getElementById('mini-cart').classList.add('hidden');
    });

});

function updateCartCount(count)
{
    var el = document.getElementById('cart-count');

    
    if (!el) {
        return;
    }

    el.textContent = count;

    el.classList.remove('cart-bump');

    void el.offsetWidth;

    el.classList.add('cart-bump');
}

function showToast()
{
    var toast = document.getElementById('toast');

    if (!toast) {
        return;
    }

    toast.classList.add('show');

    setTimeout(() => {
        toast.classList.remove('show');
    }, 2500);
}

async function loadMiniCart()
{
    var response = await fetch('/cart/mini');
    var data = await response.json();

    var container = document.getElementById('mini-cart');

    let html = '';

    data.items.forEach(item => {
        html += `
            <div>
                ${item.name} x${item.quantity} - ${item.total}₴
            </div>
        `;
    });

    html += `<hr>Total: ${data.total}₴`;

    container.innerHTML = html;
}