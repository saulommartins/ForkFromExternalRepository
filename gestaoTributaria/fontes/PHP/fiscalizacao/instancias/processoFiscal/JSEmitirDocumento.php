<?php
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
?>
<?php
/**
    * Arquivo de Java Script do Manter Emissão de Documentos
    * Data de Criação: 28/08/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Fellipe Esteves dos Santos

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso:

    $Id:$
*/
?>
<script type="text/javascript">

function openWindow(url, nome, w, h)
{
    window.open(url,nome,'width='+w+',height='+h+',toolbar=no,scrollbars=no,top='+h+',left='+w);
}

function confirmaImpressao(label, programa, metodo)
{
    var stLink = '';
    var stForm = document.forms[0];
    if ( !Valida() ) {

    } else {
        stLink += "<?=CAM_GT_FIS_INSTANCIAS?>processoFiscal/FMEmitirDocumento.php?<?=Sessao::getId()?>";
            stLink += "&stLabel="+label;
            stLink += "&stPrograma="+programa;
            stLink += "&stMetodo="+metodo;
            stLink += "&stTipo=emitir";

        for (i = 0 ; i < stForm.elements.length ; i++) {
            if ( typeof( stForm.elements[i]) == 'object' ) {
                if (stForm.elements[i].type == 'radio' ||  stForm.elements[i].type == 'checkbox') {
                    if (stForm.elements[i].checked == true) {
                        stLink += "&"+stForm.elements[i].name+"="+stForm.elements[i].value;
                    }
                } else {
                    stLink += "&"+stForm.elements[i].name+"="+stForm.elements[i].value;
                }
            }
        }
        openWindow(stLink,"",350,250);
    }
}

function downloadImpressao(label, programa, metodo)
{
    var stLink = '';
    var stForm = document.forms[0];
    if ( !Valida() ) {

        } else {
        stLink += "<?=CAM_GT_FIS_INSTANCIAS?>processoFiscal/FMEmitirDocumento.php?<?=Sessao::getId()?>";
            stLink += "&stLabel="+label;
            stLink += "&stPrograma="+programa;
            stLink += "&stMetodo="+metodo;
            stLink += "&stTipo=listar";

        for (i = 0 ; i < stForm.elements.length ; i++) {
            if ( typeof( stForm.elements[i]) == 'object' ) {
                if (stForm.elements[i].type == 'radio' ||  stForm.elements[i].type == 'checkbox') {
                    if (stForm.elements[i].checked == true) {
                        stLink += "&"+stForm.elements[i].name+"="+stForm.elements[i].value;
                    }
                } else {
                    stLink += "&"+stForm.elements[i].name+"="+stForm.elements[i].value;
                }
            }
        }
        openWindow(stLink,"",350,250);
    }
}

function submitImpressao(imprime)
{
    if (imprime == true) {
        window.opener.parent.frames['telaPrincipal'].document.forms[0].submit();
        document.forms[0].submit();

    } else {
        window.opener.parent.frames['telaPrincipal'].document.forms[0].submit();
        window.close();
    }
}

</script>
