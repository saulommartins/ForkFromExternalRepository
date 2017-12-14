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
<?
/**
    * Página de Java Script de Parametros
    * Data de Criação: 09/01/2007


    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: andre.almeida $
    $Date: 2007-01-18 09:06:26 -0200 (Qui, 18 Jan 2007) $

    * Casos de uso: uc-02.08.16
*/
?>

<script type="text/javascript">

    function mudaStatusCheckBox( boStatus ){
        document.getElementById('chkPPA').disabled              = boStatus;
        document.getElementById('chkLDO').disabled              = boStatus;
        document.getElementById('chkLOA').disabled              = boStatus;
        document.getElementById('chkExecOrcamentaria').disabled = boStatus;
        document.getElementById('chkRegContabeis').disabled     = boStatus;
        document.getElementById('chkGestaoFiscal').disabled     = boStatus;
        document.getElementById('chkLicitacao').disabled        = boStatus;
        document.getElementById('chkContratos').disabled        = boStatus;
        document.getElementById('chkConvenios').disabled        = boStatus;
        document.getElementById('chkConcursos').disabled        = boStatus;
        document.getElementById('chkPlanoCargos').disabled      = boStatus;
        document.getElementById('chkPessoal').disabled          = boStatus;
        document.getElementById('chkConstDirEmpresa').disabled  = boStatus;
        document.getElementById('chkGenericos').disabled        = boStatus;

        document.getElementById('chkPPA').checked               = boStatus;
        document.getElementById('chkLDO').checked               = boStatus;
        document.getElementById('chkLOA').checked               = boStatus;
        document.getElementById('chkExecOrcamentaria').checked  = boStatus;
        document.getElementById('chkRegContabeis').checked      = boStatus;
        document.getElementById('chkGestaoFiscal').checked      = boStatus;
        document.getElementById('chkLicitacao').checked         = boStatus;
        document.getElementById('chkContratos').checked         = boStatus;
        document.getElementById('chkConvenios').checked         = boStatus;
        document.getElementById('chkConcursos').checked         = boStatus;
        document.getElementById('chkPlanoCargos').checked       = boStatus;
        document.getElementById('chkPessoal').checked           = boStatus;
        document.getElementById('chkConstDirEmpresa').checked   = boStatus;
        document.getElementById('chkGenericos').checked         = boStatus;
    }

    function executaPaginaGeraArquivos() {
        document.frm.target = 'oculto';
        document.frm.action = 'PRGeraArquivosEsfinge.php?<?=Sessao::getId();?>';
        document.frm.submit();
    }

</script>