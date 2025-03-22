import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api'

// Hàm đăng ký
export const registerUser = async (userData: { name: string; email: string; password: string }) => {
  try {
    const response = await axios.post(`${API_URL}/register`, userData)
    return response.data
  } catch (error) {
    console.error('Lỗi đăng ký con mẹ nó rồi:', error)
    throw error
  }
}

// Hàm đăng nhập
export const loginUser = async (userData: { email: string; password: string }) => {
  try {
    const response = await axios.post(`${API_URL}/login`, userData)
    return response.data // Thường sẽ trả về token
  } catch (error) {
    console.error('Lỗi đăng nhập con mẹ nó rồi:', error)
    throw error
  }
}
