source('util.R')

data <- read.csv('feature_coverage.csv')

nMin <- min(data$occurences)
nMax <- max(data$occurences)
stepSize <- 10
x <- c()
y <- c()
for (i in seq(1, nMax, by=stepSize) ) {
	dataRange <- data[data$occurences > i & data$occurences <= i+stepSize, ]
	x <- c(x, paste0(i, "-", i+stepSize))
	y <- c(y, NROW(dataRange$occurences))
	print(paste0(i, "-", i+stepSize, ": ", NROW(dataRange$occurences)))
}
jpeg('feature_coverage.jpg')
barplot(y,names.arg=x,xlab="Number of covered features",ylab="Number of tutorials",main="Number of feature occurences in tutorials")