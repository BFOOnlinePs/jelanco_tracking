import {useParams} from "react-router-dom";
import {useEffect} from "react";
import {useTitleContext} from "../../contexts/TitleContext.jsx";

export default function EditRoles(){
    const { id } = useParams();
    const {setTitle} = useTitleContext();
    useEffect(() => {
        setTitle('تعديل دور');
    }, []);
    return (
        <>
            {id}
        </>
    );
}
