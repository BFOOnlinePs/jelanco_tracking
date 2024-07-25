import { useEffect, useState } from "react";
import axiosClient from "../../axios-client.js";

export default function AddRoles() {
    const [permissions, setPermissions] = useState([]);
    const [selectedPermissions, setSelectedPermissions] = useState([]);
    const [roleName, setRoleName] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errors, setErrors] = useState({});
    const [successMessage, setSuccessMessage] = useState('');

    useEffect(() => {
        getPermission();
    }, []);

    const getPermission = async () => {
        try {
            const response = await axiosClient.get('/users/permissions/get_permission');
            setPermissions(response.data);
        } catch (error) {
            console.error('Error fetching permissions:', error);
        }
    };

    const handlePermissionChange = (event) => {
        const value = event.target.value;
        setSelectedPermissions(
            selectedPermissions.includes(value)
                ? selectedPermissions.filter(permission => permission !== value)
                : [...selectedPermissions, value]
        );
    };

    const handleRoleNameChange = (event) => {
        setRoleName(event.target.value);
    };

    const validateForm = () => {
        const errors = {};
        if (!roleName.trim()) {
            errors.roleName = "اسم الدور مطلوب";
        }
        if (selectedPermissions.length === 0) {
            errors.permissions = "يرجى اختيار صلاحية واحدة على الاقل";
        }
        return errors;
    };

    const onSubmit = async (ev) => {
        ev.preventDefault();
        setErrors({});
        setSuccessMessage('');
        setIsSubmitting(true);

        const formErrors = validateForm();
        if (Object.keys(formErrors).length > 0) {
            setErrors(formErrors);
            setIsSubmitting(false);
            return;
        }

        const data = {
            name: roleName,
            permissions: selectedPermissions
        };

        try {
            const response = await axiosClient.post('/users/roles/create', data);
            setSuccessMessage(response.data.message);
            setRoleName('');
            setSelectedPermissions([]);
        } catch (e) {
            console.error('Error submitting the form:', e);
            setErrors({ submit: 'An error occurred while submitting the form.' });
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <>
            <div className="row">
                <div className="col-md-12">
                    <div className="card">
                        <div className="card-body">
                            <form onSubmit={onSubmit}>
                                <div className="row">
                                    <div className="col-md-8">
                                        <div className="row">
                                            <div className="col-md-8">
                                                <div className="form-group">
                                                    <label htmlFor="roleName">اسم الدور</label>
                                                    <input
                                                        type="text"
                                                        value={roleName}
                                                        onChange={handleRoleNameChange}
                                                        className='form-control'
                                                        placeholder='اسم الدور'
                                                        id="roleName"
                                                        disabled={isSubmitting}
                                                    />
                                                    {errors.roleName && <span className="text-danger">{errors.roleName}</span>}
                                                </div>
                                            </div>
                                            <div className="col-md-12">
                                                <div className="form-group">
                                                    <div>
                                                        <strong>الصلاحيات :</strong>
                                                        <div className="row">
                                                            {permissions.map((permission, index) => (
                                                                <div className='col-md-3' key={permission.id}>
                                                                    <input
                                                                        type="checkbox"
                                                                        className='m-1'
                                                                        id={'permission_checkbox_' + index}
                                                                        value={permission.name}
                                                                        onChange={handlePermissionChange}
                                                                        checked={selectedPermissions.includes(permission.name)}
                                                                        disabled={isSubmitting}
                                                                    />
                                                                    <label htmlFor={'permission_checkbox_' + index}>{permission.name}</label>
                                                                </div>
                                                            ))}
                                                        </div>
                                                        {errors.permissions && <span className="text-danger">{errors.permissions}</span>}
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="col-md-12">
                                                <button
                                                    type="submit"
                                                    className='btn btn-success btn-sm'
                                                    disabled={isSubmitting}
                                                >
                                                    {isSubmitting ? 'Saving...' : 'حفظ'}
                                                </button>
                                                {errors.submit && <div className="text-danger mt-2">{errors.submit}</div>}
                                                {successMessage && <div className="text-success mt-2">{successMessage}</div>}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-md-4 text-center">
                                        <span style={{ fontSize: '150px' }} className='fa fa-lock'></span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}
