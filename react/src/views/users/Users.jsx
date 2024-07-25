import {useTitleContext} from "../../contexts/TitleContext.jsx";
import {useEffect} from "react";
import {Link} from "react-router-dom";

export default function Users(){
    const {setTitle} = useTitleContext();
    useEffect(() => {
        setTitle('المستخدمين')
    }, []);
    return(
        <>
            <div className='row'>
                <div className="col-md-12">
                    <div className="card">
                        <div className="card-body">
                            <Link to='/add_users' className='btn btn-dark'>اضافة مستخدم</Link>
                        </div>
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="col-md-12">
                    <div className="card">
                        <div className="card-body">
                            <div className="row">
                                <div className="col-md-8">
                                    <div className="row">
                                        <div className="col-md-12">
                                            <label htmlFor="">اسم المستخدم</label>
                                            <input type="text" className='form-control' placeholder='اسم المستخدم'/>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-4 text-center">
                                    <span style={{fontSize:'150px'}} className='fa fa-user-plus'></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}
