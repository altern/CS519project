source('util.R')

data <- read.csv('authors.csv')
# data <- removeOutliers(data, "number_of_scripts");
data <- removeZeroes(data, "number_of_scripts");
# data <- removeOutliers(data, "number_of_completed_tutorials");
data <- removeZeroes(data, "number_of_completed_tutorials");
data <- removeZeroes(data, "score");
data <- removeZeroes(data, "receivedpositivereviews");
data <- removeZeroes(data, "subscribers");

# print(data)

print ("Number of scripts")

print( wilcox.test(data$number_of_scripts, data$number_of_completed_tutorials))
print( cor(data$number_of_scripts, data$number_of_completed_tutorials, use="complete.obs"))

print("Score")

print( wilcox.test(data$score, data$number_of_completed_tutorials))
print( cor(data$score, data$number_of_completed_tutorials, use="complete.obs"))

print("receivedpositivereviews")

print( wilcox.test(data$receivedpositivereviews, data$number_of_completed_tutorials))
print( cor(data$receivedpositivereviews, data$number_of_completed_tutorials, use="complete.obs"))

print("subscribers")

print( wilcox.test(data$subscribers, data$number_of_completed_tutorials))
print( cor(data$subscribers, data$number_of_completed_tutorials, use="complete.obs"))
