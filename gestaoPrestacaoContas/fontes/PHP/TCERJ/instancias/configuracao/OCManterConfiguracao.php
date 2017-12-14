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
  * Formulário oculto
  * Data de criação : 23/05/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    Caso de uso: uc-06.02.01
*/

/*
$Log$
Revision 1.8  2006/07/06 13:52:24  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:42:06  diego

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TCRJ."TCRJConfiguracao.class.php");
include_once(TCGM."TCGM.class.php");

switch ($_REQUEST['stCtrl']) {
    case "recuperaFormularioAlteracao":

        $obTConfiguracao = new TCRJConfiguracao();

        $obTConfiguracao->setDado("parametro","unidade_controle");
        $obTConfiguracao->recuperaPorChave($rsUnidadeControle);

        $stJs = "f.stUnidadeControle.value = '".$rsUnidadeControle->getCampo('valor')."';\n";
        $obTConfiguracao = new TCRJConfiguracao();
        $obTConfiguracao->setDado("parametro","sigla_unidade_controle");
        $obTConfiguracao->recuperaPorChave($rsSiglaUnidadeControle);

        $stJs .= "f.stSiglaUnidadeControle.value = '".$rsSiglaUnidadeControle->getCampo('valor')."';\n";

        $obTConfiguracao = new TCRJConfiguracao();
        $obTConfiguracao->setDado("parametro","cgm_responsavel_bens_patrimoniais");
        $obTConfiguracao->recuperaPorChave($rsCGMResponsavelBens);

        $obTCGM          = new TCGM();
        $obTCGM->setDado("numcgm",$rsCGMResponsavelBens->getCampo('valor'));
        $obTCGM->consultar();

        $stJs .= "f.inCGMResponsavelBens.value = '".$rsCGMResponsavelBens->getCampo('valor')."';\n";
        $stJs .= "d.getElementById('stNomeCGMResponsavelBens').innerHTML = '".$obTCGM->getDado("nom_cgm")."';\n";

        $obTConfiguracao = new TCRJConfiguracao();
        $obTConfiguracao->setDado("parametro","matricula_responsavel_bens_patrimoniais");
        $obTConfiguracao->recuperaPorChave($rsMatriculaBens );

        $stJs .= "f.stMatriculaBens.value = '".$rsMatriculaBens->getCampo('valor')."';\n";

        $obTConfiguracao = new TCRJConfiguracao();
        $obTConfiguracao->setDado("parametro","cargo_responsavel_bens_patrimoniais");
        $obTConfiguracao->recuperaPorChave($rsCargoBens );

        $stJs .= "f.stCargoBens.value = '".$rsCargoBens->getCampo('valor')."';\n";

        $obTConfiguracao = new TCRJConfiguracao();
        $obTConfiguracao->setDado("parametro","cgm_responsavel_conferencia");
        $obTConfiguracao->recuperaPorChave($rsCGMResponsavelConferencia );

        $obTCGM          = new TCGM();
        $obTCGM->setDado("numcgm",$rsCGMResponsavelConferencia->getCampo('valor'));
        $obTCGM->consultar();

        $stJs .= "f.inCGMResponsavelConferencia.value = '".$rsCGMResponsavelConferencia->getCampo('valor')."';\n";
        $stJs .= "d.getElementById('stNomeCGMResponsavelConferencia').innerHTML = '".$obTCGM->getDado("nom_cgm")."';\n";

        $obTConfiguracao = new TCRJConfiguracao();
        $obTConfiguracao->setDado("parametro","matricula_responsavel_conferencia");
        $obTConfiguracao->recuperaPorChave($rsMatriculaConferencia );

        $stJs .= "f.stMatriculaConferencia.value = '".$rsMatriculaConferencia->getCampo('valor')."';\n";

        $obTConfiguracao = new TCRJConfiguracao();
        $obTConfiguracao->setDado("parametro","cargo_responsavel_conferencia");
        $obTConfiguracao->recuperaPorChave($rsCargoConferencia );

        $stJs .= "f.stCargoConferencia.value = '".$rsCargoConferencia->getCampo('valor')."';\n";

        $obTConfiguracao = new TCRJConfiguracao();
        $obTConfiguracao->setDado("parametro","cgm_responsavel_visto");
        $obTConfiguracao->recuperaPorChave($rsCGMResponsavelVisto );

        $obTCGM          = new TCGM();
        $obTCGM->setDado("numcgm",$rsCGMResponsavelBens->getCampo('valor'));
        $obTCGM->consultar();

        $stJs .= "f.inCGMResponsavelVisto.value = '".$rsCGMResponsavelVisto->getCampo('valor')."';\n";
        $stJs .= "d.getElementById('stNomeCGMResponsavelVisto').innerHTML = '".$obTCGM->getDado("nom_cgm")."';\n";

        $obTConfiguracao = new TCRJConfiguracao();
        $obTConfiguracao->setDado("parametro","matricula_responsavel_visto");
        $obTConfiguracao->recuperaPorChave($rsMatriculaVisto );

        $stJs .= "f.stMatriculaVisto.value = '".$rsMatriculaVisto->getCampo('valor')."';\n";

        $obTConfiguracao = new TCRJConfiguracao();
        $obTConfiguracao->setDado("parametro","cargo_responsavel_visto");
        $obTConfiguracao->recuperaPorChave($rsCargoVisto );

        $stJs .= "f.stCargoVisto.value = '".$rsCargoVisto->getCampo('valor')."';\n";

        $obTConfiguracao = new TCRJConfiguracao();
        $obTConfiguracao->setDado("parametro","cgm_responsavel_contabilidade");
        $obTConfiguracao->recuperaPorChave($rsCGMResponsavelContabilidade );

        $obTCGM          = new TCGM();
        $obTCGM->setDado("numcgm",$rsCGMResponsavelContabilidade->getCampo('valor'));
        $obTCGM->consultar();

        $stJs .= "f.inCGMResponsavelContabilidade.value = '".$rsCGMResponsavelContabilidade->getCampo('valor')."';\n";
        $stJs .= "d.getElementById('stNomeCGMResponsavelContabilidade').innerHTML = '".$obTCGM->getDado("nom_cgm")."';\n";

        $obTConfiguracao = new TCRJConfiguracao();
        $obTConfiguracao->setDado("parametro","matricula_responsavel_contabilidade");
        $obTConfiguracao->recuperaPorChave($rsMatriculaContabilidade );

        $stJs .= "f.stMatriculaContabilidade.value = '".$rsMatriculaContabilidade->getCampo('valor')."';\n";

        $obTConfiguracao = new TCRJConfiguracao();
        $obTConfiguracao->setDado("parametro","cargo_responsavel_contabilidade");
        $obTConfiguracao->recuperaPorChave($rsCargoContabilidade );

        $stJs .= "f.stCargoContabilidade.value = '".$rsCargoContabilidade->getCampo('valor')."';\n";
    break;
}
SistemaLegado::executaFrameOculto( $stJs );
