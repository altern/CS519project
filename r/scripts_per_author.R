library(xtable)

# Correlation Tables from:
# http://myowelt.blogspot.com/2008/04/beautiful-correlation-tables-in-r.html

rm(list=ls(all=TRUE))

corstarsl <- function(x){
	require(Hmisc)
	x <- as.matrix(x)
	R <- rcorr(x)$r
	p <- rcorr(x)$P

	## define notions for significance levels; spacing is important.
	mystars <- ifelse(p < .001, "***", ifelse(p < .01, "** ", ifelse(p < .05, "* ", " ")))

	## trunctuate the matrix that holds the correlations to two decimal
	R <- format(round(cbind(rep(-1.11, ncol(x)), R), 2))[,-1]

	## build a new matrix that includes the correlations with their apropriate stars
	Rnew <- matrix(paste(R, mystars, sep=""), ncol=ncol(x))
	diag(Rnew) <- paste(diag(R), " ", sep="")
	rownames(Rnew) <- colnames(x)
	colnames(Rnew) <- paste(colnames(x), "", sep="")

	## remove upper triangle
	Rnew <- as.matrix(Rnew)
	Rnew[upper.tri(Rnew, diag = TRUE)] <- ""
	Rnew <- as.data.frame(Rnew)

	## remove last column and return the matrix (which is now a data frame)
	Rnew <- cbind(Rnew[1:length(Rnew)-1])
	return(Rnew)
}

authors.stats <- data.frame(read.table("authors_stats_view.csv", sep=",", header=TRUE))

authors.cor <- authors.stats[, c(1,2,5,6,7,8,9)]
c.table <- corstarsl(authors.cor)
# This histogram is pretty meaningless since it's so heavily distributed to the left.

# Before I could read this into R, I had to go in and remove a quote mark in someone's username.
authors.tu <- data.frame(read.delim("author_statistics_by_tutorialuse.tab", header=TRUE))


boxplot(activedays ~ notutorial, data=authors.tu, main="Active Days")
boxplot(receivedpositivereviews ~ notutorial, data=authors.tu, main="Positive Reviews Received")
boxplot(subscribers ~ notutorial, data=authors.tu, main="Subscribers")
boxplot(score ~ notutorial, data=authors.tu, main="Score")
