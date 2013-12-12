source('util.R')

data <- read.csv('interactive.csv')
# data <- removeOutliers(data, "number_of_scripts");
#data <- removeZeroes(data, "number_of_scripts");
# data <- removeOutliers(data, "number_of_completed_tutorials");
#data <- removeZeroes(data, "number_of_completed_tutorials");
data <- data[data["number_of_scripts"] > 0 || data["number_of_completed_tutorials"] > 0, ]
dataScore <- data
dataReviews <- data
dataSubscribers <- data

print (paste0("Number of authors: ", NROW(data)))
print ("")

# print(data)

print ("Number of completed tutorials")

print (mean(data$number_of_completed_tutorials))
print (median(data$number_of_completed_tutorials))

print ("Number of scripts")

print( wilcox.test(data$number_of_scripts, data$number_of_completed_tutorials))
print( cor(data$number_of_scripts, data$number_of_completed_tutorials, use="complete.obs"))
print (mean(data$number_of_scripts))
print (median(data$number_of_scripts))

print("Score")

print( wilcox.test(dataScore$score, dataScore$number_of_completed_tutorials))
print( cor(dataScore$score, dataScore$number_of_completed_tutorials, use="complete.obs"))
print (mean(dataScore$score))
print (median(dataScore$score))

print("receivedpositivereviews")

print( wilcox.test(dataReviews$receivedpositivereviews, dataReviews$number_of_completed_tutorials))
print( cor(dataReviews$receivedpositivereviews, dataReviews$number_of_completed_tutorials, use="complete.obs"))
print (mean(dataReviews$receivedpositivereviews))
print (median(dataReviews$receivedpositivereviews))

print("subscribers")

print( wilcox.test(dataSubscribers$subscribers, dataSubscribers$number_of_completed_tutorials))
print( cor(dataSubscribers$subscribers, dataSubscribers$number_of_completed_tutorials, use="complete.obs"))
print (mean(dataSubscribers$subscribers))
print (median(dataSubscribers$subscribers))