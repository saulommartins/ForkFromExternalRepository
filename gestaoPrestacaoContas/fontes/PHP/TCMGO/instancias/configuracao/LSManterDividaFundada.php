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

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGODividaFundada.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDividaFundada";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";


if ( $_REQUEST['stControleAcao'] == 'incluir' ) {    
    //PARAMETROS    
    SistemaLegado::mudaFramePrincipal( $pgForm."?stAcao=incluir".Sessao::getId()."&inCodEntidade=".$_REQUEST['inCodEntidade']."&inExercicio=".$_REQUEST['inExercicio'] );
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $request->get('stAcao') );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "" );

$obTTCMGODividaFundada = new TTCMGODividaFundada;

$obTTCMGODividaFundada->recuperaTodos($rsRecordSet, " WHERE exercicio = '".$request->get('inExercicio')."' AND cod_entidade = ".$request->get('inCodEntidade')."");

$obLista = new Lista;
$obLista->setRecordSet( $rsRecordSet );
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Número da Norma');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Número do Órgão');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Número da Unidade');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Tipo do Lançamento');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_norma" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "num_orgao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "num_unidade" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "cod_tipo_lancamento" );
$obLista->commitDado();

$stAcao = 'alterar';
$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( "ALTERAR" );
$obLista->ultimaAcao->setLink ( $pgForm."?stAcao=$stAcao&".Sessao::getId().$stLink );
$obLista->ultimaAcao->addCampo( "&inCodNorma"          , "cod_norma"            );
$obLista->ultimaAcao->addCampo( "&inNumOrgao"          , "num_orgao"            );
$obLista->ultimaAcao->addCampo( "&inNumUnidade"        , "num_unidade"          );
$obLista->ultimaAcao->addCampo( "&inNumUnidade"        , "num_unidade"          );
$obLista->ultimaAcao->addCampo( "&inExercicio"         , "exercicio"            );
$obLista->ultimaAcao->addCampo( "&inCodTipoLancamento" , "cod_tipo_lancamento"  );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"       , "cod_entidade"         );
$obLista->ultimaAcao->addCampo( "&inNumCgm"            , "numcgm"               );
$obLista->ultimaAcao->addCampo( "&vlSaldoAnterior"     , "valor_saldo_anterior" );
$obLista->ultimaAcao->addCampo( "&vlContratacao"       , "valor_contratacao"    );
$obLista->ultimaAcao->addCampo( "&vlAmortizacao"       , "valor_amortizacao"    );
$obLista->ultimaAcao->addCampo( "&vlCancelamento"      , "valor_cancelamento"   );
$obLista->ultimaAcao->addCampo( "&vlEncampacao"        , "valor_encampacao"     );
$obLista->ultimaAcao->addCampo( "&vlCorrecao"          , "valor_correcao"       );
$obLista->ultimaAcao->addCampo( "&vlSaldoAtual"        , "valor_saldo_atual"    );
$obLista->commitAcao();
$stAcao = 'excluir';
$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( "EXCLUIR" );
$obLista->ultimaAcao->setLink ( $pgProc."?stAcao=$stAcao&".Sessao::getId() );
$obLista->ultimaAcao->addCampo( "&inCodNorma"          , "cod_norma"            );
$obLista->ultimaAcao->addCampo( "&inNumOrgao"          , "num_orgao"            );
$obLista->ultimaAcao->addCampo( "&inNumUnidade"        , "num_unidade"          );
$obLista->ultimaAcao->addCampo( "&inNumUnidade"        , "num_unidade"          );
$obLista->ultimaAcao->addCampo( "&inExercicio"         , "exercicio"            );
$obLista->ultimaAcao->addCampo( "&inCodTipoLancamento" , "cod_tipo_lancamento"  );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"       , "cod_entidade"         );
$obLista->ultimaAcao->addCampo( "&inNumCgm"            , "numcgm"               );
$obLista->ultimaAcao->addCampo( "&vlSaldoAnterior"     , "valor_saldo_anterior" );
$obLista->ultimaAcao->addCampo( "&vlContratacao"       , "valor_contratacao"    );
$obLista->ultimaAcao->addCampo( "&vlAmortizacao"       , "valor_amortizacao"    );
$obLista->ultimaAcao->addCampo( "&vlCancelamento"      , "valor_cancelamento"   );
$obLista->ultimaAcao->addCampo( "&vlEncampacao"        , "valor_encampacao"     );
$obLista->ultimaAcao->addCampo( "&vlCorrecao"          , "valor_correcao"       );
$obLista->ultimaAcao->addCampo( "&vlSaldoAtual"        , "valor_saldo_atual"    );
$obLista->commitAcao();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>