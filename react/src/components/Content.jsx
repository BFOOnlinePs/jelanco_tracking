// Content.jsx
import { Outlet } from 'react-router-dom';
import {useTitleContext} from "../contexts/TitleContext.jsx";
import React from 'react';

export default function Content() {
    const { title } = useTitleContext();

    return (
        <div className="content-wrapper">
            <div className="content-header">
                <div className="container">
                    <div className="row mb-2">
                        <div className="col-sm-6">
                            <h1 className="m-0">{title}</h1>
                        </div>
                        <div className="col-sm-6">
                            {/* You can keep the breadcrumb logic here if needed */}
                        </div>
                    </div>
                </div>
            </div>

            <div className="content">
                <div className="container">
                    <Outlet />
                </div>
            </div>
        </div>
    );
}
