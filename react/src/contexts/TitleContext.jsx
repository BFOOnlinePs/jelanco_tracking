// TitleContext.jsx
import React, { createContext, useContext, useState } from 'react';

const TitleContext = createContext({
    title: 'Top Navigation Example 3.0',
    setTitle: () => {}
});

export const TitleProvider = ({ children }) => {
    const [title, setTitle] = useState('الرئيسية');

    return (
        <TitleContext.Provider value={{ title, setTitle }}>
            {children}
        </TitleContext.Provider>
    );
};

export const useTitleContext = () => useContext(TitleContext);
