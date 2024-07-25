import {useTitleContext} from "../../contexts/TitleContext.jsx";
import {useEffect, useState} from "react";
import axiosClient from "../../axios-client.js";

export default function AddUsers(){
    const {setTitle} = useTitleContext();
    const [roles, setRoles] = useState([]);
    const [permissions, setPermissions] = useState([]);
    const [selectedRoles, setSelectedRoles] = useState([]);
    const [selectedPermissions, setSelectedPermissions] = useState([]);
    useEffect(() => {
        setTitle('اضافة مستخدم جديد');
        axiosClient.get('/users/roles/get_roles').then(response => {
            setRoles(response.data);
        });

        axiosClient.get('/users/permissions/get_permission').then(response => {
            setPermissions(response.data);
        });
    }, []);

    const handleRoleChange = (event) => {
        const value = event.target.value;
        setSelectedRoles(
            selectedRoles.includes(value)
                ? selectedRoles.filter(role => role !== value)
                : [...selectedRoles, value]
        );
    };

    const handlePermissionChange = (event) => {
        const value = event.target.value;
        setSelectedPermissions(
            selectedPermissions.includes(value)
                ? selectedPermissions.filter(permission => permission !== value)
                : [...selectedPermissions, value]
        );
    };

    return (
        <>
            <div className="row">
                <div className="col-md-12">
                    <div className="card">
                        <div className="card-body">
                            <div className="row">
                                <div className="col-md-8">
                                    <div className="row">
                                        <div className="col-md-12">
                                            <div className="form-group">
                                                <label htmlFor="">الاسم</label>
                                                <input type="text" className='form-control' placeholder='الاسم' />
                                            </div>
                                        </div>
                                        <div className="col-md-6">
                                            <div className="form-group">
                                                <label htmlFor="">البريد الالكتروني</label>
                                                <input type="text" className='form-control' placeholder='البريد الالكتروني' />
                                            </div>
                                        </div>
                                        <div className="col-md-6">
                                            <div className="form-group">
                                                <label htmlFor="">كلمة المرور</label>
                                                <input type="password" className='form-control' placeholder='كلمة المرور' />
                                            </div>
                                        </div>
                                        <div className="col-md-12">
                                            <div className="form-group">
                                                <label htmlFor="">صلاحيات المستخدم</label>
                                                <div>
                                                    <strong>Roles:</strong>
                                                    {roles.map(role => (
                                                        <div key={role.id}>
                                                            <input
                                                                type="checkbox"
                                                                value={role.name}
                                                                onChange={handleRoleChange}
                                                                checked={selectedRoles.includes(role.name)}
                                                            />
                                                            {role.name}
                                                        </div>
                                                    ))}
                                                </div>
                                                <div>
                                                    <strong>Permissions:</strong>
                                                    {permissions.map(permission => (
                                                        <div key={permission.id}>
                                                            <input
                                                                type="checkbox"
                                                                value={permission.name}
                                                                onChange={handlePermissionChange}
                                                                checked={selectedPermissions.includes(permission.name)}
                                                            />
                                                            {permission.name}
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-4 text-center">
                                    <span style={{fontSize: '150px'}} className='fa fa-user-plus'></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
