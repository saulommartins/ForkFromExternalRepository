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
  * Página Oculta da Configuração de LOA
  * Data de Criação: 21/01/2015

  * @author Analista: 
  * @author Desenvolvedor: Lisiane Morais

  * @ignore
  *
  * $Id: $
  *
  * $Revision: $
  * $Name: $
  * $Author: $
  * $Date: $
  *
***/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function processarForm($boExecuta = false, $stArquivo = "Form", $stAcao = "incluir")
{
    switch ($stAcao) {
        case "manter":
            $stJs .= preencheCampoLei();
        break;
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function preencheCampoLei()
{
    include_once( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php" );
    include_once( CAM_GPC_TGO_MAPEAMENTO."TCMGOConfiguracaoLOA.class.php" );

    $rsNorma = new RecordSet();
    $rsTCMGOConfiguracaoLOA = new RecordSet();

    $obTNorma = new TNorma();
    $obTCMGConfiguracaoLOA = new TCMGOConfiguracaoLOA();
    $obTCMGConfiguracaoLOA->setDado('exercicio', Sessao::getExercicio());
    $obTCMGConfiguracaoLOA->recuperaPorChave($rsTCMGOConfiguracaoLOA);

    if ($rsTCMGOConfiguracaoLOA->getNumLinhas() > 0) {
        $stFiltro = " WHERE cod_norma = ".$rsTCMGOConfiguracaoLOA->getCampo('cod_norma');
        $obTNorma->recuperaNormas( $rsNorma, $stFiltro );
        if ($rsNorma->getNumLinhas() > 0) {
            $stJs .= "document.getElementById('stCodNorma').focus();\n";
            $stJs .= "document.getElementById('stCodNorma').value = trim('".$rsNorma->getCampo('num_norma_exercicio')."');    \n";
            $stJs .= "document.getElementById('hdnCodTipoNorma').value = '".$rsNorma->getCampo('cod_tipo_norma')."';    \n";
            $stJs .= "document.getElementById('hdnCodNorma').value = '".$rsNorma->getCampo('cod_norma')."';    \n";
            $stJs .= "document.getElementById('nuPorSuplementacao').focus();\n";
            $stJs .= "document.getElementById('nuPorSuplementacao').value = '".str_replace('.',',',$rsTCMGOConfiguracaoLOA->getCampo('percentual_suplementacao'))."';\n";
            $stJs .= "document.getElementById('nuPorCreditoInterna').value = '".str_replace('.',',',$rsTCMGOConfiguracaoLOA->getCampo('percentual_credito_interna'))."';\n";
            $stJs .= "document.getElementById('nuPorCreditoAntecipacaoReceita').value = '".str_replace('.',',',$rsTCMGOConfiguracaoLOA->getCampo('percentual_credito_antecipacao_receita'))."';\n";
        }

    return $stJs;
    }
}
?>
