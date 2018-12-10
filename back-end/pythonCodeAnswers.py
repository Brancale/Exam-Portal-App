def operation(op, a, b):
    if op == '+':
        return a + b
    elif op == '-':
        return a - b
    elif op == '*':
        return a * b
    elif op == '/':
        return a / b
    else:
        return 'error'

--------------------------------------------------------------------------------

def inRange(num,left,right):
    return num >= left and num <= right

--------------------------------------------------------------------------------

def maxArray(lst):
    maxVal = lst[0]
    for item in lst:
        if item > maxVal:
            maxVal = item
    return maxVal

--------------------------------------------------------------------------------

def initialVowels(sentence):
    vowels = 'AEIOUaeiou'
    words = sentence.split()
    lst = []
    for word in words:
        if word[0] in vowels:
            lst.append(word)
    return lst

--------------------------------------------------------------------------------

def repeatWords(sentence):
    words = sentence.split()
    lst = []
    for word in words:
        if words.count(word) > 1 and word not in lst:
            lst.append(word)
    return lst

--------------------------------------------------------------------------------

def evenLengths(sentence):
    count = 0
    for word in sentence:
        if len(word) % 2 == 0:
            count += 1
    return count

--------------------------------------------------------------------------------

def beginsWith(letter, sentence):
    count = 0
    for word in sentence:
        if word[0] == letter:
            count += 1
    return count

--------------------------------------------------------------------------------

def longestWord(sentence):
    words = sentence.split()
    max = 0
    for word in words:
        if len(word) > max:
            max = len(word)
    return max

--------------------------------------------------------------------------------

def longWords(sentence, cutoff):
    words = sentence.split()
    rtn = []
    for word in words:
        if len(word) > cutoff:
            rtn.append(word)
    return rtn

--------------------------------------------------------------------------------

def isOdd(number):
    return number % 2 == 1

--------------------------------------------------------------------------------

def isPalindrome(word):
    return word == word[::-1]
