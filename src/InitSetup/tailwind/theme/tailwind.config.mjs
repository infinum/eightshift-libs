const defaultTheme = require('tailwindcss/defaultTheme');
import { processEightshiftClasses, getScreens } from '@eightshift/frontend-libs-tailwind/scripts/editor/tailwindcss';
import globalManifest from './src/Blocks/manifest.json';
import fluid, { extract } from 'fluid-tailwind';
import plugin from 'tailwindcss/plugin';
import animate from 'tailwindcss-animate';
import { themeColors } from './assets/scripts/theme-colors';

/** @type {import('tailwindcss').Config} */
export default {
	content: {
		files: ['./src/**/*.{html,js,php,json}', './*.php'],
		transform: processEightshiftClasses(Object.keys(globalManifest.globalVariables.breakpoints)),
		extract,
	},
	theme: {
		colors: themeColors,
		screens: getScreens(globalManifest.globalVariables.breakpoints),
		extend: {
			fontFamily: {
				sans: ['Noto Sans', ...defaultTheme.fontFamily.sans],
				display: ['Fraunces', ...defaultTheme.fontFamily.serif],
			},
			gridColumnEnd: {
				'span-1': 'span 1',
				'span-2': 'span 2',
				'span-3': 'span 3',
				'span-4': 'span 4',
				'span-5': 'span 5',
				'span-6': 'span 6',
				'span-7': 'span 7',
				'span-8': 'span 8',
				'span-9': 'span 9',
				'span-10': 'span 10',
				'span-11': 'span 11',
				'span-12': 'span 12',
			},
			gridRowEnd: {
				'span-1': 'span 1',
				'span-2': 'span 2',
				'span-3': 'span 3',
				'span-4': 'span 4',
				'span-5': 'span 5',
				'span-6': 'span 6',
				'span-7': 'span 7',
				'span-8': 'span 8',
				'span-9': 'span 9',
				'span-10': 'span 10',
				'span-11': 'span 11',
				'span-12': 'span 12',
			},
			rotate: {
				135: '135deg',
			},
			aspectRatio: {
				'3/2': '3 / 2',
				'5/4': '5 / 4',
				'21/9': '21 / 9',
			},
		},
	},
	plugins: [
		fluid,
		animate,
		plugin(({ addComponents, addVariant }) => {
			addComponents({
				'.font-synthesis-none': {
					'font-synthesis': 'none',
				},
				'.font-synthesis': {
					'font-synthesis': 'auto',
				},
			});
			addVariant('open', '&.is-open');
			addVariant('wp-logged-in', 'body.logged-in &');
			addVariant('wp-logged-in-mobile', '@media (max-width: 782px) { body.logged-in & }');
		}),
	],
};
