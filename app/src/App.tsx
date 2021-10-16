import React from 'react';
import './App.scss';
import {Navbar} from "./components/Navbar";
import {Page} from "./components/Page";
import {Box} from "@mui/material";

function App() {
	return (
		<React.Fragment>
			<Navbar/>
			<Box>
				<Page title={'Teste'}>Conteúdo</Page>
			</Box>
		</React.Fragment>
	);
}

export default App;
