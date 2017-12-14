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
* Arquivo de instância para manutenção de assunto
* Data de Criação: 11/09/2005


* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 15582 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 08:38:09 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.06.95
*/
?>
<script type='text/javascript'>
function incluirAcao(){
    if( document.frm.inCodigoAcao.value ){
        var stPagina ="<?=CAM_GA_PROT_INSTANCIAS.'assunto/'.$pgOcul.'?'.Sessao::getId();?>&inCodigoAcao=" + document.frm.inCodigoAcao.value;
        var stCtrl = 'incluirAcao';
        ajaxJavaScript( stPagina, stCtrl );
    }
}

function excluirAcao( inCodigoAcao ){
    var stPagina ="<?=CAM_GA_PROT_INSTANCIAS.'assunto/'.$pgOcul.'?'.Sessao::getId();?>&inCodigoAcao=" + inCodigoAcao;
    var stCtrl = 'excluirAcao';
    ajaxJavaScript( stPagina, stCtrl );
}

function limparAcao(){
    document.frm.inCodigoAcao.value="";
    document.getElementById("stNomeAcao").innerHTML = '&nbsp;';
}
</script>