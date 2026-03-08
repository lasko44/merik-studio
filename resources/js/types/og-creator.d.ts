export interface OgBackground {
    type: 'color' | 'image';
    color: string;
    imageUrl: string | null;
}

export interface OgTitle {
    content: string;
    fontSize: number;
    fontFamily: string;
    color: string;
    x: number;
    y: number;
    maxWidth: number;
}

export interface OgLogo {
    imageUrl: string;
    width: number;
    height: number;
    x: number;
    y: number;
}

export interface OgImageState {
    background: OgBackground;
    title: OgTitle;
    logo: OgLogo | null;
}

export interface OgCreatorProps {
    show: boolean;
    initialTitle?: string;
}
