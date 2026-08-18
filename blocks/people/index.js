import { registerBlockType } from '@wordpress/blocks';

import edit from './edit.jsx';
import save from './save.jsx';
import metadata from './block.json';
import './editor.scss';

registerBlockType(metadata.name, { ...metadata, edit, save });
