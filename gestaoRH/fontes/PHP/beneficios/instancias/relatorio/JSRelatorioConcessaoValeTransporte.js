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
    * Java Script do Relatório de Concessão de Vale-Tranporte
    * Data de Criação: 07/11/2005


    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.06.11
*/

/*
$Log$
Revision 1.3  2006/08/08 17:31:09  vandre
Adicionada tag log.

*/
?>
<script type="text/javascript">

function buscaValor(tipoBusca){
     document.frm.stCtrl.value = tipoBusca;
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
     document.frm.submit();
     document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function limpaForm() {
    buscaValor('geraSpan');
    passaItem('document.frm.inCodLotacaoSelecionados','document.frm.inCodLotacaoDisponiveis','tudo');
    passaItem('document.frm.inCodLocalSelecionados','document.frm.inCodLocalDisponiveis','tudo');
    document.frm.inPeriodicidade.selectedIndex = 3;
    montaPeriodicidade(4);
}

</script>