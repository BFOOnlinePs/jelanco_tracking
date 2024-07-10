// SectionContext.jsx
import React, { createContext, useState, useContext, useEffect, useCallback } from 'react';

// Create a context for sections
const SectionContext = createContext();

// Provider component to wrap the app and provide section state
export const SectionProvider = ({ children }) => {
    const [sections, setSections] = useState({});

    // Function to set the section content, using useCallback to avoid unnecessary re-renders
    const setSection = useCallback((name, content) => {
        setSections(prevSections => {
            if (prevSections[name] === content) return prevSections; // Avoid setting the same content
            return { ...prevSections, [name]: content };
        });
    }, []);

    return (
        <SectionContext.Provider value={{ sections, setSection }}>
            {children}
        </SectionContext.Provider>
    );
};

// Hook to set a section content
export const useSection = (name, content) => {
    const { setSection } = useContext(SectionContext);

    // Use useEffect to avoid calling setSection in every render
    useEffect(() => {
        setSection(name, content);
    }, [name, content, setSection]);
};

// Hook to get all sections
export const useSections = () => {
    return useContext(SectionContext).sections;
};
