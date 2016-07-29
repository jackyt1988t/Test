import sys
import pymorphy2
import urllib.parse

morph = pymorphy2.MorphAnalyzer()

if __name__ == "__main__":
	for param in sys.argv:
		if param != "test.py":
			spisok = morph.parse(urllib.parse.unquote_plus(param))
			for element in spisok:
				print(element.lexeme)
