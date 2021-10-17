import React from 'react';
import {Box} from "@mui/material";
import {Navbar} from "./components/Navbar";
import {BrowserRouter} from "react-router-dom";
import AppRouter from "./routes/AppRouter";
import Breadcrumb from "./components/Breadcrumb/Breadcrumb";

import './App.scss';

function App() {

	return (
		<React.Fragment>
			<BrowserRouter>
				<Navbar/>
				<Box className="PaddingHeader">
					<Breadcrumb/>
					<AppRouter/>
				</Box>
			</BrowserRouter>
		</React.Fragment>
	);

}

export default App;
