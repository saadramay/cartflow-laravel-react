import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import axios from 'axios';
import toast, { Toaster } from 'react-hot-toast';

export default function Products({ auth, products: initialProducts }) {
    const [products, setProducts] = useState(initialProducts);
    const [loadingProducts, setLoadingProducts] = useState({});

    const addToCart = async (productId) => {
        // Set loading for specific product
        setLoadingProducts(prev => ({ ...prev, [productId]: true }));
        
        try {
            await axios.post('/api/cart', {
                product_id: productId,
                quantity: 1
            });
            
            toast.success('Added to cart!', {
                duration: 2000,
                position: 'bottom-right',
            });

            // Trigger cart count update in parent
            window.dispatchEvent(new Event('cart-updated'));
            
        } catch (error) {
            toast.error(error.response?.data?.message || 'Error adding to cart', {
                duration: 3000,
                position: 'bottom-right',
            });
        } finally {
            setLoadingProducts(prev => ({ ...prev, [productId]: false }));
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Products</h2>}
        >
            <Head title="Products" />
            <Toaster />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        {products.map((product) => (
                            <div key={product.id} className="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-lg transition-shadow">
                                <div className="p-6">
                                    {/* Product Image Placeholder */}
                                    <div className="w-full h-48 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg mb-4 flex items-center justify-center">
                                        <svg className="w-20 h-20 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>

                                    <h3 className="font-bold text-lg mb-2 text-gray-900">{product.name}</h3>
                                    <p className="text-gray-600 text-sm mb-4 line-clamp-2">{product.description}</p>
                                    
                                    <div className="flex justify-between items-center mb-4">
                                        <span className="text-2xl font-bold text-gray-900">
                                            ${parseFloat(product.price).toFixed(2)}
                                        </span>
                                        <span className={`text-sm px-2 py-1 rounded ${
                                            product.stock_quantity === 0 
                                                ? 'bg-red-100 text-red-700' 
                                                : product.stock_quantity <= 5 
                                                ? 'bg-yellow-100 text-yellow-700' 
                                                : 'bg-green-100 text-green-700'
                                        }`}>
                                            {product.stock_quantity} in stock
                                        </span>
                                    </div>

                                    {product.stock_quantity <= 5 && product.stock_quantity > 0 && (
                                        <div className="mb-3 text-xs bg-yellow-50 border border-yellow-200 text-yellow-800 px-2 py-1 rounded">
                                            ⚠️ Low stock - Order soon!
                                        </div>
                                    )}

                                    <button
                                        onClick={() => addToCart(product.id)}
                                        disabled={loadingProducts[product.id] || product.stock_quantity === 0}
                                        className={`w-full py-2.5 px-4 rounded-lg font-semibold transition-all flex items-center justify-center gap-2 ${
                                            product.stock_quantity === 0
                                                ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                                : loadingProducts[product.id]
                                                ? 'bg-blue-400 text-white cursor-wait'
                                                : 'bg-blue-600 text-white hover:bg-blue-700 active:scale-95'
                                        }`}
                                    >
                                        {loadingProducts[product.id] ? (
                                            <>
                                                <svg className="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Adding...
                                            </>
                                        ) : product.stock_quantity === 0 ? (
                                            'Out of Stock'
                                        ) : (
                                            <>
                                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Add to Cart
                                            </>
                                        )}
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}