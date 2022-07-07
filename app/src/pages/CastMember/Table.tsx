import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn} from 'mui-datatables';
import {useEffect, useState} from "react";
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";

import {httpVideo} from "../../util/http";

const types: any = {
	1: "Diretor",
	2: "Ator",
}

const columns: Array<MUIDataTableColumn> = [
	{
		name: "name",
		label: "Nome"
	},
	{
		name: "type",
		label: "Tipo",
		options: {
			customBodyRender(value, tableMeta, updateValue) {
				return <strong>{types[value]}</strong>;
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
		httpVideo.get('cast_members').then(response => setData(response.data.data)).catch(reason => console.error(reason));
	}, []);

	return (
		<div>
			<MUIDataTable title="Listagem de Membros do Elenco" columns={columns} data={data}
			              options={{filterType: 'checkbox'}}/>
			{/*<button onClick={() => setCount(count + 1)}>{count}</button>*/}
		</div>
	);
};
