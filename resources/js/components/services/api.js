import axios from "axios"

// Configure axios defaults
axios.defaults.baseURL = "/api"
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest"

// Add auth token to requests
axios.interceptors.request.use((config) => {
    const token = localStorage.getItem("auth_token")
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
})

// Handle auth errors
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem("auth_token")
            localStorage.removeItem("user")
            window.location.href = "/login"
        }
        return Promise.reject(error)
    },
)

export const authApi = {
    login: (credentials) => axios.post("/login", credentials),
    register: (userData) => axios.post("/register", userData),
    logout: () => axios.post("/logout"),
    me: () => axios.get("/me"),
}

export const devicesApi = {
    getAll: (params = {}) => axios.get("/devices", { params }).then((res) => res.data),
    getById: (id) => axios.get(`/devices/${id}`).then((res) => res.data),
    create: (data) => axios.post("/devices", data).then((res) => res.data),
    update: (id, data) => axios.put(`/devices/${id}`, data).then((res) => res.data),
    delete: (id) => axios.delete(`/devices/${id}`).then((res) => res.data),
}

export const dashboardApi = {
    getStats: () => axios.get("/dashboard").then((res) => res.data),
}

export const searchApi = {
    searchDevice: (imei) => axios.post("/search", { imei }).then((res) => res.data),
}

export const profileApi = {
    getProfile: () => axios.get("/profile").then((res) => res.data),
    updateProfile: (data) => axios.put("/profile", data).then((res) => res.data),
    updatePassword: (data) => axios.put("/profile/password", data).then((res) => res.data),
    uploadAvatar: (file) => {
        const formData = new FormData()
        formData.append("avatar", file)
        return axios
            .post("/profile/avatar", formData, {
                headers: { "Content-Type": "multipart/form-data" },
            })
            .then((res) => res.data)
    },
    deleteAccount: () => axios.delete("/profile").then((res) => res.data),
    getStats: () => axios.get("/profile/stats").then((res) => res.data),
}

export default {
    auth: authApi,
    devices: devicesApi,
    dashboard: dashboardApi,
    search: searchApi,
    profile: profileApi,
}
