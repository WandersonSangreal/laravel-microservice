import React from 'react';
import './App.scss';
import {Navbar} from "./components/Navbar";
import {Box} from "@mui/material";
import {BrowserRouter} from "react-router-dom";
import AppRouter from "./routes/AppRouter";

function App() {

	return (
		<React.Fragment>
			<BrowserRouter>
				<Navbar/>
				<Box>
					<AppRouter/>
				</Box>
			</BrowserRouter>
		</React.Fragment>
	);

}

export default App;
