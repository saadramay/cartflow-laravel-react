import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import axios from 'axios';
import toast, { Toaster } from 'react-hot-toast';

export default function Cart({ auth, cartItems: initialCartItems, total: initialTotal }) {
    const [cartItems, setCartItems] = useState(initialCartItems);
    const [total, setTotal] = useState(initialTotal);
    const [loading, setLoading] = useState(false);
    const [updatingItems, setUpdatingItems] = useState({});

    const fetchCart = async () => {
        try {
            const response = await axios.get('/api/cart');
            setCartItems(response.data.cartItems);
            setTotal(response.data.total);

            // Trigger cart count update
            window.dispatchEvent(new Event('cart-updated'));
        } catch (error) {
            console.error('Error fetching cart:', error);
        }
    };

    const updateQuantity = async (cartItemId, newQuantity) => {
        if (newQuantity < 1) return;

        setUpdatingItems(prev => ({ ...prev, [cartItemId]: true }));

        try {
            await axios.patch(`/api/cart/${cartItemId}`, {
                quantity: newQuantity
            });
            await fetchCart();
            toast.success('Cart updated', { duration: 1500, position: 'bottom-right' });
        } catch (error) {
            toast.error(error.response?.data?.message || 'Error updating cart', { position: 'bottom-right' });
        } finally {
            setUpdatingItems(prev => ({ ...prev, [cartItemId]: false }));
        }
    };

    const removeItem = async (cartItemId) => {
        if (!confirm('Remove this item from cart?')) return;

        setUpdatingItems(prev => ({ ...prev, [cartItemId]: true }));

        try {
            await axios.delete(`/api/cart/${cartItemId}`);
            await fetchCart();
            toast.success('Item removed', { duration: 2000, position: 'bottom-right' });
        } catch (error) {
            toast.error('Error removing item', { position: 'bottom-right' });
            setUpdatingItems(prev => ({ ...prev, [cartItemId]: false }));
        }
    };

    const handleCheckout = async () => {
        if (cartItems.length === 0) {
            toast.error('Cart is empty', { position: 'bottom-right' });
            return;
        }

        setLoading(true);

        try {
            await axios.post('/api/checkout');
            toast.success('🎉 Order placed successfully!', { duration: 4000, position: 'bottom-right' });
            await fetchCart();
        } catch (error) {
            toast.error(error.response?.data?.message || 'Checkout failed', { position: 'bottom-right' });
        } finally {
            setLoading(false);
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Shopping Cart</h2>}
        >
            <Head title="Cart" />
            <Toaster />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {cartItems.length === 0 ? (
                        <div className="bg-white overflow-hidden shadow-sm rounded-lg p-12 text-center">
                            <svg className="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 className="text-xl font-semibold text-gray-700 mb-2">Your cart is empty</h3>
                            <p className="text-gray-500 mb-6">Add some products to get started!</p>
                            <Link
                                href="/products"
                                className="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition"
                            >
                                Browse Products
                            </Link>
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            {/* Cart Items */}
                            <div className="lg:col-span-2 space-y-4">
                                {cartItems.map((item) => (
                                    <div key={item.id} className={`bg-white overflow-hidden shadow-sm rounded-lg p-6 transition-opacity ${updatingItems[item.id] ? 'opacity-50' : 'opacity-100'}`}>
                                        <div className="flex gap-6">
                                            {/* Product Image Placeholder */}
                                            <div className="w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-200 rounded flex-shrink-0 flex items-center justify-center">
                                                <svg className="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>

                                            {/* Product Details */}
                                            <div className="flex-1">
                                                <h3 className="font-bold text-lg mb-1">{item.product.name}</h3>
                                                <p className="text-gray-600 text-sm mb-3">{item.product.description}</p>
                                                <p className="text-xl font-bold text-gray-900">
                                                    ${parseFloat(item.product.price).toFixed(2)}
                                                </p>
                                                <p className="text-sm text-gray-500">
                                                    Available: {item.product.stock_quantity}
                                                </p>
                                            </div>

                                            {/* Quantity Controls */}
                                            <div className="flex flex-col items-end gap-3">
                                                <div className="flex items-center gap-2 border border-gray-300 rounded-lg">
                                                    <button
                                                        onClick={() => updateQuantity(item.id, item.quantity - 1)}
                                                        disabled={updatingItems[item.id] || item.quantity <= 1}
                                                        className="px-3 py-2 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition"
                                                    >
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20 12H4" />
                                                        </svg>
                                                    </button>
                                                    <span className="px-4 py-2 font-semibold min-w-[3rem] text-center">{item.quantity}</span>
                                                    <button
                                                        onClick={() => updateQuantity(item.id, item.quantity + 1)}
                                                        disabled={updatingItems[item.id] || item.quantity >= item.product.stock_quantity}
                                                        className="px-3 py-2 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition"
                                                    >
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div className="text-right">
                                                    <p className="text-sm text-gray-500">Subtotal:</p>
                                                    <p className="text-xl font-bold">
                                                        ${(item.quantity * parseFloat(item.product.price)).toFixed(2)}
                                                    </p>
                                                </div>

                                                <button
                                                    onClick={() => removeItem(item.id)}
                                                    disabled={updatingItems[item.id]}
                                                    className="text-red-600 hover:text-red-800 text-sm font-semibold flex items-center gap-1 disabled:opacity-50"
                                                >
                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {/* Order Summary */}
                            <div className="lg:col-span-1">
                                <div className="bg-white overflow-hidden shadow-sm rounded-lg p-6 sticky top-6">
                                    <h3 className="font-bold text-xl mb-6">Order Summary</h3>

                                    <div className="space-y-3 mb-6">
                                        <div className="flex justify-between text-gray-600">
                                            <span>Items ({cartItems.reduce((sum, item) => sum + item.quantity, 0)})</span>
                                            <span>${parseFloat(total).toFixed(2)}</span>
                                        </div>
                                        <div className="flex justify-between text-gray-600">
                                            <span>Shipping</span>
                                            <span className="text-green-600 font-semibold">Free</span>
                                        </div>
                                        <div className="border-t pt-3 flex justify-between text-xl font-bold">
                                            <span>Total</span>
                                            <span>${parseFloat(total).toFixed(2)}</span>
                                        </div>
                                    </div>

                                    <button
                                        onClick={handleCheckout}
                                        disabled={loading || cartItems.length === 0}
                                        className="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                    >
                                        {loading ? (
                                            <>
                                                <svg className="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Processing...
                                            </>
                                        ) : (
                                            <>
                                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                                Proceed to Checkout
                                            </>
                                        )}
                                    </button>

                                    <Link
                                        href="/products"
                                        className="block text-center text-blue-600 hover:text-blue-800 mt-4 font-semibold"
                                    >
                                        ← Continue Shopping
                                    </Link>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}