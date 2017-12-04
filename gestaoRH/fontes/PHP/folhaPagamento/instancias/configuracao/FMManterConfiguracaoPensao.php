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
    * Formulário
    * Data de Criação: 17/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: andre $
    $Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

    * Casos de uso: uc-04.04.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                             );
include_once ( CAM_GRH_FOL_MAPEAMENTO . 'TFolhaPagamentoConfiguracaoPensao.class.php'                  );
include_once ( CAM_GRH_FOL_MAPEAMENTO . 'TFolhaPagamentoTipoEventoPensao.class.php'                    );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

$stPrograma = 'ManterConfiguracaoPensao';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once ( $pgJs   );
include_once ( $pgOcul );

$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

$rsTiposEvento = new RecordSet;
$arCompEventos = array();

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

/// BUSCANDO A LISTA DE EVENTOS DE PENSÃO
$obTFolPensaoTipoEvento = new TFolhaPagamentoTipoEventoPensao;
$obTFolPensaoTipoEvento->recuperaTodos( $rsTiposEvento );

while ( !$rsTiposEvento->eof() ) {
    $stNome  = 'stInner_Cod_' . $rsTiposEvento->getCampo ('cod_tipo') ;
    $stInner = 'stInner_'.$rsTiposEvento->getCampo ('cod_tipo')       ;
    $ObjInner = new BuscaInner;
    $ObjInner->setRotulo                       ( $rsTiposEvento->getCampo('descricao')                       );
    $ObjInner->setTitle                        ( "Informe o Evento."                                         );
    $ObjInner->setId                           ( $stInner                                                    );
    $ObjInner->setNull                         ( false                                                       );
    $ObjInner->obCampoCod->setName             ( $stNome                                                     );

    ////TODO desscobrir como buscar o valor
    $ObjInner->obCampoCod->setValue            ( ''                                                          );
    $ObjInner->obCampoCod->setAlign            ( "LEFT"                                                      );
    $ObjInner->obCampoCod->setMascara          ( $stMascaraEvento                                            );
    $ObjInner->obCampoCod->setPreencheComZeros ( "E"                                                         );
    $ObjInner->obCampoCod->obEvento->setOnChange( "preencherEvento('".$rsTiposEvento->getCampo('cod_tipo')."','D');" );
    $ObjInner->setFuncaoBusca ( "abrePopUp('".CAM_GRH_FOL_POPUPS."IRRF/FLManterTabelaIRRF.php','frm','" .$stNome."','".$stInner."','','".Sessao::getId()."&stNatureza=D&boEventoSistema=true','800','550')" );
    $arCompEventos[] = $ObjInner;
    $rsTiposEvento->proximo();
}

$obBscFuncao = new BuscaInner;
$obBscFuncao->setRotulo ( "Função"                                          );
$obBscFuncao->setTitle  ( "Informe a função a ser utilizada no cálculo."    );
$obBscFuncao->setId     ( "stFuncao"                                        );
$obBscFuncao->setNull   ( false                                             );
$obBscFuncao->obCampoCod->setName   ( "inCodFuncao" );
$obBscFuncao->obCampoCod->setTitle  ( 'Informe a função a ser utilizada no cálculo.' );
$obBscFuncao->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncao');");
$obBscFuncao->obCampoCod->setMascara("99.99.999");
$obBscFuncao->obCampoCod->setNull   ( false     );
$obBscFuncao->setFuncaoBusca( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm',
                                  'inCodFuncao','stFuncao','todos','".Sessao::getId()."','800','550');" );

$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm    );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );

// adicionando ao fourmulário os buscaInner pra cada tipo de evento encontrado
foreach ($arCompEventos as $componente) {
    $obFormulario->addComponente( $componente );
}
$obFormulario->addComponente ( $obBscFuncao );

$obFormulario->Ok();
$obFormulario->show();
preencherInnerEventos( true );
preencheFuncao       ( true );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
