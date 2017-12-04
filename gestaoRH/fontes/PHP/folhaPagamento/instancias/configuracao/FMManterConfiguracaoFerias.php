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
    * Formulário de Configuração Férias
    * Data de Criação: 09/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2006-10-10 05:45:59 -0300 (Ter, 10 Out 2006) $

    * Casos de uso: uc-04.05.32
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                             );
include_once ( CAM_GRH_FOL_MAPEAMENTO . 'TFolhaPagamentoTipoEventoFerias.class.php'                    );
include_once ( CAM_GRH_FOL_MAPEAMENTO . 'TFolhaPagamentoFeriasEvento.class.php'                    );

$stPrograma = 'ManterConfiguracaoFerias';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
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

//BUSCANDO A LISTA DE EVENTOS DE PENSÃO
$obTFolhaPagamentoTipoFerias = new TFolhaPagamentoTipoEventoFerias();
$obTFolhaPagamentoTipoFerias->recuperaTodos( $rsTiposEvento );
$obTFolhaPagamentoFeriasEvento = new TFolhaPagamentoFeriasEvento();

while ( !$rsTiposEvento->eof() ) {
    $stFiltro = " AND ferias_evento.cod_tipo = ".$rsTiposEvento->getCampo("cod_tipo");
    $obTFolhaPagamentoFeriasEvento->recuperaRelacionamento($rsFeriasEvento,$stFiltro);

    $stNome  = 'stInner_Cod_' . $rsTiposEvento->getCampo ('cod_tipo') ;
    $stInner = 'stInner_'.$rsTiposEvento->getCampo ('cod_tipo')       ;
    $ObjInner = new BuscaInner;
    $ObjInner->setRotulo                       ( $rsTiposEvento->getCampo('descricao')                       );
    $ObjInner->setTitle                        ( "Informe o Evento."                                         );
    $ObjInner->setId                           ( $stInner                                                    );
    $ObjInner->setNull                         ( false                                                       );
    $ObjInner->setValue($rsFeriasEvento->getCampo("descricao"));
    $ObjInner->obCampoCod->setName             ( $stNome                                                     );
    $ObjInner->obCampoCodHidden->setValue($rsFeriasEvento->getCampo("cod_evento"));

    ////TODO desscobrir como buscar o valor
    $ObjInner->obCampoCod->setValue            ( $rsFeriasEvento->getCampo("codigo")                         );
    $ObjInner->obCampoCod->setAlign            ( "LEFT"                                                      );
    $ObjInner->obCampoCod->setMascara          ( $stMascaraEvento                                            );
    $ObjInner->obCampoCod->setPreencheComZeros ( "E"                                                         );
    $ObjInner->obCampoCod->obEvento->setOnChange( "executaFuncaoAjax('preencherEvento','&inCodigoEvento='+this.value+'&inCodTipo=".$rsTiposEvento->getCampo('cod_tipo')."&stNatureza=".$rsTiposEvento->getCampo("natureza")."');" );
    $ObjInner->setFuncaoBusca ( "abrePopUp('".CAM_GRH_FOL_POPUPS."IRRF/FLManterTabelaIRRF.php','frm','" .$stNome."','".$stInner."','','".Sessao::getId()."&stNatureza=".$rsTiposEvento->getCampo("natureza")."&boEventoSistema=true','800','550')" );
    $arCompEventos[] = $ObjInner;
    $rsTiposEvento->proximo();
}

$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm    );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addTitulo                    ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"   );
$obFormulario->addTitulo("Configuração do Cálculo de Férias");
$obFormulario->addTitulo("Eventos");
// adicionando ao fourmulário os buscaInner pra cada tipo de evento encontrado
foreach ($arCompEventos as $componente) {
    $obFormulario->addComponente( $componente );
}
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
