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
<?php
/**
* Arquivo de funções javascript para a PopUp de documentos
* Data de Criação: 18/10/2006


* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 17308 $
$Name$
$Author: cassiano $
$Date: 2006-10-31 09:01:39 -0300 (Ter, 31 Out 2006) $

Casos de uso: uc-01.06.98
*/
?>
<script type='text/javascript'>

function executaFuncao(stCtrl, stParametros ){
    var stId = 'listaArquivos';
    var stPagina = '<?=$pgProc;?>' + stParametros;
    ajaxJavaScript( stPagina, stCtrl )
    //ajaxPopUp(stPagina,stCtrl,stId)
}

function excluirDado( stAcao, codCopia, codDocumento, codProcesso, exercicio){
    var d = window.parent.document;
    var stTraget = d.frm.target;
    
    d.frm.target = "oculto";
    var stAction = d.frm.action;
    d.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&codCopia='+codCopia+'&codDocumento='+codDocumento+'&codProcesso='+codProcesso+'&exercicio='+exercicio+'&stCtrl='+stAcao;
    d.frm.submit();
    d.frm.action = stAction;
    d.frm.target = stTraget;
}

function executaFuncaoAjax( funcao, parametrosGET, sincrono ) {
    var d = window.parent.document;
    if( parametrosGET ) {
        stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>'+parametrosGET+'&stCtrl='+funcao;
    } else {
        stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>'+'&stCtrl='+funcao;
    }

    var stTraget = d.frm.target;
    d.frm.target = "oculto";
    var stAction = d.frm.action;
    d.frm.action = stPag;
    d.frm.submit();
    d.frm.action = stAction;
    d.frm.target = stTraget;
}

//Para chamar uma function de um oculto por ajax
//passando todos os campos que possuam ID por GET
function montaParametrosGET( funcao, sincrono ) {
    var stLink = '';
    var f = window.parent.document.frm;

    for( i=0 ; i<f.elements.length ; i++) {
        if ( f.elements[i].id ) {
            stLink += "&"+f.elements[i].id+"="+f.elements[i].value;
        }
    }
    executaFuncaoAjax( funcao, stLink, sincrono );
}

</script>