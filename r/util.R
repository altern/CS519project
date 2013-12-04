if(!exists('util_R')){
 util_R<-T
 
 	PRUNE_PERCENTILE <<- .90

	printf <- function(...) {
		cat(sprintf(...))
	}

	removeOutliers <- function(dataFrame, column){
		if(nrow(dataFrame) == 0)
			return(dataFrame)
		if(!column %in% colnames(dataFrame)) {
			return(dataFrame)
		}
		biggest <- unname(quantile(dataFrame[column], PRUNE_PERCENTILE, na.rm=TRUE))
		prunnedData <- dataFrame[dataFrame[column] < biggest, ]

		return(prunnedData)
	}

	removeZeroes <- function(dataFrame, column){
		if(nrow(dataFrame) == 0)
			return(dataFrame)
		if(!column %in% colnames(dataFrame)) {
			return(dataFrame)
		}
		withoutZeroes <- dataFrame[dataFrame[column] > 0, ]

		return(withoutZeroes)
	}
	
	sortData <- function(dataFrame, column) {
		sortedDataFrame <- dataFrame[order(dataFrame[column]),]
		return (sortedDataFrame)
	}
	
	classifyData <- function(dataFrame, columnToBucket, bucketSize, functionToApply, columnToApply) {
		bucketData <- dataFrame[columnToBucket]
		bucketStart <- as.numeric(head(bucketData,n=1))
		print(paste0("min: ", bucketStart))
		maxElem <- as.numeric(tail(bucketData, n=1))
		print(paste0("max: ", maxElem))
		bucketValues <- c()
		if(is.na(bucketStart) | is.na(maxElem)) {
			return (bucketValues)
		}
		while (bucketStart < maxElem) {
			bucket <- dataFrame[(dataFrame[columnToBucket] > bucketStart) & (dataFrame[columnToBucket] < (bucketStart + bucketSize)),]
			if(!is.null(bucket)) {
				if(length(bucket[columnToApply]) > 0 & !is.null(bucket[columnToApply])) {
					dataToApply <- bucket[columnToApply] 
					bucketValue <- as.numeric(sapply(dataToApply, functionToApply))
					if(!is.na(bucketValue)) {
						bucketValues <- c(bucketValues, bucketValue)
					}
				}
			} 
			bucketStart <- bucketStart + bucketSize
		}
		return (bucketValues)
	}
	
	getAuthorsContribCoeff <- function(repoName) {
		if(!file.exists(repoName)) {
			return(FALSE)
		}
		data <- read.csv(repoName)
		authors <- rownames(table(data$Author[data$Author != ""]))
		authorSLOCs <- c()
		for (author in authors) {
			authorData <- data[which(data$Author == author),]
			authorData  <- removeZeroes(authorData, "SLOC")
			authorData  <- removeOutliers(authorData, "SLOC")
			authorSLOC <- sum(authorData$SLOC)
			authorSLOCs <- c(authorSLOCs, authorSLOC)
		}
		authorsContribCoeff <- sum(authorSLOCs/max(authorSLOCs))
		return (authorsContribCoeff)
	}

	analyzeRepo <- function(repoPath, repoAnalysisFunction) {
		data <- read.csv(repoPath)

		dataWithoutZeroes <- removeZeroes(data, "SLOC")
		prunnedData <- removeOutliers(dataWithoutZeroes, "SLOC")
		prunnedData <- prunnedData[is.na(prunnedData$Author) == 0, ]

		repoAnalysisFunction(repoPath, prunnedData)
	}

	analyzeRepositories <- function(repoDir, repoAnalysisFunction){
		repositoryFiles <- list.files(path = repoDir, pattern = "*csv", full.names = TRUE) 

		for (repositoryFile in repositoryFiles){
			analyzeRepo(repositoryFile, repoAnalysisFunction)
		}
	}
	
	plotSwitch <- function(plotDev, plotIndex) {
		index <- plotIndex
		if(index%%(COLS*ROWS) == COLS*ROWS - 1 ) {
			# dev.new();
			plotDev <- dev.cur() 
			par(mfrow=c(ROWS,COLS), oma=c(1,1,1,1))
			plotIndex <- plotIndex + 1
		}
		
		return(plotIndex)
	}
	
	getAverageCorrelation <- function( correlations ) {
		correlations[correlations == 1] <- 0.999
		correlations[correlations == -1] <- -0.999
		Z <- 0.5*(log(1 + correlations) - log(1 - correlations))
		return ( (exp(2*mean(Z)) - 1) / (exp(2*mean(Z)) + 1) )
	}
}

