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
    * Arquivo de Java Script do Manter Levantamento Fiscal
    * Data de Criação: 29/07/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso:

    $Id:$
*/
?>
<script type="text/javascript">
function openWindow(url,nome,w,h)
{
    window.open(url,nome,'width='+w+',height='+h+',toolbar=no,scrollbars=no,top='+h+',left='+w);
}

function verificaDocumento()
{
        var link = new String();

    link ="<?=CAM_GT_FIS_INSTANCIAS?>processoFiscal/"+verificaDocumento.arguments[11]+"?<?=Sessao::getId()?>";
        link+="&inTipoFiscalizacao="+verificaDocumento.arguments[1];
    link+="&inCodProcesso="+verificaDocumento.arguments[2];
        link+="&inInscricao="+verificaDocumento.arguments[3];
        link+="&inCodFiscal="+verificaDocumento.arguments[4];
        link+="&inCodAtividade="+verificaDocumento.arguments[5];
        link+="&inCodModalidade="+verificaDocumento.arguments[6];
    link+="&inNomModalidade="+verificaDocumento.arguments[7];
    link+="&inNomAtividade="+verificaDocumento.arguments[8];
    link+="&inInicio="+verificaDocumento.arguments[9];
    link+="&inTermino="+verificaDocumento.arguments[10];
    link+="&stUrl="+verificaDocumento.arguments[12];
    link+="&stAcao=cadastrar";

    if (verificaDocumento.arguments[0] != "" || verificaDocumento.arguments[0] >= 1) {

            openWindow(link,"",350,250);
    } else {
        window.document.location.href = link;
    }
}

function submitLevantamento(caso)
{
        var link = new String();

    switch (caso) {

        case 1:
            link ="<?=CAM_GT_FIS_INSTANCIAS?>processoFiscal/"+window.document.getElementById('stUrl').value+"?<?=Sessao::getId()?>";
                link+="&inTipoFiscalizacao="+window.document.getElementById('stUrl').value;
            link+="&inCodProcesso="+window.document.getElementById('inCodProcesso').value;
                link+="&inInscricao="+window.document.getElementById('inInscricao').value;
                link+="&inCodFiscal="+window.document.getElementById('inCodFiscal').value;
                link+="&inCodAtividade="+window.document.getElementById('inCodAtividade').value;
                link+="&inCodModalidade="+window.document.getElementById('inCodModalidade').value;
            link+="&inNomModalidade="+window.document.getElementById('inNomModalidade').value;
            link+="&inNomAtividade="+window.document.getElementById('inNomAtividade').value;
            link+="&inInicio="+window.document.getElementById('inInicio').value;
            link+="&inTermino="+window.document.getElementById('inTermino').value;
            link+="&stAcao=cadastrar";
        break;
        case 2:
            link ="<?=CAM_GT_FIS_INSTANCIAS?>processoFiscal/LSManterLevantamentoDocumentos.php?<?=Sessao::getId()?>";
                link+="&inTipoFiscalizacao=1";
            link+="&inCodProcesso="+window.document.getElementById('inCodProcesso').value;
                link+="&inInscricao="+window.document.getElementById('inInscricao').value;
                link+="&inCodFiscal="+window.document.getElementById('inCodFiscal').value;
            link+="&stAcao=cadastrar";
        break;
    }
    window.opener.parent.frames['telaPrincipal'].document.location.href = link;
    window.close();
}
</script>
