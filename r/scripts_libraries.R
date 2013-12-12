source('util.R')

data <- read.csv('scripts_libraries.csv')

nMin <- min(data$Number_of_occurences)
nMax <- max(data$Number_of_occurences)
stepSize <- nMax %/% 10
x <- c()
y <- c()
for (i in seq(1, nMax, by=stepSize) ) {
	dataRange <- data[data$Number_of_occurences > i & data$Number_of_occurences <= i+stepSize, ]
	x <- c(x, paste0(i, "-", i+stepSize))
	y <- c(y, NROW(dataRange$Number_of_occurences))
	print(paste0(i, "-", i+stepSize, ": ", NROW(dataRange$Number_of_occurences)))
}
jpeg('scripts_libraries.jpg')
barplot(y,names.arg=x,xlab="Occurences",ylab="Number of libraries",main="Occurence of libraries in scripts")