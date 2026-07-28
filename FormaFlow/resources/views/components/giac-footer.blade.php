<style>
    /*
       Styles combined from both files.
       You can keep this inline within the component for PDF generators like DomPDF,
       or move it to your main stylesheet.
    */
    .giac-footer {
        position: fixed;
        bottom: -70px;
        left: -90px;
        right: -90px;
        text-align: center;
        font-family: 'Times New Roman', Times, serif;
    }

    /* The custom double border from B-1 */
    .pink-divider {
        border-top: 3px solid #622423;
        border-bottom: 1px solid #622423;
        height: 0;
        margin-bottom: 3px;
        padding-top: 1px;
    }

    /* Typography from B-2 */
    .giac-footer .handwritten-text {
        font-family: 'Lucida Handwriting', cursive;
        font-style: italic;
    }

    .giac-footer .footer-line2 {
        font-weight: bold;
        font-style: italic;
        font-size: 12px;
        margin-top: 2px;
    }

    .giac-footer .field-line {
        margin: 5px 30px; /* Included for structural safety if not globally defined */
    }

    .giac-footer a {
        color: #0563C1;
        text-decoration: underline;
    }
</style>

<div class="giac-footer">
    <!-- Top brown line with two borders (one large, one small) from B-1 -->
    <div class="pink-divider"></div>

    <!-- Footer structure and text from B-2 -->
    <div class="field-line">
        <span class="handwritten-text">GIAC Technologies</span>
        <span class="footer-line2">- 2 Rue Abou Said Assoussi, Résidence El Fariss, 1<sup>er</sup> étage, Appartement n° 9, Casablanca</span>
    </div>

    <div class="field-line footer-line2">
        Tél. : 0522 27 24 93 &ndash; Fax : 0522 27 57 65 &ndash; CNSS : 7365514 &ndash; e-mail :
        <a href="mailto:giactechnologies@gmail.com">giactechnologies@gmail.com</a>
        &ndash; web : <a href="http://www.giactechnologies.com">www.giactechnologies.com</a>
    </div>
</div>
