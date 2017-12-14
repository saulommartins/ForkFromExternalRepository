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
* Página de Processamento da Configuração de Salário Família
* Data de Criação: 26/04/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 31475 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.05.44
*/

//Ticket #13872

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoSalarioFamilia.class.php" );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php" );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoSalarioFamilia";
$pgForm     = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc     = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgList     = "LS".$stPrograma.".php?stAcao=$stAcao";

if ( $stAcao == "incluir" )
    $pgProx = $pgForm;
else
    $pgProx = $pgList;

$obSalarioFamilia = new RFolhaPagamentoSalarioFamilia;

switch ($stAcao) {
    case "incluir":
    case "alterar":
        $arFaixasConcessoes = Sessao::read("FaixasConcessoes");
        if ( count($arFaixasConcessoes) > 0 ) {
            $obSalarioFamilia->obRFolhaPagamentoPrevidencia->setCodRegimePrevidencia( $_POST["stCodRegime"] );
            //listar a ultima vigencia
            $obSalarioFamilia->listarSalarioFamilia( $rsSalarioFamilia );

            $arDataUltimaVigencia = explode("/", $rsSalarioFamilia->getCampo("vigencia") );
            $arDataVigenciaAtual  = explode("/", $_POST["dtVigencia"] );
            $inDataUltimaVigencia = $arDataUltimaVigencia[2].$arDataUltimaVigencia[1].$arDataUltimaVigencia[0];
            $inDataVigenciaAtual  = $arDataVigenciaAtual[2].$arDataVigenciaAtual[1].$arDataVigenciaAtual[0];

            if ($inDataVigenciaAtual >= $inDataUltimaVigencia) {
                $obSalarioFamilia->setIdadeLimite( $_POST["inIdadeLimite"] );
                $obSalarioFamilia->setVigencia( $_POST["dtVigencia"] );

                $obEvento = new RFolhaPagamentoEvento;
                $obEvento->listarTiposEventoSalarioFamilia( $rsTiposEventos );

                while (!$rsTiposEventos->eof()) {
                    $inCodTipo   = $rsTiposEventos->getCampo("cod_tipo");
                    $stCodigoEvento = $_POST[ "inTipoEvento".$inCodTipo ];
                    $obEvento->setCodigo($stCodigoEvento);
                    $obEvento->listarEvento( $rsEventos );
                    $inCodEvento = $rsEventos->getCampo( "cod_evento" );

                    $obSalarioFamilia->addRFolhaPagamentoEvento();
                    $obSalarioFamilia->roFolhaPagamentoEvento->setCodEvento($inCodEvento);
                    $obSalarioFamilia->roRFolhaPagamentoEventoSalarioFamilia->setCodTipoEventoSalarioFamilia($inCodTipo);
                    $rsTiposEventos->proximo();
                }

                for ( $i=0 ; $i<count($arFaixasConcessoes) ; $i++ ) {
                    $obSalarioFamilia->addRFolhaPagamentoFaixaPagamento();
                    $obSalarioFamilia->roRFolhaPagamentoFaixaPagamento->setCodFaixa    ( $arFaixasConcessoes[$i]["inCodFaixa"]     );
                    $obSalarioFamilia->roRFolhaPagamentoFaixaPagamento->setVlInicial   ( $arFaixasConcessoes[$i]["inValorInicial"] );
                    $obSalarioFamilia->roRFolhaPagamentoFaixaPagamento->setVlFinal     ( $arFaixasConcessoes[$i]["inValorFinal"]   );
                    $obSalarioFamilia->roRFolhaPagamentoFaixaPagamento->setVlPagamento ( $arFaixasConcessoes[$i]["inValorPagar"]   );
                }
                $obErro = $obSalarioFamilia->incluirSalarioFamilia();

                $obSalarioFamilia->obRFolhaPagamentoPrevidencia->listarRegimePrevidencia( $rsRegimePrevidencia );

                foreach ($rsRegimePrevidencia->getElementos() as $arRegimePrevidencia) {
                     if ($_POST["stCodRegime"] == $arRegimePrevidencia["cod_regime_previdencia"]) {
                         $stRegimePrevidencia = $arRegimePrevidencia["descricao"];
                     }
                }

                if ( !$obErro->ocorreu() ) {
                    sistemaLegado::alertaAviso($pgProx,"Regime Previdenciário: ".$stRegimePrevidencia, $stAcao,"aviso", Sessao::getId(), "../");
                } else {
                    sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");
                }
            } else {
                sistemaLegado::exibeAviso("A data da vigência deve ser igual ou posterior a: ".$rsSalarioFamilia->getCampo("vigencia")."."," "," ");
            }
        } else {
            sistemaLegado::exibeAviso("Informe ao menos uma faixa de concessão."," "," ");
        }
    break;
    case "excluir":
        $obSalarioFamilia->obRFolhaPagamentoPrevidencia->setCodRegimePrevidencia( $_GET["inCodRegimePrevidencia"] );
        $obErro = $obSalarioFamilia->excluirSalarioFamilia();

        $obSalarioFamilia->obRFolhaPagamentoPrevidencia->listarRegimePrevidencia( $rsRegimePrevidencia );
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Regime Previdenciário: ".$rsRegimePrevidencia->getCampo("descricao"),"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;
}
?>
