// eslint-disable-next-line no-unused-vars
import React from "react";
import {Link} from "react-router-dom";

function Sidebar (){
    return (
        <>
            <aside className="main-sidebar sidebar-dark-primary elevation-4">
                {/* Brand Logo */}
                <a href="index3.html" className="brand-link">
                    <img
                        src={`${import.meta.env.VITE_API_BASE_URL}/src/assets/dist/img/AdminLTELogo.png`}
                        alt="AdminLTE Logo"
                        className="brand-image img-circle elevation-3"
                        style={{opacity: ".8"}}
                    />
                    <span className="brand-text font-weight-light">Jelanco Tracking</span>
                </a>
                {/* Sidebar */}
                <div className="sidebar">
                    {/* Sidebar user panel (optional) */}
                    <div className="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div className="image">
                            <img
                                src="./src/assets/dist/img/user2-160x160.jpg"
                                className="img-circle elevation-2"
                                alt="User Image"
                            />
                        </div>
                        <div className="info">
                            <a href="#" className="d-block">
                                Alexander Pierce
                            </a>
                        </div>
                    </div>
                    {/* Sidebar Menu */}
                    <nav className="mt-2">
                        <ul
                            className="nav nav-pills nav-sidebar flex-column"
                            data-widget="treeview"
                            role="menu"
                            data-accordion="false"
                        >
                            <li className="nav-item">
                                <Link to='/home' className="nav-link">
                                    <i className="nav-icon fa fa-home"/>
                                    <p>الرئيسية</p>
                                </Link>
                            </li>
                            <li className="nav-item has-treeview">
                                <a href="#" className="nav-link">
                                    <i className="nav-icon fa fa-user"/>
                                    <p>
                                        المستخدمين
                                        <i className="right fas fa-angle-left"/>
                                    </p>
                                </a>
                                <ul className="nav nav-treeview">
                                    <li className="nav-item">
                                        <Link to='/authorization' className="nav-link">
                                            <i className="far fa-circle nav-icon"/>
                                            <p>صلاحيات المستخدمين</p>
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link to='/users' className="nav-link">
                                            <i className="far fa-circle nav-icon"/>
                                            <p>قائمة المستخدمين</p>
                                        </Link>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                    {/* /.sidebar-menu */}
                </div>
                {/* /.sidebar */}
            </aside>

        </>
    );
}

export default Sidebar;
