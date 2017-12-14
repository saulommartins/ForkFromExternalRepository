<script type="text/javascript">
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
</script>
// Esta função "chama" todas as funções usadas em um documento.
function init() {
	createExternalLinks();
}











// FORÇA LINKS A ABRIREM EM UMA NOVA JANELA
//******************************************************************************
// A W3C não aceita mais o atributo "target" em links (tag <a>) em doctypes XHTML 1.0 Strict
// A função abaixo faz com que todos os links que possuem rel="externo" no link, abram em outra janela conforme no exemplo abaixo:
// EXEMPLO:
//******************************************************************************
//<a href="index.php" title="Link de Exemplo" rel="externo">

/* Para chamar a função, insira no body  == <script type="text/javascript">createExternalLinks();</script>  */

function createExternalLinks() {
    if(document.getElementsByTagName) {
        var anchors = document.getElementsByTagName('a');
        for(var i=0; i<anchors.length; i++) {
            var anchor = anchors[i];
            if(anchor.getAttribute("href") && anchor.getAttribute('rel')=='externo') { // <-- É necessário inserir rel="externo" no link
                anchor.target = '_blank';
                var title = anchor.title + ' (Este link abre uma nova janela)'; // <-- Insere este texto no final do Title do link
                anchor.title = title;
            }
        }
    }
}

//........................................................................
//........................................................................















// Esta função "chama" todas as funções usadas em um documento.
//========================================================
// Event Listener by Scott Andrew - http://scottandrew.com
// edited by Mark Wubben, <useCapture> is now set to false
//http://simon.incutio.com/archive/2004/05/26/addLoadEvent
//========================================================
function addEvent(obj, evType, fn){
    if(obj.addEventListener){
        obj.addEventListener(evType, fn, false);

        return true;

    } else if (obj.attachEvent){
        var r = obj.attachEvent('on'+evType, fn);

        return r;

    } else {
        return false;

    }
}

addEvent(window, "load", init);
