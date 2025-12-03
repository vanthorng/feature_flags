import axios from "axios";

const api = axios.create({
  // Laravel backend URL
  baseURL: "http://127.0.0.1:8000",
  withCredentials: true,
});

export default api;
