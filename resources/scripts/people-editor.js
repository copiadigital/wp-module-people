import { registerPlugin } from '@wordpress/plugins';

import PersonPanel from './editor/PersonPanel.jsx';

// Registers theme/people. Kept here so every People concern stays inside this
// module — wp-module-blocks' editor barrel has no knowledge of it.
import '../../blocks/people';

// PersonPanel renders nothing outside the People post type, so this entry is
// safe to load on every block editor screen.
registerPlugin('people-person-details', { render: PersonPanel });
