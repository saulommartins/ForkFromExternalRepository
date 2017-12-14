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
* Arquivo de popup de busca de Acaos
* Data de Criação: 02/12/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Jânio Eduardo Vasconcellos de Magalhães
*/
?>
<script>
function insereReceita(numReceita,codEstrutural,descricao,valorTotal)
{
    var inTarget = document.frm.campoNum;

    var stTarget = document.frm.campoNom;
    var stBusca  = document.frm.tipoBusca;
    var stForm   = document.frm.nomForm.value;
    var stExibeValorReceita  = document.frm.boExibeValorReceita;

    eval("window.opener.parent.frames['telaPrincipal'].document." + stForm + "." + inTarget.value).value = codEstrutural;
    window.opener.parent.frames['telaPrincipal'].document.getElementById('inNumReceita').value = numReceita;
    window.opener.parent.frames['telaPrincipal'].document.getElementById(stTarget.value).innerHTML = descricao;
    if (stExibeValorReceita.value==1) {
        window.opener.parent.frames['telaPrincipal'].document.getElementById('lbTotalReceita').innerHTML = retornaFormatoMonetario(valorTotal);
    }
    eval("window.opener.parent.frames['telaPrincipal'].document." + stForm + "." + inTarget.value).focus();
    window.close();
}
</script>
