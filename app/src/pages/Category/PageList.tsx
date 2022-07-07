import * as React from 'react';
import {Page} from "../../components/Page";
import {Box, Fab} from "@mui/material";
import {Link} from "react-router-dom";
import AddIcon from '@mui/icons-material/Add';
import {Table} from "./Table";

export const PageList = () => {

	return (
		<Page title={'Listar Categorias'}>
			<Box dir={'rtl'}>
				<Fab title="Adidionar Categoria" size="small" color={"primary"} component={Link}
				     to="/categories/create">
					<AddIcon/>
				</Fab>
			</Box>
			<Box>
				<Table/>
			</Box>
		</Page>
	);

};
