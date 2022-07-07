import * as React from 'react';
import {Page} from "../../components/Page";
import {Box, Fab} from "@mui/material";
import {Link} from "react-router-dom";
import AddIcon from '@mui/icons-material/Add';
import {Table} from "./Table";

export const PageList = () => {

	return (
		<Page title={'Listar Membros do Elenco'}>
			<Box dir={'rtl'}>
				<Fab title="Adidionar Membro do Elenco" size="small" color={"primary"} component={Link}
				     to="/cast-members/create">
					<AddIcon/>
				</Fab>
			</Box>
			<Box>
				<Table/>
			</Box>
		</Page>
	);

};
