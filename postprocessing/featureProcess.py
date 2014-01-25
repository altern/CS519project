import re

Features_input = "features-95.txt"
Features_output = "features-95-split.txt"

def split_features():

    features = None
    with open(Features_input, "r") as f_in:
        features = f_in.read()

    sc = []
    features = features.split("\n")
    for f in features:
        m = re.match('([A-Z][a-z]+)[A-Z]', f)
        if m:
            sc.append(m.group(1))
        else:
            sc.append('corelanguage');

    with open(Features_output, "w") as f_out:
        for i in range(0, len(sc) - 1):
            print(features[i] + "\t" + sc[i])
        
    


if __name__ == "__main__":
    split_features()
