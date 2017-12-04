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
* Página de Processamento de Evento
* Data de Criação   : 10/02/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Eduardo Antunez

* @ignore

$Revision: 30727 $
$Name$
$Author: souzadl $
$Date: 2007-11-20 15:48:41 -0200 (Ter, 20 Nov 2007) $

* Casos de uso: uc-04.05.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stPrograma = "ManterEvento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRFolhaPagamentoEvento  = new RFolhaPagamentoEvento;
$arAbas = array('1'=>'Sal','2'=>'Fer','3'=>'13o','4'=>'Res');
#$arConfiguracaoEvento = sessao->transf;
$obErro = new Erro;

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

switch ($stAcao) {

    case "incluir":
    case "alterar":
        //Informações do Evento
        $obErro = new erro;
        $obRFolhaPagamentoEvento->setCodigo              ( $_POST['stCodigo']                            );
        if ($stAcao == "incluir") {
          $obRFolhaPagamentoEvento->listarEvento($rsEvento);
          if ( $rsEvento->getNumLinhas() > 0 ) {
              $obErro->setDescricao("Evento ".$_POST['stCodigo']." já existe");
          }
        }
       
        if( $_POST['inCodVerbaRescisoriaMTE'] == "" AND ($_REQUEST["hdnNatureza"] == 'D' || $_REQUEST["hdnNatureza"] == 'P')) {
           $obErro->setDescricao( "Campo Verba Rescisória MTE da guia Identificação inválido! É campo obrigatório." );
        }

        if ( !$obErro->ocorreu() ) {
            $obRFolhaPagamentoEvento->setDescricao               ( $_POST['stDescricaoIde']                      );
            $obRFolhaPagamentoEvento->setSigla                   ( $_POST['stSigla']                             );
            $obRFolhaPagamentoEvento->setNatureza                ( $_POST['natureza']                            );
            $obRFolhaPagamentoEvento->setTipo                    ( ($_POST['stTipo'])  ? $_POST['stTipo']  : 'B' );
            $obRFolhaPagamentoEvento->setFixado                  ( ($_POST['stFixar']) ? $_POST['stFixar'] : 'B' );
            $obRFolhaPagamentoEvento->setValor                   ( $_POST['nuValor']                             );
            $obRFolhaPagamentoEvento->setUnidadeQuantitativa     ( $_POST['nuUnidadeQuantitativa']               );
            $obRFolhaPagamentoEvento->setLimiteCalculo           ( $_POST['boLimiteCalculo']                     );
            $obRFolhaPagamentoEvento->setApresentaParcela        ( $_POST['boApresentaParcela']                  );
            $obRFolhaPagamentoEvento->setObservacao              ( $_POST['stTextoComplementar']                 );
            $obRFolhaPagamentoEvento->setEventoAutomaticoSistema ( $_POST['stEventoAutomatico'] == 'S'           );
            $obRFolhaPagamentoEvento->setApresentaContraCheque   ( $_POST['boApresentarContraCheque']            );
            $obRFolhaPagamentoEvento->setCodVerbaRescisoriaMTE   ( $_POST['inCodVerbaRescisoriaMTE']             );

            $obRFolhaPagamentoEvento->obRFolhaPagamentoSequencia->setCodSequencia( $_POST['inCodSequencia'] );

            //Atributos dinâmicos
            foreach ($arChave as $key => $value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode( "," , $value );
                }
                $obRFolhaPagamentoEvento->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }
            $stNatureza = ( isset($_POST['natureza']) ) ? $_POST['natureza'] : $_POST['hdnNatureza'];
            $stNatureza = ( $stNatureza == 'Base' )     ? 'B'                : $stNatureza;

            //Verifica se as abas foram preenchidas
            $boAbasPreenchidas = false;
            foreach ($arAbas as $inCodConfiguracaoEvento => $stAba) {
                $arConfiguracaoEvento = Sessao::read('Caso'.$stAba);
                if ( !empty($_POST['stMascClassificacao'.$stAba]) || !empty($arConfiguracaoEvento) ) {
                    $boAbasPreenchidas = true;
                }
            }
            if (!$boAbasPreenchidas) {
                $obErro->setDescricao('Abas sem parâmetros (salário,férias,13o salário,rescisão) não são permitidas.');
            }

            if ( !$obErro->ocorreu() ) {

                //Trata cada aba
                foreach ($arAbas as $inCodConfiguracaoEvento => $stAba) {

                    $obRFolhaPagamentoEvento->addConfiguracaoEvento();
                    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao ( $inCodConfiguracaoEvento );
                    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $_POST['stMascClassificacao'.$stAba] );
                    //Caso (particularidade) de uma aba
                    $arConfiguracaoEvento = Sessao::read('Caso'.$stAba);
                    if (isset($arConfiguracaoEvento)) {
                        foreach ($arConfiguracaoEvento as $arCasoEvento) {
                            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
                            $arCodFuncao = explode('.',$arCasoEvento['inCodFuncao']);
                            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFuncao->setCodFuncao                          ( $arCodFuncao[2] );
                            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFuncao->obRBiblioteca->setCodigoBiblioteca    ( $arCodFuncao[1] );
                            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFuncao->obRBiblioteca->roRModulo->setCodModulo( $arCodFuncao[0] );
                            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setDescricao( $arCasoEvento['stDescricaoCaso'] );
                            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setProporcaoAdiantamento( $arCasoEvento['boConsProporcaoAdiantamento'] );
                            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setProporcaoAbono( $arCasoEvento['boProporcionalizarAbono'] );
                            if ($arCasoEvento['inCodigoTipoMedia'] != "") {
                                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoMedia.class.php");
                                $obTFolhaPagamentoTipoMedia = new TFolhaPagamentoTipoMedia;
                                $stFiltro = " WHERE codigo = '".$arCasoEvento['inCodigoTipoMedia']."'";
                                $obTFolhaPagamentoTipoMedia->recuperaTodos($rsTipoMedia,$stFiltro);
                                $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setCodTipoMedia($rsTipoMedia->getCampo("cod_tipo"));
                            }
                            if ( is_array($arCasoEvento['arSubDivisao']) ) {
                                //Subdivisao de cada Caso
                                $arCodSubDivisao = array();
                                foreach ($arCasoEvento['arSubDivisao'] as $stSubDivisao) {
                                    $arSubDivisao = explode("/",$stSubDivisao);
                                    $arCodSubDivisao[] = $arSubDivisao[1];
                                }
                                $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->arCodSubDivisao = $arCodSubDivisao;
                            }
                            if ( is_array($arCasoEvento['arCargo']) ) {
                                asort($arCasoEvento['arCargo']);
                                $inCodCargoTmp = 0;
                                //Cargo de cada caso
                                $arCodCargoCodEspecialidade = array();
                                foreach ($arCasoEvento['arCargo'] as $stCargo) {
                                    $arCargo = explode("/",$stCargo);

                                    $inCodCargo = $arCargo[0];
                                    $inCodEspecialidade = $arCargo[1];

                                    if ($inCodEspecialidade != "") {
                                        $arCodCargoCodEspecialidade[] = $inCodCargo."-".$inCodEspecialidade;
                                    } else {
                                        $arCodCargoCodEspecialidade[] = $inCodCargo."-0";
                                    }
                                }

                                $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->arCodCargoCodEspecialidade = $arCodCargoCodEspecialidade;

                            }

                            //Processamento dos eventos de base
                            if ( is_object($arCasoEvento['eventosBaseSal']) ) {
                                $rsEventosBase = $arCasoEvento['eventosBaseSal'];
                                if ( is_object($rsEventosBase) ) {
                                    while ( !$rsEventosBase->eof() ) {
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->addRFolhaPagamentoEvento();
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->setCodEvento($rsEventosBase->getCampo('cod_evento'));
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->setTimestamp($rsEventosBase->getCampo('timestamp'));
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->addConfiguracaoEvento();
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao($rsEventosBase->getCampo('cod_configuracao'));
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setCodCaso($rsEventosBase->getCampo('cod_caso'));
                                        $rsEventosBase->proximo();
                                    }
                                }
                            }
                            if ( is_object($arCasoEvento['eventosBaseFer']) ) {
                                $rsEventosBase = "";
                                $rsEventosBase = $arCasoEvento['eventosBaseFer'];
                                if ( is_object($rsEventosBase) ) {
                                    while ( !$rsEventosBase->eof() ) {
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->addRFolhaPagamentoEvento();
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->setCodEvento($rsEventosBase->getCampo('cod_evento'));
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->setTimestamp($rsEventosBase->getCampo('timestamp'));
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->addConfiguracaoEvento();
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao($rsEventosBase->getCampo('cod_configuracao'));
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setCodCaso($rsEventosBase->getCampo('cod_caso'));
                                        $rsEventosBase->proximo();
                                    }
                                }
                            }
                            if ( is_object($arCasoEvento['eventosBase13o']) ) {
                                $rsEventosBase = "";
                                $rsEventosBase = $arCasoEvento['eventosBase13o'];
                                if ( is_object($rsEventosBase) ) {
                                    while ( !$rsEventosBase->eof() ) {
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->addRFolhaPagamentoEvento();
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->setCodEvento($rsEventosBase->getCampo('cod_evento'));
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->setTimestamp($rsEventosBase->getCampo('timestamp'));
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->addConfiguracaoEvento();
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao($rsEventosBase->getCampo('cod_configuracao'));
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setCodCaso($rsEventosBase->getCampo('cod_caso'));
                                        $rsEventosBase->proximo();
                                    }
                                }
                            }
                            if ( is_object($arCasoEvento['eventosBaseRes']) ) {
                                $rsEventosBase = "";
                                $rsEventosBase = $arCasoEvento['eventosBaseRes'];
                                if ( is_object($rsEventosBase) ) {
                                    while ( !$rsEventosBase->eof() ) {
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->addRFolhaPagamentoEvento();
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->setCodEvento($rsEventosBase->getCampo('cod_evento'));
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->setTimestamp($rsEventosBase->getCampo('timestamp'));
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->addConfiguracaoEvento();
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao($rsEventosBase->getCampo('cod_configuracao'));
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
                                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setCodCaso($rsEventosBase->getCampo('cod_caso'));
                                        $rsEventosBase->proximo();
                                    }
                                }
                            }
                        }
                    }
                }//foreach aba
            }

            if ( !$obErro->ocorreu() ) {
                if ($stAcao == "incluir") {
                    $obErro = $obRFolhaPagamentoEvento->incluirEvento();
                    $pgNext = $pgForm;
                } elseif ($stAcao == "alterar") {
                    $obRFolhaPagamentoEvento->setCodEvento( $_POST['inCodEvento'] );
                    $obErro = $obRFolhaPagamentoEvento->alterarEvento();
                    $pgNext = $pgList;
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgNext,"Evento: ".$_POST['stCodigo'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;

    case "excluir":
        $obRFolhaPagamentoEvento->setCodEvento( $_GET['inCodEvento'] );
        $obErro = $obRFolhaPagamentoEvento->excluirEvento();
        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgList,"Evento: ".$_GET['stCodigo'],"excluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::alertaAviso($pgList,"O Evento ".urlencode($_GET['stCodigo'])." não pode ser excluído, possivelmente está registrado para um  contrato ou sendo utilizado em uma configuração.","n_excluir","erro", Sessao::getId(), "../");
    break;
}

?>
