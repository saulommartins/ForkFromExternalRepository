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
 * Javascript que valida inclusão e alteração de Ação
 * Data de Criação: 04/08/2007


 * @author Analista      : Heleno Menezes da Silva
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: JSManterAcao.php 35524 2008-11-12 11:48:47Z pedro.medeiros $

 * Casos de uso: uc-02.09.04
 */
?>

<script type="text/javascript">

function buscaOCMontaOrgaoUnidade(tipoBusca,actionAnterior,actionPosterior,targetPosterior,sessao){
    BloqueiaFrames(true,true);
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = actionAnterior + '?' + sessao;
    document.frm.submit();

    if( targetPosterior != '' ){
        document.frm.target = targetPosterior;
    }
    document.frm.action = actionPosterior + '?' + sessao;
}

</script>
