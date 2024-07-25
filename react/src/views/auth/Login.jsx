import {useStateContext} from "../../contexts/ContextProvider.jsx";
import {useRef, useState} from "react";
import axiosClient from "../../axios-client.js";

export default function Login(){
    const {setToken , setUser} = useStateContext();
    const {errors , setErrors} = useState();
    const emailRef = useRef();
    const passwordRef = useRef();
    const onSubmit = async (ev) => {
        ev.preventDefault();
        await axiosClient.post('/login',{
            'email' : emailRef.current.value,
            'password' : passwordRef.current.value,
        })
            .then(({data}) => {
                setToken(data.token);
                setUser(data.user);
            })
            .catch(err => {
                const response = err.response;
                if (response && response.status === 422){
                    if (response.data.errors){
                        setErrors(response.data.errors);
                        console.log(errors);
                    }
                    else{
                        setErrors({
                            email : [response.data.message]
                        })
                    }
                }
            });
    }

    return (
        <>
            <form onSubmit={onSubmit}>
                <input type="text" ref={emailRef} placeholder='email'/>
                <input type="text" ref={passwordRef} placeholder='password'/>
                <button type='submit'>Login</button>
            </form>
        </>
    )
}
