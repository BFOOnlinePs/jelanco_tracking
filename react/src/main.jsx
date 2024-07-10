import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'
import './index.css'
import {ContextProvider} from "./contexts/ContextProvider.jsx";
import {TitleProvider} from "./contexts/TitleContext.jsx";
import router from "./router.jsx";
import Router from "./router.jsx";
import {RouterProvider} from "react-router-dom";

ReactDOM.createRoot(document.getElementById('root')).render(
    <React.StrictMode>
        <ContextProvider>
            <TitleProvider>
                <RouterProvider router={router} />
            </TitleProvider>
        </ContextProvider>
    </React.StrictMode>,
)
