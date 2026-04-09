const badgeStatus = (label, color = '') => {
    if (!label) return '-';

    const colors = getBadgeStyle(color);
    
    return `
        <span class="badge" style="
            font-size: 12px;width: 82px;
            background-color: ${colors.bg}; 
            color: ${colors.text};
        ">
            ${label}
        </span>`.trim();
};

const getBadgeStyle = (hexColor, opacity = 0.2) => {
    let hex = hexColor.replace("#", "");

    if (!hex) hex = "cccccc";

    let r, g, b;

    if (hex.length === 3) {
        r = parseInt(hex.substring(0, 1).repeat(2), 16);
        g = parseInt(hex.substring(1, 2).repeat(2), 16);
        b = parseInt(hex.substring(2, 3).repeat(2), 16);
    } else {
        r = parseInt(hex.substring(0, 2), 16);
        g = parseInt(hex.substring(2, 4), 16);
        b = parseInt(hex.substring(4, 6), 16);
    }

    const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
    const textColorAuto = (yiq >= 128) ? "#000000" : "#ffffff";

    return {
        bg: `rgba(${r}, ${g}, ${b}, ${opacity})`,
        text: hexColor,
        border: hexColor
    };
};