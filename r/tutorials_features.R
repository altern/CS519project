source('util.R')

data <- read.csv('tutorials_features.csv')

nMin <- min(data$Number_of_occurences)
nMax <- max(data$Number_of_occurences)
stepSize <- 10
x <- c()
y <- c()
for (i in seq(nMin, nMax-stepSize, by=stepSize) ) {
	dataRange <- data[data$Number_of_occurences > i & data$Number_of_occurences <= i+stepSize, ]
	x <- c(x, paste0(i, "-", i+stepSize))
	y <- c(y, NROW(dataRange$Number_of_occurences))
	print(paste0(i, "-", i+stepSize, ": ", NROW(dataRange$Number_of_occurences)))
}
jpeg('tutorials_features.jpg')
barplot(y,names.arg=x,xlab="Occurences",ylab="Number of features",main="Occurence of features in tutorials")