import * as React from 'react';
import {IconButton, Menu as MuiMenu, MenuItem} from "@mui/material";
import MenuIcon from '@mui/icons-material/Menu';

export const Menu: React.FC = () => {

	const [anchorEl, setAnchorEl] = React.useState(null);
	const toogleMenu = Boolean(anchorEl);

	const handleOpen = (event: any) => setAnchorEl(event.currentTarget);
	const handleClose = () => setAnchorEl(null);

	return (
		<React.Fragment>
			<IconButton color="inherit" edge="start"
			            aria-label="open drawer"
			            aria-controls="menu-appbar"
			            aria-haspopup="true"
			            onClick={handleOpen}>
				<MenuIcon/>
			</IconButton>

			<MuiMenu id="appbar" open={toogleMenu} anchorEl={anchorEl} onClose={handleClose}>
				<MenuItem onClick={handleClose}>Categorias</MenuItem>
				<MenuItem onClick={handleClose}>Gêneros</MenuItem>
			</MuiMenu>
		</React.Fragment>
	);

};
