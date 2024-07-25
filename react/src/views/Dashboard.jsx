import {useTitleContext} from "../contexts/TitleContext.jsx";
import {useEffect} from "react";

export default function Dashboard(){
    const {setTitle} = useTitleContext();
    useEffect(() => {
        setTitle('الرئيسية')
    }, [setTitle]);
    return (
        <>
            <div className="content">
                <div className="container">
                    <div className="row">
                        <div className="col-lg-6">
                            <div className="card">
                                <div className="card-body">
                                    <h5 className="card-title">Card title</h5>
                                    <p className="card-text">
                                        Some quick example text to build on the card title and make up the bulk of the
                                        card's
                                        content.
                                    </p>
                                    <a href="#" className="card-link">Card link</a>
                                    <a href="#" className="card-link">Another link</a>
                                </div>
                            </div>
                            <div className="card card-success card-outline">
                                <div className="card-body">
                                    <h5 className="card-title">Card title</h5>
                                    <p className="card-text">
                                        Some quick example text to build on the card title and make up the bulk of the
                                        card's
                                        content.
                                    </p>
                                    <a href="#" className="card-link">Card link</a>
                                    <a href="#" className="card-link">Another link</a>
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-6">
                            <div className="card">
                                <div className="card-header">
                                    <h5 className="card-title m-0">Featured</h5>
                                </div>
                                <div className="card-body">
                                    <h6 className="card-title">Special title treatment</h6>
                                    <p className="card-text">With supporting text below as a natural lead-in to
                                        additional content.</p>
                                    <a href="#" className="btn btn-success">Go somewhere</a>
                                </div>
                            </div>
                            <div className="card card-success card-outline">
                                <div className="card-header">
                                    <h5 className="card-title m-0">Featured</h5>
                                </div>
                                <div className="card-body">
                                    <h6 className="card-title">Special title treatment</h6>
                                    <p className="card-text">With supporting text below as a natural lead-in to
                                        additional content.</p>
                                    <a href="#" className="btn btn-success">Go somewhere</a>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </>
    )
}
