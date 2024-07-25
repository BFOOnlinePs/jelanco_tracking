import {createBrowserRouter, Navigate} from "react-router-dom";
import Login from "./views/auth/Login.jsx";
import Signup from "./views/auth/Signup.jsx";
import NotFound from "./views/NotFound";
import DefaultLayout from "./components/DefaultLayout";
import GuestLayout from "./components/GuestLayout";
import Dashboard from "./views/Dashboard";
import Users from "./views/users/Users.jsx";
import AddUsers from "./views/users/AddUsers.jsx";
import Authorization from "./views/users/Authorization.jsx";
import AddRoles from "./views/users/AddRoles.jsx";
import EditRoles from "./views/users/EditRoles.jsx";

const router = createBrowserRouter([
    {
        path : '/',
        element : <DefaultLayout />,
        children: [
            {
                path:'/home',
                element: <Dashboard/>
            },
            {
                path:'/users',
                element: <Users/>
            },
            {
                path:'/add_users',
                element: <AddUsers/>
            },
            {
                path:'/authorization',
                element: <Authorization/>
            },
            {
                path:'/add_roles',
                element: <AddRoles/>
            },
            {
                path:'/edit_roles/:id',
                element: <EditRoles/>
            }
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
