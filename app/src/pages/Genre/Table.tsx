import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn} from 'mui-datatables';
import {useEffect, useState} from "react";
import {Chip} from "@mui/material";
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";

import {httpVideo} from "../../util/http";

const columns: Array<MUIDataTableColumn> = [
	{
		name: "name",
		label: "Nome"
	},
	{
		name: "categories",
		label: "Categorias",
		options: {
			customBodyRender(value, tableMeta, updateValue) {
				return value.map((item: any) => item.name).join(',');
			}
		}
	},
	{
		name: "is_active",
		label: "Ativo",
		options: {
			customBodyRender(value, tableMeta, updateValue) {
				return value ? <Chip label="Sim" color="primary"/> : <Chip label="Não" color="secondary"/>;
			}
		}
	},
	{
		name: "created_at",
		label: "Data de Criação",
		options: {
			customBodyRender(value, tableMeta, updateValue) {
				return <span>{format(parseISO(value), 'dd/MM/yyyy HH:mm:ss')}</span>;
			}
		}
	}
];

type Props = {};

export const Table = (props: Props) => {

	const [data, setData] = useState([]);

	useEffect(() => {
		httpVideo.get('genres').then(response => setData(response.data.data)).catch(reason => console.error(reason));
	}, []);

	return (
		<div>
			<MUIDataTable title="Listagem de Gêneros" columns={columns} data={data}
			              options={{filterType: 'checkbox'}}/>
			{/*<button onClick={() => setCount(count + 1)}>{count}</button>*/}
		</div>
	);
};
