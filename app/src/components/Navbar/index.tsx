import * as React from 'react';
import {AppBar, Button, Toolbar, Typography} from "@mui/material";
import "./index.scss";
import logo from "../../static/img/logo.png";
import {Menu} from "./Menu";

export const Navbar: React.FC = () => {

	return (
		<AppBar>
			<Toolbar className="Toolbar">
				<Menu/>
				<Typography className="Title">
					<img src={logo} alt="Codeflix" className="App-logo"/>
				</Typography>
				<Button color="inherit">Login</Button>
			</Toolbar>
		</AppBar>
	);

};
