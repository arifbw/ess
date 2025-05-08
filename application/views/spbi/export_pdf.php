<style>
	*,
	::after,
	::before {
		box-sizing: border-box
	}


	html {
		font-family: sans-serif;
		line-height: 1.15;
		-webkit-text-size-adjust: 100%;
		-webkit-tap-highlight-color: transparent
	}

	article,
	aside,
	figcaption,
	figure,
	footer,
	header,
	hgroup,
	main,
	nav,
	section {
		display: block
	}

	body {
		margin: 0;
		font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
		font-size: 1rem;
		font-weight: 400;
		line-height: 1.5;
		color: #212529;
		text-align: left;
		background-color: #fff
	}

	[tabindex="-1"]:focus:not(:focus-visible) {
		outline: 0 !important
	}

	hr {
		box-sizing: content-box;
		height: 0;
		overflow: visible
	}

	h1,
	h2,
	h3,
	h4,
	h5,
	h6 {
		margin-top: 0;
		margin-bottom: .5rem
	}

	p {
		margin-top: 0;
		margin-bottom: 1rem
	}

	abbr[data-original-title],
	abbr[title] {
		text-decoration: underline;
		-webkit-text-decoration: underline dotted;
		text-decoration: underline dotted;
		cursor: help;
		border-bottom: 0;
		-webkit-text-decoration-skip-ink: none;
		text-decoration-skip-ink: none
	}

	address {
		margin-bottom: 1rem;
		font-style: normal;
		line-height: inherit
	}

	dl,
	ol,
	ul {
		margin-top: 0;
		margin-bottom: 1rem
	}

	ol ol,
	ol ul,
	ul ol,
	ul ul {
		margin-bottom: 0
	}

	dt {
		font-weight: 700
	}

	dd {
		margin-bottom: .5rem;
		margin-left: 0
	}

	blockquote {
		margin: 0 0 1rem
	}

	b,
	strong {
		font-weight: bolder
	}

	small {
		font-size: 80%
	}

	sub,
	sup {
		position: relative;
		font-size: 75%;
		line-height: 0;
		vertical-align: baseline
	}

	sub {
		bottom: -.25em
	}

	sup {
		top: -.5em
	}

	a {
		color: #007bff;
		text-decoration: none;
		background-color: transparent
	}

	a:hover {
		color: #0056b3;
		text-decoration: underline
	}

	a:not([href]) {
		color: inherit;
		text-decoration: none
	}

	a:not([href]):hover {
		color: inherit;
		text-decoration: none
	}

	code,
	kbd,
	pre,
	samp {
		font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
		font-size: 1em
	}

	pre {
		margin-top: 0;
		margin-bottom: 1rem;
		overflow: auto
	}

	figure {
		margin: 0 0 1rem
	}

	img {
		vertical-align: middle;
		border-style: none
	}

	svg {
		overflow: hidden;
		vertical-align: middle
	}

	table {
		border-collapse: collapse
	}

	caption {
		padding-top: .75rem;
		padding-bottom: .75rem;
		color: #6c757d;
		text-align: left;
		caption-side: bottom
	}

	th {
		text-align: inherit
	}

	label {
		display: inline-block;
		margin-bottom: .5rem
	}

	button {
		border-radius: 0
	}

	button:focus {
		outline: 1px dotted;
		outline: 5px auto -webkit-focus-ring-color
	}

	button,
	input,
	optgroup,
	select,
	textarea {
		margin: 0;
		font-family: inherit;
		font-size: inherit;
		line-height: inherit
	}

	button,
	input {
		overflow: visible
	}

	button,
	select {
		text-transform: none
	}

	select {
		word-wrap: normal
	}

	[type=button],
	[type=reset],
	[type=submit],
	button {
		-webkit-appearance: button
	}

	[type=button]:not(:disabled),
	[type=reset]:not(:disabled),
	[type=submit]:not(:disabled),
	button:not(:disabled) {
		cursor: pointer
	}

	[type=button]::-moz-focus-inner,
	[type=reset]::-moz-focus-inner,
	[type=submit]::-moz-focus-inner,
	button::-moz-focus-inner {
		padding: 0;
		border-style: none
	}

	input[type=checkbox],
	input[type=radio] {
		box-sizing: border-box;
		padding: 0
	}

	input[type=date],
	input[type=datetime-local],
	input[type=month],
	input[type=time] {
		-webkit-appearance: listbox
	}

	textarea {
		overflow: auto;
		resize: vertical
	}

	fieldset {
		min-width: 0;
		padding: 0;
		margin: 0;
		border: 0
	}

	legend {
		display: block;
		width: 100%;
		max-width: 100%;
		padding: 0;
		margin-bottom: .5rem;
		font-size: 1.5rem;
		line-height: inherit;
		color: inherit;
		white-space: normal
	}

	progress {
		vertical-align: baseline
	}

	[type=number]::-webkit-inner-spin-button,
	[type=number]::-webkit-outer-spin-button {
		height: auto
	}

	[type=search] {
		outline-offset: -2px;
		-webkit-appearance: none
	}

	[type=search]::-webkit-search-decoration {
		-webkit-appearance: none
	}

	::-webkit-file-upload-button {
		font: inherit;
		-webkit-appearance: button
	}

	output {
		display: inline-block
	}

	summary {
		display: list-item;
		cursor: pointer
	}

	template {
		display: none
	}

	[hidden] {
		display: none !important
	}

	.h1,
	.h2,
	.h3,
	.h4,
	.h5,
	.h6,
	h1,
	h2,
	h3,
	h4,
	h5,
	h6 {
		margin-bottom: .5rem;
		font-weight: 500;
		line-height: 1.2
	}

	.h1,
	h1 {
		font-size: 2.5rem
	}

	.h2,
	h2 {
		font-size: 2rem
	}

	.h3,
	h3 {
		font-size: 1.75rem
	}

	.h4,
	h4 {
		font-size: 1.5rem
	}

	.h5,
	h5 {
		font-size: 1.25rem
	}

	.h6,
	h6 {
		font-size: 1rem
	}

	.lead {
		font-size: 1.25rem;
		font-weight: 300
	}

	.display-1 {
		font-size: 6rem;
		font-weight: 300;
		line-height: 1.2
	}

	.display-2 {
		font-size: 5.5rem;
		font-weight: 300;
		line-height: 1.2
	}

	.display-3 {
		font-size: 4.5rem;
		font-weight: 300;
		line-height: 1.2
	}

	.display-4 {
		font-size: 3.5rem;
		font-weight: 300;
		line-height: 1.2
	}

	hr {
		margin-top: 1rem;
		margin-bottom: 1rem;
		border: 0;
		border-top: 1px solid rgba(0, 0, 0, .1)
	}

	.small,
	small {
		font-size: 80%;
		font-weight: 400
	}

	.mark,
	mark {
		padding: .2em;
		background-color: #fcf8e3
	}

	.list-unstyled {
		padding-left: 0;
		list-style: none
	}

	.list-inline {
		padding-left: 0;
		list-style: none
	}

	.list-inline-item {
		display: inline-block
	}

	.list-inline-item:not(:last-child) {
		margin-right: .5rem
	}

	.initialism {
		font-size: 90%;
		text-transform: uppercase
	}

	.blockquote {
		margin-bottom: 1rem;
		font-size: 1.25rem
	}

	.blockquote-footer {
		display: block;
		font-size: 80%;
		color: #6c757d
	}

	.blockquote-footer::before {
		content: "\2014\00A0"
	}

	.img-fluid {
		max-width: 100%;
		height: auto
	}

	.img-thumbnail {
		padding: .25rem;
		background-color: #fff;
		border: 1px solid #dee2e6;
		border-radius: .25rem;
		max-width: 100%;
		height: auto
	}

	.figure {
		display: inline-block
	}

	.figure-img {
		margin-bottom: .5rem;
		line-height: 1
	}

	.figure-caption {
		font-size: 90%;
		color: #6c757d
	}

	code {
		font-size: 87.5%;
		color: #e83e8c;
		word-wrap: break-word
	}

	a>code {
		color: inherit
	}

	kbd {
		padding: .2rem .4rem;
		font-size: 87.5%;
		color: #fff;
		background-color: #212529;
		border-radius: .2rem
	}

	kbd kbd {
		padding: 0;
		font-size: 100%;
		font-weight: 700
	}

	pre {
		display: block;
		font-size: 87.5%;
		color: #212529
	}

	pre code {
		font-size: inherit;
		color: inherit;
		word-break: normal
	}

	.pre-scrollable {
		max-height: 340px;
		overflow-y: scroll
	}

	.container {
		width: 100%;
		padding-right: 15px;
		padding-left: 15px;
		margin-right: auto;
		margin-left: auto
	}



	body {
		margin: 0;
		padding: 1.5cm 1.5cm 1cm 1cm;
	}

	.wrapper {
		/* border: 2px dotted rgba(0, 0, 0, 0.308); */
	}

	.row {
		display: -ms-flexbox;
		display: flex;
		-ms-flex-wrap: wrap;
		flex-wrap: wrap;
		margin-right: -15px;
		margin-left: -15px
	}

	.col,
	.col-1,
	.col-10,
	.col-11,
	.col-12,
	.col-2,
	.col-3,
	.col-4,
	.col-5,
	.col-6,
	.col-7,
	.col-8,
	.col-9,
	.col-auto {
		position: relative;
		width: 100%;
		padding-right: 15px;
		padding-left: 15px
	}

	.col-auto {
		-ms-flex: 0 0 auto;
		flex: 0 0 auto;
		width: auto;
		max-width: 100%
	}

	.col-1 {
		-ms-flex: 0 0 8.333333%;
		flex: 0 0 8.333333%;
		max-width: 8.333333%
	}

	.col-2 {
		-ms-flex: 0 0 16.666667%;
		flex: 0 0 16.666667%;
		max-width: 16.666667%
	}

	.col-3 {
		-ms-flex: 0 0 25%;
		flex: 0 0 25%;
		max-width: 25%
	}

	.col-4 {
		-ms-flex: 0 0 33.333333%;
		flex: 0 0 33.333333%;
		max-width: 33.333333%
	}

	.col-5 {
		-ms-flex: 0 0 41.666667%;
		flex: 0 0 41.666667%;
		max-width: 41.666667%
	}

	.col-6 {
		-ms-flex: 0 0 50%;
		flex: 0 0 50%;
		max-width: 50%
	}

	.col-7 {
		-ms-flex: 0 0 58.333333%;
		flex: 0 0 58.333333%;
		max-width: 58.333333%
	}

	.col-8 {
		-ms-flex: 0 0 66.666667%;
		flex: 0 0 66.666667%;
		max-width: 66.666667%
	}

	.col-9 {
		-ms-flex: 0 0 75%;
		flex: 0 0 75%;
		max-width: 75%
	}

	.col-10 {
		-ms-flex: 0 0 83.333333%;
		flex: 0 0 83.333333%;
		max-width: 83.333333%
	}

	.col-11 {
		-ms-flex: 0 0 91.666667%;
		flex: 0 0 91.666667%;
		max-width: 91.666667%
	}

	.col-12 {
		-ms-flex: 0 0 100%;
		flex: 0 0 100%;
		max-width: 100%
	}

	.text-left {
		text-align: left !important
	}

	.text-right {
		text-align: right !important
	}

	.text-center {
		text-align: center !important
	}

	.m-0 {
		margin: 0 !important
	}

	.mt-0,
	.my-0 {
		margin-top: 0 !important
	}

	.mr-0,
	.mx-0 {
		margin-right: 0 !important
	}

	.mb-0,
	.my-0 {
		margin-bottom: 0 !important
	}

	.ml-0,
	.mx-0 {
		margin-left: 0 !important
	}

	.m-1 {
		margin: .25rem !important
	}

	.mt-1,
	.my-1 {
		margin-top: .25rem !important
	}

	.mr-1,
	.mx-1 {
		margin-right: .25rem !important
	}

	.mb-1,
	.my-1 {
		margin-bottom: .25rem !important
	}

	.ml-1,
	.mx-1 {
		margin-left: .25rem !important
	}

	.m-2 {
		margin: .5rem !important
	}

	.mt-2,
	.my-2 {
		margin-top: .5rem !important
	}

	.mr-2,
	.mx-2 {
		margin-right: .5rem !important
	}

	.mb-2,
	.my-2 {
		margin-bottom: .5rem !important
	}

	.ml-2,
	.mx-2 {
		margin-left: .5rem !important
	}

	.m-3 {
		margin: 1rem !important
	}

	.mt-3,
	.my-3 {
		margin-top: 1rem !important
	}

	.mr-3,
	.mx-3 {
		margin-right: 1rem !important
	}

	.mb-3,
	.my-3 {
		margin-bottom: 1rem !important
	}

	.ml-3,
	.mx-3 {
		margin-left: 1rem !important
	}

	.m-4 {
		margin: 1.5rem !important
	}

	.mt-4,
	.my-4 {
		margin-top: 1.5rem !important
	}

	.mr-4,
	.mx-4 {
		margin-right: 1.5rem !important
	}

	.mb-4,
	.my-4 {
		margin-bottom: 1.5rem !important
	}

	.ml-4,
	.mx-4 {
		margin-left: 1.5rem !important
	}

	.m-5 {
		margin: 3rem !important
	}

	.mt-5,
	.my-5 {
		margin-top: 3rem !important
	}

	.mr-5,
	.mx-5 {
		margin-right: 3rem !important
	}

	.mb-5,
	.my-5 {
		margin-bottom: 3rem !important
	}

	.ml-5,
	.mx-5 {
		margin-left: 3rem !important
	}

	.p-0 {
		padding: 0 !important
	}

	.pt-0,
	.py-0 {
		padding-top: 0 !important
	}

	.pr-0,
	.px-0 {
		padding-right: 0 !important
	}

	.pb-0,
	.py-0 {
		padding-bottom: 0 !important
	}

	.pl-0,
	.px-0 {
		padding-left: 0 !important
	}

	.p-1 {
		padding: .25rem !important
	}

	.pt-1,
	.py-1 {
		padding-top: .25rem !important
	}

	.pr-1,
	.px-1 {
		padding-right: .25rem !important
	}

	.pb-1,
	.py-1 {
		padding-bottom: .25rem !important
	}

	.pl-1,
	.px-1 {
		padding-left: .25rem !important
	}

	.p-2 {
		padding: .5rem !important
	}

	.pt-2,
	.py-2 {
		padding-top: .5rem !important
	}

	.pr-2,
	.px-2 {
		padding-right: .5rem !important
	}

	.pb-2,
	.py-2 {
		padding-bottom: .5rem !important
	}

	.pl-2,
	.px-2 {
		padding-left: .5rem !important
	}

	.p-3 {
		padding: 1rem !important
	}

	.pt-3,
	.py-3 {
		padding-top: 1rem !important
	}

	.pr-3,
	.px-3 {
		padding-right: 1rem !important
	}

	.pb-3,
	.py-3 {
		padding-bottom: 1rem !important
	}

	.pl-3,
	.px-3 {
		padding-left: 1rem !important
	}

	.p-4 {
		padding: 1.5rem !important
	}

	.pt-4,
	.py-4 {
		padding-top: 1.5rem !important
	}

	.pr-4,
	.px-4 {
		padding-right: 1.5rem !important
	}

	.pb-4,
	.py-4 {
		padding-bottom: 1.5rem !important
	}

	.pl-4,
	.px-4 {
		padding-left: 1.5rem !important
	}

	.p-5 {
		padding: 3rem !important
	}

	.pt-5,
	.py-5 {
		padding-top: 3rem !important
	}

	.pr-5,
	.px-5 {
		padding-right: 3rem !important
	}

	.pb-5,
	.py-5 {
		padding-bottom: 3rem !important
	}

	.pl-5,
	.px-5 {
		padding-left: 3rem !important
	}

	.m-n1 {
		margin: -.25rem !important
	}

	.mt-n1,
	.my-n1 {
		margin-top: -.25rem !important
	}

	.mr-n1,
	.mx-n1 {
		margin-right: -.25rem !important
	}

	.mb-n1,
	.my-n1 {
		margin-bottom: -.25rem !important
	}

	.ml-n1,
	.mx-n1 {
		margin-left: -.25rem !important
	}

	.m-n2 {
		margin: -.5rem !important
	}

	.mt-n2,
	.my-n2 {
		margin-top: -.5rem !important
	}

	.mr-n2,
	.mx-n2 {
		margin-right: -.5rem !important
	}

	.mb-n2,
	.my-n2 {
		margin-bottom: -.5rem !important
	}

	.ml-n2,
	.mx-n2 {
		margin-left: -.5rem !important
	}

	.m-n3 {
		margin: -1rem !important
	}

	.mt-n3,
	.my-n3 {
		margin-top: -1rem !important
	}

	.mr-n3,
	.mx-n3 {
		margin-right: -1rem !important
	}

	.mb-n3,
	.my-n3 {
		margin-bottom: -1rem !important
	}

	.ml-n3,
	.mx-n3 {
		margin-left: -1rem !important
	}

	.m-n4 {
		margin: -1.5rem !important
	}

	.mt-n4,
	.my-n4 {
		margin-top: -1.5rem !important
	}

	.mr-n4,
	.mx-n4 {
		margin-right: -1.5rem !important
	}

	.mb-n4,
	.my-n4 {
		margin-bottom: -1.5rem !important
	}

	.ml-n4,
	.mx-n4 {
		margin-left: -1.5rem !important
	}

	.m-n5 {
		margin: -3rem !important
	}

	.mt-n5,
	.my-n5 {
		margin-top: -3rem !important
	}

	.mr-n5,
	.mx-n5 {
		margin-right: -3rem !important
	}

	.mb-n5,
	.my-n5 {
		margin-bottom: -3rem !important
	}

	.ml-n5,
	.mx-n5 {
		margin-left: -3rem !important
	}

	.m-auto {
		margin: auto !important
	}

	.mt-auto,
	.my-auto {
		margin-top: auto !important
	}

	.mr-auto,
	.mx-auto {
		margin-right: auto !important
	}

	.mb-auto,
	.my-auto {
		margin-bottom: auto !important
	}

	.ml-auto,
	.mx-auto {
		margin-left: auto !important
	}

	.w-25 {
		width: 25% !important
	}

	.w-50 {
		width: 50% !important
	}

	.w-75 {
		width: 75% !important
	}

	.w-100 {
		width: 100% !important
	}

	.w-auto {
		width: auto !important
	}

	.h-25 {
		height: 25% !important
	}

	.h-50 {
		height: 50% !important
	}

	.h-75 {
		height: 75% !important
	}

	.h-100 {
		height: 100% !important
	}

	.h-auto {
		height: auto !important
	}

	.text-lowercase {
		text-transform: lowercase !important
	}

	.text-uppercase {
		text-transform: uppercase !important
	}

	.text-capitalize {
		text-transform: capitalize !important
	}

	.font-weight-light {
		font-weight: 300 !important
	}

	.font-weight-lighter {
		font-weight: lighter !important
	}

	.font-weight-normal {
		font-weight: 400 !important
	}

	.font-weight-bold {
		font-weight: 700 !important
	}

	.font-weight-bolder {
		font-weight: bolder !important
	}

	.font-italic {
		font-style: italic !important
	}

	.text-white {
		color: #fff !important
	}

	.text-primary {
		color: #007bff !important
	}

	a.text-primary:focus,
	a.text-primary:hover {
		color: #0056b3 !important
	}

	.text-secondary {
		color: #6c757d !important
	}

	a.text-secondary:focus,
	a.text-secondary:hover {
		color: #494f54 !important
	}

	.text-success {
		color: #28a745 !important
	}

	a.text-success:focus,
	a.text-success:hover {
		color: #19692c !important
	}

	.text-info {
		color: #17a2b8 !important
	}

	a.text-info:focus,
	a.text-info:hover {
		color: #0f6674 !important
	}

	.text-warning {
		color: #ffc107 !important
	}

	a.text-warning:focus,
	a.text-warning:hover {
		color: #ba8b00 !important
	}

	.text-danger {
		color: #dc3545 !important
	}

	a.text-danger:focus,
	a.text-danger:hover {
		color: #a71d2a !important
	}

	.text-light {
		color: #f8f9fa !important
	}

	a.text-light:focus,
	a.text-light:hover {
		color: #cbd3da !important
	}

	.text-dark {
		color: #343a40 !important
	}

	a.text-dark:focus,
	a.text-dark:hover {
		color: #121416 !important
	}

	.text-body {
		color: #212529 !important
	}

	.text-muted {
		color: #6c757d !important
	}

	.text-black-50 {
		color: rgba(0, 0, 0, .5) !important
	}

	.text-white-50 {
		color: rgba(255, 255, 255, .5) !important
	}

	.text-hide {
		font: 0/0 a;
		color: transparent;
		text-shadow: none;
		background-color: transparent;
		border: 0
	}

	.text-decoration-none {
		text-decoration: none !important
	}

	.text-break {
		word-break: break-word !important;
		overflow-wrap: break-word !important
	}

	.text-reset {
		color: inherit !important
	}

	.visible {
		visibility: visible !important
	}

	.invisible {
		visibility: hidden !important
	}

	.embed-responsive {
		position: relative;
		display: block;
		width: 100%;
		padding: 0;
		overflow: hidden
	}

	.embed-responsive::before {
		display: block;
		content: ""
	}

	.embed-responsive .embed-responsive-item,
	.embed-responsive embed,
	.embed-responsive iframe,
	.embed-responsive object,
	.embed-responsive video {
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		width: 100%;
		height: 100%;
		border: 0
	}

	.embed-responsive-21by9::before {
		padding-top: 42.857143%
	}

	.embed-responsive-16by9::before {
		padding-top: 56.25%
	}

	.embed-responsive-4by3::before {
		padding-top: 75%
	}

	.embed-responsive-1by1::before {
		padding-top: 100%
	}

	.flex-row {
		-ms-flex-direction: row !important;
		flex-direction: row !important
	}

	.flex-column {
		-ms-flex-direction: column !important;
		flex-direction: column !important
	}

	.flex-row-reverse {
		-ms-flex-direction: row-reverse !important;
		flex-direction: row-reverse !important
	}

	.flex-column-reverse {
		-ms-flex-direction: column-reverse !important;
		flex-direction: column-reverse !important
	}

	.flex-wrap {
		-ms-flex-wrap: wrap !important;
		flex-wrap: wrap !important
	}

	.flex-nowrap {
		-ms-flex-wrap: nowrap !important;
		flex-wrap: nowrap !important
	}

	.flex-wrap-reverse {
		-ms-flex-wrap: wrap-reverse !important;
		flex-wrap: wrap-reverse !important
	}

	.flex-fill {
		-ms-flex: 1 1 auto !important;
		flex: 1 1 auto !important
	}

	.flex-grow-0 {
		-ms-flex-positive: 0 !important;
		flex-grow: 0 !important
	}

	.flex-grow-1 {
		-ms-flex-positive: 1 !important;
		flex-grow: 1 !important
	}

	.flex-shrink-0 {
		-ms-flex-negative: 0 !important;
		flex-shrink: 0 !important
	}

	.flex-shrink-1 {
		-ms-flex-negative: 1 !important;
		flex-shrink: 1 !important
	}

	.justify-content-start {
		-ms-flex-pack: start !important;
		justify-content: flex-start !important
	}

	.justify-content-end {
		-ms-flex-pack: end !important;
		justify-content: flex-end !important
	}

	.justify-content-center {
		-ms-flex-pack: center !important;
		justify-content: center !important
	}

	.justify-content-between {
		-ms-flex-pack: justify !important;
		justify-content: space-between !important
	}

	.justify-content-around {
		-ms-flex-pack: distribute !important;
		justify-content: space-around !important
	}

	.align-items-start {
		-ms-flex-align: start !important;
		align-items: flex-start !important
	}

	.align-items-end {
		-ms-flex-align: end !important;
		align-items: flex-end !important
	}

	.align-items-center {
		-ms-flex-align: center !important;
		align-items: center !important
	}

	.align-items-baseline {
		-ms-flex-align: baseline !important;
		align-items: baseline !important
	}

	.align-items-stretch {
		-ms-flex-align: stretch !important;
		align-items: stretch !important
	}

	.align-content-start {
		-ms-flex-line-pack: start !important;
		align-content: flex-start !important
	}

	.align-content-end {
		-ms-flex-line-pack: end !important;
		align-content: flex-end !important
	}

	.align-content-center {
		-ms-flex-line-pack: center !important;
		align-content: center !important
	}

	.align-content-between {
		-ms-flex-line-pack: justify !important;
		align-content: space-between !important
	}

	.align-content-around {
		-ms-flex-line-pack: distribute !important;
		align-content: space-around !important
	}

	.align-content-stretch {
		-ms-flex-line-pack: stretch !important;
		align-content: stretch !important
	}

	.align-self-auto {
		-ms-flex-item-align: auto !important;
		align-self: auto !important
	}

	.align-self-start {
		-ms-flex-item-align: start !important;
		align-self: flex-start !important
	}

	.align-self-end {
		-ms-flex-item-align: end !important;
		align-self: flex-end !important
	}

	.align-self-center {
		-ms-flex-item-align: center !important;
		align-self: center !important
	}

	.align-self-baseline {
		-ms-flex-item-align: baseline !important;
		align-self: baseline !important
	}

	.align-self-stretch {
		-ms-flex-item-align: stretch !important;
		align-self: stretch !important
	}


	.judul {
		font-size: 22pt;
	}

	.judul-list {
		font-size: 15pt;
	}

	.ket-pemesan {
		font-weight: 600;
		font-size: 12pt;
	}

	.pemesan {
		color: #252525;
		font-size: 12pt;
		font-style: italic;
	}

	.lh-1 {
		line-height: 1;
	}

	.border-bold {
		border-width: 5px;
	}

	.border-dotted {
		border-style: dashed;
		border-width: 1px;
		border-color: #d0d0d0;
	}

	.top-head {
		/* background: #0000001a;
      color: black; */
		background: #535353;
		color: white;
	}

	.offset-1 {
		margin-left: 8.333333%
	}

	.offset-2 {
		margin-left: 16.666667%
	}

	.offset-3 {
		margin-left: 25%
	}

	.offset-4 {
		margin-left: 33.333333%
	}

	.offset-5 {
		margin-left: 41.666667%
	}

	.offset-6 {
		margin-left: 50%
	}

	.offset-7 {
		margin-left: 58.333333%
	}

	.offset-8 {
		margin-left: 66.666667%
	}

	.offset-9 {
		margin-left: 75%
	}

	.offset-10 {
		margin-left: 83.333333%
	}

	.offset-11 {
		margin-left: 91.666667%
	}

	.table-preview,
	.th,
	.td {
		border: 1px solid #dcdfe3;
		padding: 3px;
	}

	.td-center {
		border: 1px solid #dcdfe3;
		text-align: center;
	}

	.td-right {
		border: 1px solid #dcdfe3;
		text-align: right;
	}

	table,
	td,
	p {
		font-size: 10pt !important;
	}
</style>

<!-- Style Print PDF -->
<style>
	.head-logo img {
		width: 200px;
	}

	.table {
		width: 100%;
	}

	.table-checking th,
	.table-checking td,
	.table-content th,
	.table-content td,
	.table-bordered th,
	.table-bordered td {
		border: 1px solid #000;
		wordwrap: break-word;
	}

	.no-pdf {
		text-align: center;
	}

	.no-pdf .number {
		font-size: 16px;
	}

	.text-center {
		text-align: center;
	}

	.head-title {
		font-size: 16px;
		font-weight: bold;
		border: 1px solid #000;
		padding: 5px;
	}

	.head-title span {
		line-height: 1.5;
	}

	.safety-wrap > * {
		margin-top: 0 !important;
		margin-bottom: 0 !important;
	}

	.user-info h4,
	.reason-info h4 {
		font-size: 14px;
	}
</style>

<page orientation="portrait" format="F4" backtop="0mm" backbottom="0mm" backleft="0mm" backright="0mm">

	<!-- <div class="head-logo">
		<img src="https://egov.phicos.co.id/peruri/vm/assets/img/perum-peruri-kebayoran-baru-state-owned-enterprise.png" alt="">
	</div> -->

	<div class="">
		<table class="table head-table">
			<tbody>
				<tr>
					<td colspan="5">
						<h5 class="no-pdf mb-3">No. <?= $nomor_surat?></h5>
					</td>
				</tr>
				<tr>
					<td style="width: 36%;"></td>
					<td style="width: 28%;">
						<div class="head-title text-center">
							<span>Surat Pengantar Barang <br> Intern</span>
						</div>
					</td>
					<td style="width: 18%;"></td>
					<td style="width: 14%;">
						<div class="safety-wrap text-center">
							<h6>Lembar I</h6>
							<hr>
							<?php if(@$this->session->userdata('grup')!=7){
								echo "<h6>Arsip</h6>";
							}?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="user-info">
		<table class="table">
			<tbody>
				<tr>
					<td style="width: 50%;">
						<h5>Nama : <?= @$nama?></h5>
					</td>
					<td style="width: 50%;">
						<h5>NP : <?= @$np_karyawan?></h5>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="width: 100%;">
						<h5>Seksi : <?= @$nama_unit?></h5>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="width: 100%;">
						<h5>Diizinkan / diperintahkan mengeluarkan barang produksi / permesinan / barang lainnya *)</h5>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="width: 100%;">
						<h5>Milik : <?= @$milik?></h5>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="table-info" style="margin-bottom: 5px;">
		<table class="table table-content">
			<tbody>
				<tr>
					<td style="width: 5%;">
						<h5 class="text-center">NO.</h5>
					</td>
					<td style="width: 30%;">
						<h5 class="text-center">JUMLAH <br> (ANGKA DAN HURUF)</h5>
					</td>
					<td style="width: 40%;">
						<h5 class="text-center">NAMA BARANG DAN SPESIFIKASINYA</h5>
					</td>
					<td style="width: 25%;">
						<h5 class="text-center">KETERANGAN</h5>
					</td>
				</tr>
				<?php
				$barang_array = json_decode($barang);
				$no=0;
				foreach ($barang_array as $key) {
					$no++;
					echo "<tr>";
						echo "<td>{$no}</td>";
						echo "<td>{$key->jumlah}</td>";
						echo "<td>". wordwrap($key->nama_barang, 45, '<br>', true). "</td>";
						echo "<td>". wordwrap($key->keterangan, 30, '<br>', true). "</td>";
					echo "</tr>";
				}
				?>
				<!-- <tr>
					<td style="height: 150px; width: 5%;">
					</td>
					<td style="height: 150px; width: 30%;">
					</td>
					<td style="height: 150px; width: 45%;">
					</td>
					<td style="height: 150px; width: 20%;">
					</td>
				</tr> -->
			</tbody>
		</table>
	</div>

	<div class="reason-info">
		<table class="table table-reason">
			<tbody>
				<tr>
					<td style="width: 33.334%">
						<h5>Maksud : <?= @$maksud?></h5>
					</td>
					<td style="width: 38.334%"></td>
					<td style="width: 28.334%">
						<h5 class="text-center"><?= @$lokasi_ttd;?>, <?= @$approval_atasan_updated_at ? tanggal_indonesia(date('Y-m-d', strtotime($approval_atasan_updated_at))):'.............................'?></h5>
					</td>
				</tr>
				<tr>
					<td style="width: 33.334%">
						<h5>Dikirim ke : <?= @$dikirim_ke?></h5>
					</td>
					<td style="width: 38.334%"></td>
					<td style="width: 28.334%">
						<h5 class="text-center">KASEK / KADEP / KADIV *)</h5>
					</td>
				</tr>
				<tr>
					<td style="width: 33.334%">
						<h5>Keluar Tanggal : <?= @$keluar_tanggal ? tanggal_indonesia($keluar_tanggal):''?></h5>
					</td>
					<td style="width: 38.334%"></td>
					<td style="width: 28.334%">
						<?php $img = 'https://api.qrserver.com/v1/create-qr-code/?data='.$uuid.'&amp;size=25x25';?>
					</td>
				</tr>
				<tr>
					<td style="width: 33.334%"></td>
					<td style="width: 38.334%">
						<h5 class="text-center">CAP POS KO</h5>
					</td>
					<td style="width: 28.334%">
						<h5 class="text-center">( <?= @$approval_atasan_nama?> )</h5>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="checking-info" style="margin-bottom: 5px;">
		<h5>1. Pemeriksaan waktu keluar Perusahaan.</h5>
		<table class="table table-checking">
			<tbody>
				<tr>
					<td style="padding-top: 5px; width: 25%">
						<h5 class="text-center">PENGECEK</h5>
					</td>
					<td style="padding-top: 5px; width: 25%">
						<h5 class="text-center">PENGAWAS / PENGAWAL / PENYEGEL *)</h5>
					</td>
					<td style="padding-top: 5px; width: 25%">
						<h5 class="text-center">DAN / WADAN POS KO *)</h5>
					</td>
					<td style="padding-top: 5px; width: 25%">
						<h5 class="text-center">POS I</h5>
					</td>
				</tr>
				<tr>
					<td style="padding-top: 10px; width: 25%">
						<div class="text-center">
							<h5>(<?= @$pengecek1_nama?:'............................................'?>)</h5>
							<h5>NP. <?= @$pengecek1_np?:'............................................'?></h5>
							<h5>TGL. <?= @$pengecek1_tanggal ? tanggal_indonesia($pengecek1_tanggal):'............................................'?></h5>
							<h5>JAM. <?= @$pengecek1_jam ? date('H:i',strtotime($pengecek1_jam)):'............................................'?></h5>
						</div>
					</td>
					<td style="padding-top: 10px; width: 25%">
						<div class="text-center">
							<h5>(<?= @$konfirmasi_pengguna_nama?>)</h5>
							<h5>NP. <?= @$konfirmasi_pengguna_np?></h5>
							<h5>TGL. <?= @$konfirmasi_pengguna_tanggal ? tanggal_indonesia($konfirmasi_pengguna_tanggal):'............................................'?></h5>
							<h5>JAM. <?= @$konfirmasi_pengguna_jam ? date('H:i',strtotime($konfirmasi_pengguna_jam)):'............................................'?></h5>
						</div>
					</td>
					<td style="padding-top: 10px; width: 25%">
						<div class="text-center">
							<h5>(<?= @$danposko_nama?:'............................................'?>)</h5>
							<h5>NP. <?= @$danposko_np?:'............................................'?></h5>
							<h5>TGL. <?= @$danposko_tanggal ? tanggal_indonesia($danposko_tanggal):'............................................'?></h5>
							<h5>JAM. <?= @$danposko_jam ? date('H:i',strtotime($danposko_jam)):'............................................'?></h5>
						</div>
					</td>
					<td style="padding-top: 10px; width: 25%">
						<div class="text-center">
							<h5>(<?= @$last_keluar['approval_nama']?:'............................................'?>)</h5>
							<h5>NP. <?= @$last_keluar['approval_np']?:'............................................'?></h5>
							<h5>TGL. <?= @$last_keluar['tanggal'] ? tanggal_indonesia($last_keluar['tanggal']):'............................................'?></h5>
							<h5>JAM. <?= @$last_keluar['jam'] ? date('H:i',strtotime($last_keluar['jam'])):'............................................'?></h5>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="checking-info" style="margin-bottom: 5px;">
		<h5>2. Pemeriksaan waktu tiba di lokasi tujuan</h5>
		<table class="table table-checking">
			<tbody>
				<tr>
					<td style="padding-top: 5px; width: 25%">
						<h5 class="text-center">PENGECEK</h5>
					</td>
					<td style="padding-top: 5px; width: 25%">
						<h5 class="text-center">PEMBAWA BARANG</h5>
					</td>
					<td colspan="2" style="padding-top: 5px; width: 50%">
						<?php
						if( @$last_masuk['barang_sesuai'] ){
							switch ($last_masuk['barang_sesuai']) {
								case '1':
									echo "<h5 class=\"text-center\">TELAH DICEK SESUAI / <del>TIDAK SESUAI</del> *)</h5>";
									break;
								case '2':
									echo "<h5 class=\"text-center\">TELAH DICEK <del>SESUAI</del> / TIDAK SESUAI *)</h5>";
									break;
								default:
									echo "<h5 class=\"text-center\">TELAH DICEK SESUAI / TIDAK SESUAI *)</h5>";
									break;
							}
						} else{
							echo "<h5 class=\"text-center\">TELAH DICEK SESUAI / TIDAK SESUAI *)</h5>";
						}
						?>
					</td>
				</tr>
				<tr>
					<td rowspan="2" style="padding-top: 10px; width: 25%">
						<div class="text-center">
							<h5>(<?= @$last_masuk['approval_nama']?:'............................................'?>)</h5>
							<h5>NP. <?= @$last_masuk['approval_np']?:'............................................'?></h5>
							<h5>TGL. <?= @$last_masuk['tanggal'] ? tanggal_indonesia($last_masuk['tanggal']):'............................................'?></h5>
							<h5>JAM. <?= @$last_masuk['jam'] ? date('H:i',strtotime($last_masuk['jam'])):'............................................'?></h5>
						</div>
					</td>
					<td rowspan="2" style="padding-top: 10px; width: 25%">
						<div class="text-center">
							<h5>(<?= @$konfirmasi_pengguna_nama?>)</h5>
							<h5>NP. <?= @$konfirmasi_pengguna_np?></h5>
							<h5>TGL. <?= @$konfirmasi_pembawa_tanggal ? tanggal_indonesia($konfirmasi_pembawa_tanggal):'............................................'?></h5>
							<h5>JAM. <?= @$konfirmasi_pembawa_jam ? date('H:i',strtotime($konfirmasi_pembawa_jam)):'............................................'?></h5>
						</div>
					</td>
					<td style="padding-top: 10px; width: 25%">
						<h5 class="text-center">KURANG / LEBIH *)</h5>
					</td>
					<td style="padding-top: 10px; width: 25%">
						<h5 class="text-center">RUSAK</h5>
					</td>
				</tr>
				<tr>
					<td style="height: 70px; width: 25%;">
						<?php
						echo "<ol>";
						foreach ($last_kondisi_barang as $key) {
							if( in_array($key['kondisi'],['kurang','lebih']) ){
								echo '<li>' . wordwrap("{$key['nama_barang']}<br>{$key['keterangan']}", 24, '<br>', true) . '</li>';
							}
						}
						echo "</ol>";
						?>
					</td>
					<td style="height: 70px; width: 25%;">
						<?php
						echo "<ol>";
						foreach ($last_kondisi_barang as $key) {
							if( in_array($key['kondisi'],['rusak']) ){
								echo '<li>' . wordwrap("{$key['nama_barang']}<br>{$key['keterangan']}", 24, '<br>', true) . '</li>';
							}
						}
						echo "</ol>";
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="notes">
		<h5>Catatan :</h5>
		<ol>
			<li>*) Coret yang tidak perlu.</li>
			<li>Masa berlaku pengeluaran barang 3 hari sejak ditanda tangani.</li>
			<li>Lembar kedua sebagai pengantar bagi barang ke Pos. I lokasi tujuan.</li>
		</ol>
	</div>
</page>