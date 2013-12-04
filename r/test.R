data <- read.csv('authors.csv')
print wilcox.test(data$number_of_scripts, data$number_of_completed_tutorials)