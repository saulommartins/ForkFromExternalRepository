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
    * Formulario de Consulta de Arrecadação
    * Data de Criação   : 22/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: FMConsultaImovel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.9  2007/05/09 20:01:33  cercato
Bug #9237#

Revision 1.8  2006/10/16 15:47:33  cercato
correcoes para a consulta do itbi.

Revision 1.7  2006/09/15 11:04:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRLancamento.class.php"                                             );
include_once( CAM_GT_ARR_NEGOCIO."RARRParcela.class.php"                                                );
include_once( CAM_GT_MON_NEGOCIO."RMONCredito.class.php"                                                );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaArrecadacao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LSConsultaImovel.php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

;
include_once($pgJs);

// passar do request pra variaveis
$inInscricao        = $_REQUEST["inInscricao"       ];
$inNumCgm           = $_REQUEST["inNumCgm"          ];
$stNomCgm           = $_REQUEST["inNomCgm"          ];
$stDados            = $_REQUEST["stDados"           ];

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//* LISTAGEM DE PROPRIETARIOS
include_once( CAM_GT_CIM_NEGOCIO . "RCIMImovel.class.php"        );
include_once( CAM_GA_CGM_NEGOCIO . "RCGM.class.php"     );
$obRCIMImovel        = new RCIMImovel (new RCIMLote);
$obRCIMProprietario  = new RCIMProprietario ( $obRCIMImovel );
$obRCGM              = new RCGM;

$obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricao"]);
$stProprietarios = '';

$obRCIMProprietario->listarProprietariosPorImovel($rsProprietarios );

if ( $rsProprietarios->getNumLinhas() > 0 ) {
    $rsProprietarios->addFormatacao( "cota", "NUMERIC_BR" );
    $stProprietarios  = "<div style='width:50px;float:left;font-weight:bold;'>CGM</div>";
    $stProprietarios .= "<div style='width:400px;float:left;font-weight:bold;'>Nome</div>";
    $stProprietarios .= "<div style='width:50px;float:left;font-weight:bold;'>Cota</div><BR>";
    while (!$rsProprietarios->eof()) {
            $inNumCgm   = $rsProprietarios->getCampo("numcgm"   );
            $nuCota     = $rsProprietarios->getCampo("cota"   )." %";
            $obRCGM->setNumCGM  ($inNumCgm  );
            $obRCGM->consultar  ( $rsCGM    );
            $arProprietarios[$inCont][ 'inSeq'   ] = $inCont;
            $arProprietarios[$inCont][ 'cgm'     ] = $inNumCgm;
            $arProprietarios[$inCont][ 'nome'   ] = $obRCGM->getNomCGM();

            $stProprietarios .= "<div style='width:50px;float:left;'>".$inNumCgm."</div>";
            $stProprietarios .= "<div style='width:400px;float:left;'>".$obRCGM->getNomCGM()."</div>";
            $stProprietarios .= "<div style='width:60px;float:left;'>".$nuCota."</div><BR>";
//            $stProprietarios .= "<div style='width:50px;float:left;'>".$inNumCgm . ' - '.$obRCGM->getNomCGM(). '<><br>';

            $rsProprietarios->proximo();
            $inCont++;
    }
} else {
    $stProprietarios = $_REQUEST['inNumCgm']. ' - '. $_REQUEST['inNomCgm'];
}

// HIDDENS
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnInscricao = new Hidden;
$obHdnInscricao->setName    ( "inInscricao"     );
$obHdnInscricao->setValue   ( $inInscricao      );

$obHdnNumCgm = new Hidden;
$obHdnNumCgm->setName    ( "inNumCgm"   );
$obHdnNumCgm->setValue   ( $inNumCgm    );

// COMPONENTES
$obLabelContribuinte = new Label;
if ( $rsProprietarios->getNumLinhas() > 1 )
    $obLabelContribuinte->setRotulo ( "Contribuintes"                );
else
    $obLabelContribuinte->setRotulo ( "Contribuinte"                );
$obLabelContribuinte->setValue ( $stProprietarios );

$obLabelInscricao = new Label;
$obLabelInscricao->setRotulo( "Inscrição Imobiliária"       );
$obLabelInscricao->setValue ( $inInscricao." - ".$stDados   );

// situacao do imovel
include_once(CAM_GT_ARR_MAPEAMENTO."FARRSituacaoImovel.class.php");
$obFARRSituacao = new FARRSituacaoImovel;
$obFARRSituacao->executaFuncao($rsSituacao, $inInscricao.",'".date('Y-m-d')."'");
$stSituacaoImovel = explode("*-*",$rsSituacao->getCampo('valor'));
if ($stSituacaoImovel[0] == 'Baixado') {
    $stHintSituacao  = "Data de Baixa: ".$stSituacaoImovel[1];
    $stHintSituacao .= "<hr>Início: ".$stSituacaoImovel[2];
    $stHintSituacao .= "<br>Fim:    ".$stSituacaoImovel[3];
    $stHintSituacao .= "<hr>Justificativa:    ".$stSituacaoImovel[4];
} else {
    $stHintSituacao = $stSituacaoImovel[0];
}

$obLblSituacao = new Label;
$obLblSituacao->setRotulo   ( "Situação do Imóvel"      );
$obLblSituacao->setValue    ( $stSituacaoImovel[0]      );
$obLblSituacao->setTitle    ( $stHintSituacao           );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                   );
$obFormulario->addHidden    ( $obHdnCtrl                );
$obFormulario->addHidden    ( $obHdnAcao                );

$obFormulario->addTitulo    ( "Dados do Imóvel"         );
$obFormulario->addHidden    ( $obHdnInscricao           );
$obFormulario->addHidden    ( $obHdnNumCgm              );
$obFormulario->addComponente( $obLabelContribuinte      );
$obFormulario->addComponente( $obLabelInscricao         );
$obFormulario->addComponente( $obLblSituacao        );
$obFormulario->show();

include_once(CAM_GT_ARR_NEGOCIO."RARRAvaliacaoImobiliaria.class.php");
$obRARRAvaliacaoImobiliaria = new RARRAvaliacaoImobiliaria;
$obRARRAvaliacaoImobiliaria->obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricao"]);
$obRARRAvaliacaoImobiliaria->listarVenaisImoveisConsulta($rsVenais);

$rsVenais->addFormatacao( 'venal_territorial', 'NUMERIC_BR');
$rsVenais->addFormatacao( 'venal_predial', 'NUMERIC_BR');
$rsVenais->addFormatacao( 'venal_total', 'NUMERIC_BR');

$obListaVenais = new Lista;
$obListaVenais->setRecordSet          ( $rsVenais           );
$obListaVenais->setTitulo             ( "Lista Valores Venais");
$obListaVenais->setMostraPaginacao    ( false                 );
$obListaVenais->addCabecalho();
$obListaVenais->ultimoCabecalho->addConteudo("&nbsp;");
$obListaVenais->ultimoCabecalho->setWidth( 5 );
$obListaVenais->commitCabecalho();
$obListaVenais->addCabecalho();
$obListaVenais->ultimoCabecalho->addConteudo("Exercício");
$obListaVenais->ultimoCabecalho->setWidth( 5 );
$obListaVenais->commitCabecalho();
$obListaVenais->addCabecalho();
$obListaVenais->ultimoCabecalho->addConteudo("Data");
$obListaVenais->ultimoCabecalho->setWidth( 10 );
$obListaVenais->commitCabecalho();
$obListaVenais->addCabecalho();
$obListaVenais->ultimoCabecalho->addConteudo("Venal Territorial");
$obListaVenais->ultimoCabecalho->setWidth( 15 );
$obListaVenais->commitCabecalho();
$obListaVenais->addCabecalho();
$obListaVenais->ultimoCabecalho->addConteudo("Venal Predial");
$obListaVenais->ultimoCabecalho->setWidth( 15 );
$obListaVenais->commitCabecalho();
$obListaVenais->addCabecalho();
$obListaVenais->ultimoCabecalho->addConteudo("Venal Total");
$obListaVenais->ultimoCabecalho->setWidth( 15 );
$obListaVenais->commitCabecalho();
$obListaVenais->addCabecalho();
$obListaVenais->ultimoCabecalho->addConteudo("Tipo");
$obListaVenais->ultimoCabecalho->setWidth( 10 );
$obListaVenais->commitCabecalho();

$obListaVenais->addDado();
$obListaVenais->ultimoDado->setCampo          ( "exercicio" );
$obListaVenais->ultimoDado->setAlinhamento    ( "CENTRO" );
$obListaVenais->commitDado();
$obListaVenais->addDado();
$obListaVenais->ultimoDado->setCampo          ( "data" );
$obListaVenais->ultimoDado->setAlinhamento    ( "CENTRO" );
$obListaVenais->commitDado();
$obListaVenais->addDado();
$obListaVenais->ultimoDado->setCampo          ( "venal_territorial" );
$obListaVenais->ultimoDado->setAlinhamento    ( "DIREITA");
$obListaVenais->commitDado();
$obListaVenais->addDado();
$obListaVenais->ultimoDado->setCampo          ( "venal_predial" );
$obListaVenais->ultimoDado->setAlinhamento    ( "DIREITA");
$obListaVenais->commitDado();
$obListaVenais->addDado();
$obListaVenais->ultimoDado->setCampo          ( "venal_total" );
$obListaVenais->ultimoDado->setAlinhamento    ( "DIREITA");
$obListaVenais->commitDado();
$obListaVenais->addDado();
$obListaVenais->ultimoDado->setCampo          ( "tipo" );
$obListaVenais->ultimoDado->setAlinhamento    ( "CENTRO" );
$obListaVenais->commitDado();

$obListaVenais->show();

$obRARRLancamento = new RARRLancamento ( new RARRCalculo );
$obRARRLancamento->obRCIMImovel->setNumeroInscricao( $inInscricao);
$obRARRLancamento->roRARRCalculo->setExercicio($_REQUEST["stExercicio"]);
$obRARRLancamento->listarLancamentoConsulta($rsLista);

$rsLista->ordena("cod_lancamento", "DESC");

$rsLista->addFormatacao('valor_calculado', 'NUMERIC_BR' );
$rsLista->addFormatacao('valor_lancamento', 'NUMERIC_BR' );

// lista de lançamentos
$obLista = new Lista;
$obLista->setRecordSet  ( $rsLista );
$obLista->setTitulo     ( "Lista de Lançamentos" );
$obLista->setMostraPaginacao ( false);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Lançamento");
$obLista->ultimoCabecalho->setWidth( 10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Referência");
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Parcelas");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Cotas Únicas");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor Calculado");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor Lançado");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Tipo");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_lancamento" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "timestamp_calculo" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "origem" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_parcelas" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_unicas" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor_calculado" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor_lancamento" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "tipo_calculo" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

// Define ACOES
$obLista->addAcao();
$stAcao = "consultar";
$obLista->ultimaAcao->setAcao   ( $stAcao );
$obLista->ultimaAcao->addCampo  ( "&inCodLancamento", "cod_lancamento"          );
$obLista->ultimaAcao->addCampo  ( "&inInscricao"    , "inscricao"               );
$obLista->ultimaAcao->addCampo  ( "&inCodModulo"    , "cod_modulo"              );
$obLista->ultimaAcao->addCampo  ( "&stOrigem"       , "origem"                  );
$obLista->ultimaAcao->addCampo  ( "&inNumCgm"       , "numcgm"                  );
$obLista->ultimaAcao->addCampo  ( "&inNomCgm"       , "nom_cgm"                 );
$obLista->ultimaAcao->addCampo  ( "&inCodGrupo"     , "cod_grupo"               );
$obLista->ultimaAcao->addCampo  ( "&stDados"        , "dados_complementares"    );
$obLista->ultimaAcao->setLink($stCaminho.$pgForm."?".Sessao::getId().$stLink."&stAcao=".$stAcao."&stCtrl=consultar&stFormOrigem=FMConsultaImovel" );
$obLista->commitAcao();

$obLista->show();

$obRARRLancamento->roRARRCalculo->obRCIMImovel->setNumeroInscricao($inInscricao);
$obRARRLancamento->roRARRCalculo->listarConsulta($rsCalculos);

$stAnt = '';
$arTmp = array();
$inC = 0;
while ( !$rsCalculos->eof() ) {
    if ($inC == 0) {
        $arTmp[] = array(   "data" => $rsCalculos->getCampo('data'),
                            "cod_calculo" => $rsCalculos->getCampo('cod_calculo'),
                            "credito" => $rsCalculos->getCampo('credito'),
                            "exercicio" => $rsCalculos->getCampo('exercicio'),
                            "vlr" => number_format( $rsCalculos->getCampo('vlr'), 2, ",","." ),
                            "ps" => "&nsbp;"
                        );
        $stAnt = $rsCalculos->getCampo('data');
        $inC++;
    } else {
        if ( $stAnt == $rsCalculos->getCampo('data')) {
            $arTmp[$inC - 1]['data'] .= "";
            $arTmp[$inC - 1]['cod_calculo'] .= "<br>".$rsCalculos->getCampo('cod_calculo');
            $arTmp[$inC - 1]['credito'] .= "<br>".$rsCalculos->getCampo('credito');
            $arTmp[$inC - 1]['exercicio'] .= "<br>".$rsCalculos->getCampo('exercicio');
            $arTmp[$inC - 1]['vlr'] .= "<br>".number_format( $rsCalculos->getCampo('vlr'), 2, ",","." );
            $arTmp[$inC - 1]['ps'] .= "<br>".$rsCalculos->getCampo('ps');
            $stAnt = $rsCalculos->getCampo('data');
        } else {
            $arTmp[$inC] = array(   "data" => $rsCalculos->getCampo('data'),
                                "cod_calculo" => $rsCalculos->getCampo('cod_calculo'),
                                "credito" => $rsCalculos->getCampo('credito'),
                                "exercicio" => $rsCalculos->getCampo('exercicio'),
                                "vlr" => number_format( $rsCalculos->getCampo('vlr'), 2, ",","." ),
                                "ps" => "&nsbp;"
                            );
            $stAnt = $rsCalculos->getCampo('data');
            $inC++;
        }
    }
    $rsCalculos->proximo();
}
$rsCalculos->preenche( $arTmp);
$rsCalculos->setPrimeiroElemento();
$rsCalculos->addFormatacao('valor','NUMERIC_BR');

// lista de calculos
$obLista = new Lista;
$obLista->setRecordSet  ( $rsCalculos );
$obLista->setMostraPaginacao ( false);
$obLista->setTitulo     ( "Lista de Cálculos não Lançados" );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data de Cálculo");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Cálculo");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Crédito");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Exercício");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor Calculado");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "data" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_calculo" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "credito" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "exercicio" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vlr" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->show();

$stFiltro = Sessao::read( 'filtro' );
$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick( "Cancelar('".$stLocation."');" );

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->defineBarra( array( $obButtonVoltar), "left", "" );
$obFormulario->show();

?>
