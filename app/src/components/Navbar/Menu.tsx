import * as React from 'react';
import {IconButton, Menu as MuiMenu, MenuItem} from "@mui/material";
import MenuIcon from '@mui/icons-material/Menu';
import {Link} from "react-router-dom";

import routes, {CustomRoute} from "../../routes";

const listRoutes = routes.map(route => route.name);

const menuRoutes = routes.filter(route => listRoutes.includes(route.name));

export const Menu: React.FC = () => {

	const [anchorEl, setAnchorEl] = React.useState(null);
	const toogleMenu = Boolean(anchorEl);

	const handleToogle = (event?: any) => {

		setAnchorEl((event.currentTarget.attributes['aria-controls']?.value === 'menu-appbar' ? event.currentTarget : null))

	};

	return (
		<React.Fragment>
			<IconButton color="inherit" edge="start"
			            aria-label="open drawer"
			            aria-controls="menu-appbar"
			            aria-haspopup="true"
			            onClick={handleToogle}>
				<MenuIcon/>
			</IconButton>

			<MuiMenu id="appbar" open={toogleMenu} anchorEl={anchorEl} onClose={handleToogle}>

				{
					listRoutes.map((routeName, key) => {

						const route = menuRoutes.find(route => route.name === routeName) as CustomRoute;

						return (
							<MenuItem key={key} component={Link} to={route.path as string} onClick={handleToogle}>
								{route.label}
							</MenuItem>
						);

					})
				}

			</MuiMenu>
		</React.Fragment>
	);

};
