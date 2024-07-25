import {useTitleContext} from "../../contexts/TitleContext.jsx";
import {useEffect, useState} from "react";
import AxiosClient from "../../axios-client.js";
import {Link} from "react-router-dom";
import EditRoles from "./EditRoles.jsx";

export default function Authorization(){
    const {setTitle} = useTitleContext();
    const [loading , setLoading] = useState(false);
    const [roles , setRoles] = useState([]);
    useEffect(() => {
        setTitle('صلاحيات المستخدمين');
        getRoles();
    }, []);
    const getRoles = async () => {
        setLoading(true);
        try {
            const response = await AxiosClient.get('/users/roles/get_roles');
            setRoles(response.data);
        } catch (error) {
            console.error('There was an error fetching the roles!', error);
        } finally {
            setLoading(false);
        }
    };

    return (
        <>
            <div className="row">
                <div className="col-md-12">
                    <div className="card">
                        <div className="card-body">
                            <Link to='/add_roles' className='btn btn-dark btn-sm'>اضافة صلاحية للمستخدمين</Link>
                        </div>
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="col-md-12">
                    <div className="card">
                        <div className="card-body">
                            <table className='table table-sm'>
                                <thead>
                                    <tr>
                                        <th>اسم الدور</th>
                                        <th>العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                {
                                    loading ?
                                        <tr>
                                            <td colSpan={2} className='text-center'>جاري التحميل</td>
                                        </tr>
                                        :
                                        roles.length <= 0
                                            ?
                                                <tr>
                                                    <td colSpan={2} className='text-center'>لا توجد بيانات</td>
                                                </tr>
                                            :
                                            roles.map(role => (
                                                <>
                                                    <tr>
                                                        <td>{role.name}</td>
                                                        <td>
                                                            <Link to={`/edit_roles/${role.id}`} className='btn btn-success btn-sm'><span className='fa fa-edit'></span></Link>
                                                        </td>
                                                    </tr>
                                                </>
                                            ))
                                }
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}
