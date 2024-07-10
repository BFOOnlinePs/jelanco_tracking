import {Link, Navigate, Outlet} from "react-router-dom";
import {useStateContext} from "../contexts/ContextProvider.jsx";
import {useEffect} from "react";
import axiosClient from "../axios-client.js";
import Sidebar from "./Sidebar.jsx";
import Navbar from "./Navbar.jsx";
import Content from "./Content.jsx";
import Footer from "./Footer.jsx";

export default function DefaultLayout(){
    const {user,token,setUser,setToken,setNotification,notification} = useStateContext();
    if (!token){
        return <Navigate to='/login'/>
    }
    const onLogout = async (ev) => {

        ev.preventDefault()
        await axiosClient.post('/logout')
            .then(() => {
                setUser({})
                setToken(null);
            });
    }

    // useEffect( () => {
    //     axiosClient.get('/user')
    //         .then(({data}) => {
    //             setUser(data);
    //         })
    // } , [])

    return (
        <>
            <div className="wrapper">
                <Navbar onLogout={onLogout}/>
                <Sidebar/>
                <Content/>
                <Footer/>
            </div>
        </>
    )
}
