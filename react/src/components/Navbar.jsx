import {Link} from "react-router-dom";
import axiosClient from "../axios-client.js";

function Navbar({onLogout}) {
    return (
        <>
            <nav className="main-header navbar navbar-expand-md navbar-light navbar-white">
                <div className="container">
                    <a href="../../index3.html" className="navbar-brand">
                        <img src="./src/assets/img/logo.png" alt="AdminLTE Logo"
                             className="brand-image  elevation-3" style={{ opacity: .8}}/>
                        <span className="brand-text font-weight-light">اتحاد لجان العمل الزراعي</span>
                    </a>

                    <button className="navbar-toggler order-1" type="button" data-toggle="collapse"
                            data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                            aria-label="Toggle navigation">
                        <span className="navbar-toggler-icon"></span>
                    </button>

                    <div className="collapse navbar-collapse order-3" id="navbarCollapse">
                        <ul className="navbar-nav">
                            <li className="nav-item">
                                <Link to='/' className="nav-link">Home</Link>
                            </li>
                            <li className="nav-item dropdown">
                                <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                                   aria-expanded="false" className="nav-link dropdown-toggle">Team</a>
                                <ul aria-labelledby="dropdownSubMenu1" className="dropdown-menu border-0 shadow">
                                    <li><Link to='/users' href="#" className="dropdown-item">All Stuff </Link></li>
                                </ul>
                            </li>
                            <li className="nav-item dropdown">
                                <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                                   aria-expanded="false" className="nav-link dropdown-toggle">Fundraising Unit</a>
                                <ul aria-labelledby="dropdownSubMenu1" className="dropdown-menu border-0 shadow">
                                    <li><Link to='/donors' href="#" className="dropdown-item">Donors </Link></li>
                                    <li><Link to='/users' href="#" className="dropdown-item">Calls </Link></li>
                                    <li><Link to='/users' href="#" className="dropdown-item">Proposals </Link></li>
                                </ul>
                            </li>
                            <li className="nav-item dropdown">
                                <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                                   aria-expanded="false" className="nav-link dropdown-toggle">Media Report</a>
                                <ul aria-labelledby="dropdownSubMenu1" className="dropdown-menu border-0 shadow">
                                    <li><Link to='/donors' href="#" className="dropdown-item">Media Report </Link></li>
                                </ul>
                            </li>
                            <li className="nav-item dropdown">
                                <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                                   aria-expanded="false" className="nav-link dropdown-toggle">Projects</a>
                                <ul aria-labelledby="dropdownSubMenu1" className="dropdown-menu border-0 shadow">
                                    <li><Link to='/projects' href="#" className="dropdown-item">Projects </Link></li>
                                    <li><Link to='/activity' href="#" className="dropdown-item">Activity </Link></li>
                                    <li><Link to='/project_activity' href="#" className="dropdown-item">Project Activity </Link></li>
                                </ul>
                            </li>
                            <li className="nav-item dropdown">
                                <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                                   aria-expanded="false" className="nav-link dropdown-toggle">Settings</a>
                                <ul aria-labelledby="dropdownSubMenu1" className="dropdown-menu border-0 shadow">
                                    <li><Link to='/region' href="#" className="dropdown-item">Region </Link></li>
                                    <li><Link to='/users' href="#" className="dropdown-item">Cites </Link></li>
                                    <li><Link to='/users' href="#" className="dropdown-item">Places </Link></li>
                                    <li><Link to='/users' href="#" className="dropdown-item">Currencies </Link></li>
                                    <li><Link to='/type_of_beneficiaries' href="#" className="dropdown-item">Types Of Beneficiaries </Link></li>
                                </ul>
                            </li>
                        </ul>
                        <div style={{width:'100%'}}>
                            <a style={{cursor:"pointer"}} onClick={onLogout} className='float-right text-danger'>
                                Logout
                            </a>
                        </div>
                        {/*<form className="form-inline ml-0 ml-md-3">*/}
                        {/*    <div className="input-group input-group-sm">*/}
                        {/*        <input className="form-control form-control-navbar" type="search" placeholder="Search"*/}
                        {/*               aria-label="Search"/>*/}
                        {/*        <div className="input-group-append">*/}
                        {/*            <button className="btn btn-navbar" type="submit">*/}
                        {/*                <i className="fas fa-search"></i>*/}
                        {/*            </button>*/}
                        {/*        </div>*/}
                        {/*    </div>*/}
                        {/*</form>*/}
                    </div>

                    {/*<ul className="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">*/}
                    {/*    <li className="nav-item dropdown">*/}
                    {/*        <a className="nav-link" data-toggle="dropdown" href="#">*/}
                    {/*            <i className="fas fa-comments"></i>*/}
                    {/*            <span className="badge badge-danger navbar-badge">3</span>*/}
                    {/*        </a>*/}
                    {/*        <div className="dropdown-menu dropdown-menu-lg dropdown-menu-right">*/}
                    {/*            <a href="#" className="dropdown-item">*/}
                    {/*                <div className="media">*/}
                    {/*                    <img src="./src/assets/dist/img/user1-128x128.jpg" alt="User Avatar"*/}
                    {/*                         className="img-size-50 mr-3 img-circle"/>*/}
                    {/*                    <div className="media-body">*/}
                    {/*                        <h3 className="dropdown-item-title">*/}
                    {/*                            Brad Diesel*/}
                    {/*                            <span className="float-right text-sm text-danger"><i*/}
                    {/*                                className="fas fa-star"></i></span>*/}
                    {/*                        </h3>*/}
                    {/*                        <p className="text-sm">Call me whenever you can...</p>*/}
                    {/*                        <p className="text-sm text-muted"><i className="far fa-clock mr-1"></i> 4*/}
                    {/*                            Hours Ago</p>*/}
                    {/*                    </div>*/}
                    {/*                </div>*/}
                    {/*            </a>*/}
                    {/*            <div className="dropdown-divider"></div>*/}
                    {/*            <a href="#" className="dropdown-item">*/}
                    {/*                <div className="media">*/}
                    {/*                    <img src="../../dist/img/user8-128x128.jpg" alt="User Avatar"*/}
                    {/*                         className="img-size-50 img-circle mr-3"/>*/}
                    {/*                    <div className="media-body">*/}
                    {/*                        <h3 className="dropdown-item-title">*/}
                    {/*                            John Pierce*/}
                    {/*                            <span className="float-right text-sm text-muted"><i*/}
                    {/*                                className="fas fa-star"></i></span>*/}
                    {/*                        </h3>*/}
                    {/*                        <p className="text-sm">I got your message bro</p>*/}
                    {/*                        <p className="text-sm text-muted"><i className="far fa-clock mr-1"></i> 4*/}
                    {/*                            Hours Ago</p>*/}
                    {/*                    </div>*/}
                    {/*                </div>*/}
                    {/*            </a>*/}
                    {/*            <div className="dropdown-divider"></div>*/}
                    {/*            <a href="#" className="dropdown-item">*/}
                    {/*                <div className="media">*/}
                    {/*                    <img src="../../dist/img/user3-128x128.jpg" alt="User Avatar"*/}
                    {/*                         className="img-size-50 img-circle mr-3"/>*/}
                    {/*                    <div className="media-body">*/}
                    {/*                        <h3 className="dropdown-item-title">*/}
                    {/*                            Nora Silvester*/}
                    {/*                            <span className="float-right text-sm text-warning"><i*/}
                    {/*                                className="fas fa-star"></i></span>*/}
                    {/*                        </h3>*/}
                    {/*                        <p className="text-sm">The subject goes here</p>*/}
                    {/*                        <p className="text-sm text-muted"><i className="far fa-clock mr-1"></i> 4*/}
                    {/*                            Hours Ago</p>*/}
                    {/*                    </div>*/}
                    {/*                </div>*/}
                    {/*            </a>*/}
                    {/*            <div className="dropdown-divider"></div>*/}
                    {/*            <a href="#" className="dropdown-item dropdown-footer">See All Messages</a>*/}
                    {/*        </div>*/}
                    {/*    </li>*/}
                    {/*    <li className="nav-item dropdown">*/}
                    {/*        <a className="nav-link" data-toggle="dropdown" href="#">*/}
                    {/*            <i className="far fa-bell"></i>*/}
                    {/*            <span className="badge badge-warning navbar-badge">15</span>*/}
                    {/*        </a>*/}
                    {/*        <div className="dropdown-menu dropdown-menu-lg dropdown-menu-right">*/}
                    {/*            <span className="dropdown-header">15 Notifications</span>*/}
                    {/*            <div className="dropdown-divider"></div>*/}
                    {/*            <a href="#" className="dropdown-item">*/}
                    {/*                <i className="fas fa-envelope mr-2"></i> 4 new messages*/}
                    {/*                <span className="float-right text-muted text-sm">3 mins</span>*/}
                    {/*            </a>*/}
                    {/*            <div className="dropdown-divider"></div>*/}
                    {/*            <a href="#" className="dropdown-item">*/}
                    {/*                <i className="fas fa-users mr-2"></i> 8 friend requests*/}
                    {/*                <span className="float-right text-muted text-sm">12 hours</span>*/}
                    {/*            </a>*/}
                    {/*            <div className="dropdown-divider"></div>*/}
                    {/*            <a href="#" className="dropdown-item">*/}
                    {/*                <i className="fas fa-file mr-2"></i> 3 new reports*/}
                    {/*                <span className="float-right text-muted text-sm">2 days</span>*/}
                    {/*            </a>*/}
                    {/*            <div className="dropdown-divider"></div>*/}
                    {/*            <a href="#" className="dropdown-item dropdown-footer">See All Notifications</a>*/}
                    {/*        </div>*/}
                    {/*    </li>*/}
                    {/*    <li className="nav-item">*/}
                    {/*        <a className="nav-link" data-widget="control-sidebar" data-slide="true" href="#"*/}
                    {/*           role="button">*/}
                    {/*            <i className="fas fa-th-large"></i>*/}
                    {/*        </a>*/}
                    {/*    </li>*/}
                    {/*</ul>*/}
                </div>
            </nav>

        </>
    );
}

export default Navbar;
