\version "2.14.2"

\include "paper.ly"

\layout {
	\include "layout.ly"
}

\header {
	title = "[TITRE]"
	instrument = " "
	meter = ""
	arranger = ""
	tagline = ""
}

notes = {
  \include "score.ly"
}

\include "pdf.ly"

\include "midi.ly"