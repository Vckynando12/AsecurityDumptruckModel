<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SMARTCAB</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-center mb-8">Login</h2>
            
            <form id="loginForm" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Login
                    </button>
                </div>

                <!-- Tambahkan link lupa password -->
                <div class="text-center mt-4">
                    <button onclick="openResetModal()" class="text-sm text-blue-600 hover:text-blue-500">
                        Lupa Password?
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Reset Password -->
    <div id="resetModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Reset Password</h3>
                <div class="mt-2 px-7 py-3">
                    <form id="resetPasswordForm" class="space-y-4">
                        @csrf
                        <div>
                            <label for="resetEmail" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                            <input type="email" id="resetEmail" name="email" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="flex justify-between mt-4">
                            <button type="button" onclick="closeResetModal()"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Kirim Link Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                console.log('Attempting login...');
                const response = await fetch('/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();
                console.log('Server response:', data);

                if (data.status === 'success') {
                    console.log('Login successful');
                    console.log('Redirecting to:', data.redirect);
                    window.location.replace(data.redirect);
                } else {
                    console.error('Login failed:', data.message);
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error during login:', error);
                alert('Terjadi kesalahan saat login');
            }
        });

        // Fungsi untuk membuka modal
        function openResetModal() {
            document.getElementById('resetModal').classList.remove('hidden');
        }

        // Fungsi untuk menutup modal
        function closeResetModal() {
            document.getElementById('resetModal').classList.add('hidden');
        }

        // Handle form reset password
        document.getElementById('resetPasswordForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('resetEmail').value;

            try {
                const response = await fetch('/forgot-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email })
                });

                // Cek jika response tidak OK
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(errorText || 'Gagal mengirim link reset password');
                }

                // Coba parse JSON hanya jika content-type adalah application/json
                const contentType = response.headers.get('content-type');
                let data = {};
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                }

                alert('Link reset password telah dikirim ke email Anda!');
                closeResetModal();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message);
            }
        });
    </script>
</body>
</html>