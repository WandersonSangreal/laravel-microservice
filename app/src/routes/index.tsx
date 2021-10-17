import {RouteProps} from "react-router-dom";
import {Dashboard} from "../pages/Dashboard";
import {List as CategoryList} from "../pages/Category/List";

export interface CustomRoute extends RouteProps {
	name: string
	label: string
}

const routes: Array<CustomRoute> = [
	{
		name: "dashboard",
		label: "Dashboard",
		path: "/",
		component: Dashboard,
		exact: true
	},
	{
		name: "categories.list",
		label: "Listar Categorias",
		path: "/categories",
		component: CategoryList,
		exact: true
	}
];

export default routes;
