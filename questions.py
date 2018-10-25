def isRange(num,left,right):
    return num >= left and num <= right

print(isRange(5,1,6))


def maxArray(lst):
    maxVal = lst[0]
    for item in lst:
        if item > maxVal:
            maxVal = item
    return maxVal

print(maxArray([1,6,2,5,12]))


def initialVowels(sentence):
    vowels = "AEIOUaeiou"
    words = sentence.split()
    lst = []
    for word in words:
        if word[0] in vowels:
            lst.append(word)
    return lst

print(initialVowels('Our lives begin to end the day we become silent about things that matter'))


def repeatWords(sentence):
    words = sentence.split()
    lst = []
    for word in words:
        if words.count(word) > 1 and word not in lst:
            lst.append(word)
    return lst

print(repeatWords('repeat words repeat word words repeat no repeat'))


def evenLengths(sentence):
    count = 0
    for word in sentence:
        if len(word) % 2 == 0:
            count += 1
    return count
print(evenLengths(["hi","bye","cart","mart"]))


def beginsWith(letter, sentence):
    count = 0
    for word in sentence:
        if word[0] == letter:
            count += 1
    return count

print(beginsWith('t',['the','rain','in','spain','falls','mainly','on','the','plain']))


def longestWord(sentence):
    words = sentence.split()
    max = 0
    for word in words:
        if len(word) > max:
            max = len(word)
    return max

print(longestWord("the quick brown fox jumped over the lazy dog"))

def longWords(sentence, cutoff):
    words = sentence.split()
    rtn = []
    for word in words:
        if len(word) > cutoff:
            rtn.append(word)
    return rtn


def isOdd(number):
    return number % 2 == 1

print(isOdd(3))

def isPalindrome(word):
    return word == word[::-1]

print(isPalindrome("racecar"))


