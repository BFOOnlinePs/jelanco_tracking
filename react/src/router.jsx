import {createBrowserRouter, Navigate} from "react-router-dom";
import Login from "./views/auth/Login.jsx";
import Signup from "./views/auth/Signup.jsx";
import NotFound from "./views/NotFound";
import DefaultLayout from "./components/DefaultLayout";
import GuestLayout from "./components/GuestLayout";
import Dashboard from "./views/Dashboard";

const router = createBrowserRouter([
    {
        path : '/',
        element : <DefaultLayout />,
        children: [

        ]
    },
    {
        path : '/',
        element : <GuestLayout />,
        children: [
            {
                path:'/login',
                element:<Login/>
            },
            {
                path:'/signup',
                element:<Signup/>
            },
        ]
    },
    {
        path:'*',
        element:<NotFound/>
    },
]);

export default router;
