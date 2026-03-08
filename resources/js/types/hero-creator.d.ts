export interface GradientColorStop {
    color: string;
    position: number; // 0-100 percentage
}

export interface GradientConfig {
    type: 'linear' | 'radial';
    angle: number; // degrees for linear gradient
    colorStops: GradientColorStop[];
}

export interface HeroBackground {
    type: 'solid' | 'gradient' | 'image';
    color: string;
    gradient: GradientConfig;
    imageUrl: string | null;
    overlay: {
        enabled: boolean;
        color: string;
        opacity: number; // 0-100
    };
}

export interface HeroText {
    heading: string;
    subheading: string;
    color: string;
    alignment: 'left' | 'center' | 'right';
}

export interface HeroImageState {
    background: HeroBackground;
    text: HeroText;
    dimensions: {
        width: number;
        height: number;
    };
}

export interface ThemeColors {
    primaryColor: string;
    secondaryColor: string;
    accentColor: string;
    backgroundColor: string;
    textColor: string;
}

export interface HeroCreatorProps {
    show: boolean;
    initialHeading?: string;
    initialSubheading?: string;
    themeColors?: ThemeColors;
}
