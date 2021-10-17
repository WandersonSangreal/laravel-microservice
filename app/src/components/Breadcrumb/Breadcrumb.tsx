import * as React from 'react';
import Box from '@mui/material/Box';
import Link, {LinkProps} from '@mui/material/Link';
import Typography from '@mui/material/Typography';
import Breadcrumbs from '@mui/material/Breadcrumbs';
import {Link as RouterLink, Route} from 'react-router-dom';
import RouteParser from "route-parser";
import {Location} from "history";

import routes from "../../routes";
import './Breadcrumb.scss';

const breadcrumbMap: { [key: string]: string } = Object.fromEntries(new Map(routes.map(route => [route.path, route.label])));

interface LinkRouterProps extends LinkProps {
	to: string;
	replace?: boolean;
}

const LinkRouter = (props: LinkRouterProps) => (
	<Link {...props} component={RouterLink as any}/>
);

export default function Breadcrumb() {

	function makeBreadcrumb(location: Location) {

		const pathnames = location.pathname.split('/').filter(x => x);
		pathnames.unshift('/');

		return (
			<Breadcrumbs aria-label="breadcrumb">

				{
					pathnames.map((value, index) => {

						const last = index === pathnames.length - 1;
						const to = pathnames.slice(0, index + 1).join('/').replace('//', '/');
						const route = Object.keys(breadcrumbMap).find(path => new RouteParser(path).match(to));

						if (!route) {
							return null;
						}

						return last ? (
							<Typography key={to} color="inherit">
								{breadcrumbMap[route]}
							</Typography>
						) : (
							<LinkRouter underline="hover" to={to} key={to} className="ActiveRouteItem">
								{breadcrumbMap[route]}
							</LinkRouter>
						);

					})
				}
			</Breadcrumbs>
		);

	}

	return (
		<Box sx={{display: 'flex', flexDirection: 'column'}}>
			<Route>
				{
					({location}) => makeBreadcrumb(location)
				}
			</Route>
		</Box>
	);

}
