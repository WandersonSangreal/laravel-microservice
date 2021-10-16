import * as React from 'react';
import {Container, Typography} from "@mui/material";
import {makeStyles} from "@mui/styles";

const useStyles = makeStyles({
	title: {
		color: '#999999'
	},
	paddingTop: {
		paddingTop: '80px'
	}
})

type PageProps = {
	title: string,
	children: string,
};
export const Page = (props: PageProps) => {

	const classes = useStyles();

	return (
		<Container className={classes.paddingTop}>
			<Typography className={classes.title} variant="h5">
				{props.title}
			</Typography>
			{props.children}
		</Container>
	);
};
