import {RouteProps} from "react-router-dom";
import {Dashboard} from "../pages/Dashboard";
import {List as CategoryList} from "../pages/Category/List";

interface CustomRoute extends RouteProps {
	label: string
}

const routes: Array<CustomRoute> = [
	{
		label: "Dashboard",
		path: "/",
		component: Dashboard,
		exact: true
	},
	{
		label: "Listar Categorias",
		path: "/categories",
		component: CategoryList,
		exact: true
	}
];

export default routes;
