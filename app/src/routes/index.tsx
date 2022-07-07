import {RouteProps} from "react-router-dom";
import {Dashboard} from "../pages/Dashboard";
import {PageList as GenreList} from "../pages/Genre/PageList";
import {PageList as CategoryList} from "../pages/Category/PageList";
import {PageList as CastMemberList} from "../pages/CastMember/PageList";

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
	},
	{
		name: "categories.create",
		label: "Criar Categoria",
		path: "/categories/create",
		component: CategoryList,
		exact: true
	},
	{
		name: "genres.list",
		label: "Listar Gêneros",
		path: "/genres",
		component: GenreList,
		exact: true
	},
	{
		name: "genres.create",
		label: "Criar Gênero",
		path: "/genres/create",
		component: GenreList,
		exact: true
	},
	{
		name: "cast_members.list",
		label: "Listar Membros do Elenco",
		path: "/cast_members",
		component: CastMemberList,
		exact: true
	},
	{
		name: "cast_members.create",
		label: "Criar Membro do Elenco",
		path: "/cast_members/create",
		component: CastMemberList,
		exact: true
	}
];

export default routes;
