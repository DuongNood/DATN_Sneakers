import { createContext, useContext, useState } from 'react'

interface AuthContextType {
  user: any
  login: (userData: any) => void
  logout: () => void
}

const AuthContext = createContext<AuthContextType | undefined>(undefined)

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
  const [user, setUser] = useState<any>(null)

  const login = (userData: any) => {
    setUser(userData)
    localStorage.setItem('user', JSON.stringify(userData)) // Lưu vào localStorage
  }

  const logout = () => {
    setUser(null)
    localStorage.removeItem('user')
  }

  return <AuthContext.Provider value={{ user, login, logout }}>{children}</AuthContext.Provider>
}

// Hook để dùng AuthContext
export const useAuth = () => {
  const context = useContext(AuthContext)
  if (!context) {
    throw new Error('useAuth phải được sử dụng bên trong AuthProvider')
  }
  return context
}
