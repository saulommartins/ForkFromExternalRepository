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

    * $Id: FMConsultaArrecadacao.php 63839 2015-10-22 18:08:07Z franver $
*/

/*
$Log$
Revision 1.58  2007/07/13 19:44:05  dibueno
Exibir Label da Inscricao/Contribuinte

Revision 1.57  2007/07/12 12:56:53  dibueno
Bug #9641#

Revision 1.56  2007/03/12 14:39:07  dibueno
Bug #8608#

Revision 1.55  2007/03/02 18:46:54  dibueno
Bug #5611#

Revision 1.54  2007/03/02 14:53:25  dibueno
*** empty log message ***

Revision 1.53  2007/02/07 11:12:58  dibueno
Melhorias da consulta da arrecadacao

Revision 1.52  2007/02/05 11:07:30  dibueno
Melhorias da consulta da arrecadacao

Revision 1.51  2006/12/18 11:03:28  dibueno
Alterações para exibição dos dados da parcela quando o filtro utilizado na consulta for uma numeração

Revision 1.50  2006/12/04 15:55:49  dibueno
Bug #7667#

Revision 1.49  2006/11/06 16:36:42  dibueno
Bug #7351#

Revision 1.48  2006/09/15 11:04:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRLancamento.class.php"                                             );
include_once( CAM_GT_ARR_NEGOCIO."RARRParcela.class.php"                                                );
include_once( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"                                                );
include_once( CAM_GT_MON_NEGOCIO."RMONCredito.class.php"                                            );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaArrecadacao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

include_once($pgJs);

// passar do request pra variaveis
$inCodLancamento    = $_REQUEST["inCodLancamento"   ];
$inInscricao        = $_REQUEST["inInscricao"       ];
$inNumCgm           = $_REQUEST["inNumCgm"          ];
$stNomCgm           = $_REQUEST["stNomCgm"          ];
$stDados            = $_REQUEST["stDados"           ];
$inCodGrupo         = $_REQUEST["inCodGrupo"        ];
$stOrigem           = $_REQUEST["stOrigem"          ];
$inCodModulo        = $_REQUEST["inCodModulo"       ];
$inOcorrencia       = $_REQUEST['inOcorrencia'];
$stCompetencia      = $_REQUEST['stCompetencia'];

//DEFINICAO DOS COMPONENTES

Sessao::remove('stIdCarregamento');
    //* LISTAGEM DE PROPRIETARIOS
    include_once( CAM_GT_CIM_NEGOCIO . "RCIMImovel.class.php"        );
    include_once( CAM_GA_CGM_NEGOCIO . "RCGM.class.php"     );
    $obRCIMImovel        = new RCIMImovel (new RCIMLote);
    $obRCIMProprietario  = new RCIMProprietario ( $obRCIMImovel );
    $obRCGM              = new RCGM;
    /* Listar Proprietarios */
    /* Se estiver tudo certo, busca proprietarios do imovel */

    $arCGM = explode ( ' - ', $_REQUEST['stProprietarios'] );

    if ($_REQUEST["inInscricao"]) {

        if ($inCodModulo == 14) { //inscricao_economica
           $stProprietarios = $_REQUEST['stProprietarios'];
        } elseif ($inCodModulo == 12) {

            $obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricao"]);
            $stProprietarios = '';

            $obRCIMProprietario->listarProprietariosPorImovel($rsProprietarios );

            if ( $rsProprietarios->getNumLinhas() > 0 ) {
                while (!$rsProprietarios->eof()) {
                    $inNumCgm   = $rsProprietarios->getCampo("numcgm"   );
                    $obRCGM->setNumCGM  ($inNumCgm  );
                    $obRCGM->consultar  ( $rsCGM    );
                    $arProprietarios[$inCont][ 'inSeq'   ] = $inCont;
                    $arProprietarios[$inCont][ 'cgm'     ] = $inNumCgm;
                    $arProprietarios[$inCont][ 'nome'   ] = $obRCGM->getNomCGM();

                    $stProprietarios .= $inNumCgm . ' - '. $obRCGM->getNomCGM(). '<br>';

                    $rsProprietarios->proximo();
                    $inCont++;
                }
            } else {
                $stProprietarios = $_REQUEST['stProprietarios'];
            }
        } else {
            $stProprietarios = $_REQUEST['stProprietarios'];
        }
    } else {
        $stProprietarios = $_REQUEST['stProprietarios'];
    }

// HIDDENS
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnInscricao = new Hidden;
$obHdnInscricao->setName    ( "inInscricao"     );
$obHdnInscricao->setValue   ( $inInscricao      );

$obHdnDados = new Hidden;
$obHdnDados->setName ('stDados');
$obHdnDados->setValue ($stDados);

$obHdnLancamento = new Hidden;
$obHdnLancamento->setName    ( "inCodLancamento" );
$obHdnLancamento->setValue   ( $inCodLancamento  );

$obHdnNumCgm = new Hidden;
$obHdnNumCgm->setName    ( "inNumCgm"   );
$obHdnNumCgm->setValue   ( $inNumCgm    );

$obHdnCodGrupo = new Hidden;
$obHdnCodGrupo->setName    ( "inCodGrupo"   );
$obHdnCodGrupo->setValue   ( $inCodGrupo    );

$obHdnOrigemGrupo = new Hidden;
$obHdnOrigemGrupo->setName ('stOrigem');
$obHdnOrigemGrupo->setName ($stOrigem);

$obHdnProprietarios = new Hidden; // utilizado para o relatório
$obHdnProprietarios->setName ('stProprietarios');
$obHdnProprietarios->setValue ($stProprietarios);

$obHdnOcorrenciaPagamento = new Hidden; // utilizado para o relatório
$obHdnOcorrenciaPagamento->setName ('inOcorrencia');
$obHdnOcorrenciaPagamento->setValue ($inOcorrencia);

// COMPONENTES
$obLabelContribuinte = new Label;
$obLabelContribuinte->setRotulo ( "Contribuinte"                );
$obLabelContribuinte->setValue ( $stProprietarios );

if ($_REQUEST['inInscricao']) {
    $obLabelInscricao = new Label;
    if ($inCodModulo == 12) {
        $obLabelInscricao->setRotulo( "Inscrição Imobiliária"       );
    } elseif ($inCodModulo == 14) {
        $obLabelInscricao->setRotulo( "Inscrição Econômica"         );
    } else {
        $obLabelInscricao->setRotulo( "Outros"                      );
    }
    $obLabelInscricao->setValue ( $inInscricao." - ".$stDados   );
}

// busca valor venal do imovel
if ($inCodModulo == 12 || $stOrigem == "I.T.B.I.") {
    // pega ultimo valor no banco
    if ($_REQUEST['inInscricao']) {
        include_once(CAM_GT_ARR_MAPEAMENTO."FARRUltimoValorVenalLanc.class.php");
        include_once(CAM_GT_ARR_MAPEAMENTO."FARRUltimoValorVenal.class.php");

        $obFARRUltimoValorVenal = new FARRUltimoValorVenalLanc;
        $obFARRUltimoValorVenal->executaFuncao($rsUltimoVenal, $inInscricao.",".$inCodLancamento);

        $stValorVenalTotal = $rsUltimoVenal->getCampo('valor');

        $obFARRUltimoValorVenal = new FARRUltimoValorVenal;
        // estatico, depois tem que pegar o exercicio corrente do lancamento
        $obFARRUltimoValorVenal->executaFuncao($rsUltimoVenal, $inInscricao.",'".Sessao::getExercicio()."'");
        $stValorVenalTotalAtualizado = $rsUltimoVenal->getCampo('valor');

        $stValorVenalTotal = 'R$ '.number_format($stValorVenalTotal,2,',','.');
        $stValorVenalTotalA = 'R$ '.number_format($stValorVenalTotalAtualizado,2,',','.');
        // monta componente

        $obLblVenalTotal = new Label;
        $obLblVenalTotal->setRotulo   ( "Valor Venal Total"     );
        $obLblVenalTotal->setTitle    ( "Valor Venal Total Atualizado do Imóvel:<hr><i>$stValorVenalTotalA</i>");
        $obLblVenalTotal->setValue    ( $stValorVenalTotal." - ".$_REQUEST["stTipoVenal"]      );

    // situacao do imovel
        include_once(CAM_GT_ARR_MAPEAMENTO."FARRSituacaoImovel.class.php");
        $obFARRSituacao = new FARRSituacaoImovel;
        $obFARRSituacao->executaFuncao($rsSituacao, $inInscricao.",'".date('Y-m-d')."'");
        $stSituacaoImovel = $rsSituacao->getCampo('valor');

        $obLblSituacao = new Label;
        $obLblSituacao->setRotulo   ( "Situação do Imóvel"     );
        $obLblSituacao->setTitle    ( "Situação do Imóvel na Data de Hoje"     );
        $obLblSituacao->setValue    ( $stSituacaoImovel      );

    }
}

$obLblTipoCalculo = new Label;
$obLblTipoCalculo->setRotulo   ( "Tipo Cálculo"     );
$obLblTipoCalculo->setTitle    ( "Tipo Cálculo");
$obLblTipoCalculo->setValue    ( $_REQUEST["stTipoCalculo"] );

$obLblGrupoCredito = new Label;
$obLblGrupoCredito->setRotulo   ( "Grupo de Créditos"   );
$obLblGrupoCredito->setValue    ( $inCodGrupo." - ".$stOrigem );

$obLblCredito   = new Label;
$obLblCredito->setRotulo    ( "Crédito" );
$obLblCredito->setValue     ( $stOrigem );

$obLblCompetencia = new Label;
$obLblCompetencia->setRotulo   ( "Competência"   );
if ($stCompetencia == 'competencia') {
$stCompetencia = 'Não Informado';
}
$obLblCompetencia->setValue    ( $stCompetencia );

// observação
include_once(CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php");
$obTARRLancamento = new TARRLancamento;
$stFiltro = " \n\t where cod_lancamento=".$inCodLancamento;
$obTARRLancamento->recuperaObservacaoLancamento($rsObs, $stFiltro);

$obLblObs   = new Label;
$obLblObs->setRotulo    ( "Observação" );
$obLblObs->setValue     ( $rsObs->getCampo('observacao') );

$obLblObsSistema   = new Label;
$obLblObsSistema->setRotulo    ( "Observação do Sistema" );
$obLblObsSistema->setValue     ( $rsObs->getCampo('observacao_sistema') );

//processo
$obTARRLancamento->recuperaProcessoLancamento($rsPro, $stFiltro);
$stProcesso  = $rsPro->getCampo('cod_processo')."/".$rsPro->getCampo('ano_exercicio');
if ($rsPro->getCampo('resumo_assunto') )
$stProcesso .= "\n<br>".$rsPro->getCampo('resumo_assunto');
if ($rsPro->getCampo('observacoes'))
$stProcesso .= "\n<br>".$rsPro->getCampo('observacoes');
$obLblProcResumo = new Label;
$obLblProcResumo->setRotulo    ( "Processo" );
$obLblProcResumo->setValue     ( $stProcesso );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                   );
$obFormulario->addHidden    ( $obHdnAcao                );
$obFormulario->addHidden ( $obHdnCtrl );

$obFormulario->addTitulo    ( "Dados para Emissão"      );
$obFormulario->addHidden    ( $obHdnLancamento          );
$obFormulario->addHidden    ( $obHdnInscricao           );
$obFormulario->addHidden    ( $obHdnNumCgm              );
$obFormulario->addHidden    ( $obHdnCodGrupo            );
$obFormulario->addHidden    ( $obHdnDados                  );
$obFormulario->addHidden    ( $obHdnInscricao              );
$obFormulario->addHidden    ( $obHdnOrigemGrupo        );
$obFormulario->addHidden    ( $obHdnProprietarios        );
$obFormulario->addHidden    ( $obHdnOcorrenciaPagamento );

$obFormulario->addComponente( $obLabelContribuinte      );
if ( $_REQUEST['inInscricao'] && ( $inInscricao != 'inscricao') ) {
$obFormulario->addComponente( $obLabelInscricao         );
}
if ( $inCodModulo == 12 || $stOrigem == "I.T.B.I." )
    if ($_REQUEST['inInscricao']) {
        $obFormulario->addComponente( $obLblVenalTotal      );
        $obFormulario->addComponente( $obLblSituacao        );
    }
if ($inCodGrupo)
    $obFormulario->addComponente( $obLblGrupoCredito    );
else
    $obFormulario->addComponente( $obLblCredito         );

$obFormulario->addComponente( $obLblTipoCalculo );
if ($stCompetencia)
    $obFormulario->addComponente( $obLblCompetencia );

if ( $rsObs->getCampo('observacao') )
    $obFormulario->addComponente ($obLblObs);
if ( $rsObs->getCampo('observacao_sistema') )
    $obFormulario->addComponente ($obLblObsSistema);
if ( $rsPro->getCampo('cod_processo') )
    $obFormulario->addComponente ($obLblProcResumo);

$obFormulario->show();

// capturar creditos VALORES
//####################################################
$obRARRParcela = new RARRParcela( new RARRLancamento ( new RARRCalculo));
$obRARRParcela->roRARRLancamento->setCodLancamento ($inCodLancamento);
//####################################################
Sessao::write( 'transf4', array() );

if ($inCodGrupo) {         // caso seja grupo de credito
#echo 'xGRUPO';

    $obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->setCodGrupo($inCodGrupo);
    $obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->setExercicio( $inExercicio );
    $obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->listarCreditos ( $rsCreditosDescontos );
    $obRARRParcela->roRARRLancamento->listarCalculosCredito($rsCreditos);
    Sessao::write( 'tipoLancamento', 'Grupo' );
    $obRARRGrupo  = new RARRGrupo;
    $rsCreditosDescontos->setPrimeiroElemento();

    $cont = 0;
    while ( !$rsCreditosDescontos->eof() ) {
        $arlistaCreditos[$cont]['codigo'] = $rsCreditosDescontos->getCampo('cod_credito');
        $arlistaCreditos[$cont++]['desconto'] = $rsCreditosDescontos->getCampo('desconto');
        $rsCreditosDescontos->proximo();
    }

    Sessao::write( 'listaCreditos', $arlistaCreditos );
} else {                      // caso seja credito
    #echo 'xCredito';
    $obRARRParcela->roRARRLancamento->listarCalculosCredito($rsCreditos);
    #$obRARRParcela->roRARRLancamento->listarCalculosCreditoIndividual($rsCreditos);
    Sessao::write( 'tipoLancamento', 'Credito' );

    $cont = 0;
    $arlistaCreditos[$cont]['codigo'] = $rsCreditos->getCampo('cod_credito');
    $arlistaCreditos[$cont++]['desconto'] = 't';
    Sessao::write( 'listaCreditos', $arlistaCreditos );
}

$rsCreditos->addFormatacao("valor_calculado","NUMERIC_BR");
$rsCreditos->addFormatacao("valor","NUMERIC_BR");
$rsCreditos->setPrimeiroElemento();
########################### TABELA DOM
$table = new Table();
$table->setRecordset( $rsCreditos );
$table->setSummary('Lista de Créditos');

// lista zebrada
////$table->setConditional( true , "#efefef" );

$table->Head->addCabecalho( 'Código' , 10  );
$table->Head->addCabecalho( 'Descrição' , 35  );
$table->Head->addCabecalho( 'Exercício' , 5  );
$table->Head->addCabecalho( 'Valor Calculado ( R$ )' , 20  );
$table->Head->addCabecalho( 'Valor Lançado ( R$ )' , 20  );

$table->Body->addCampo( 'codigo_composto' );
$table->Body->addCampo( 'descricao_credito' );
$table->Body->addCampo( 'exercicio', "C" );
$table->Body->addCampo( 'valor_calculado', "D");
$table->Body->addCampo( 'valor', "D" );

$table->Foot->addSoma ( 'valor', "D" );

#$table->Body->addAcao( null ,  null , array( 'nome' ) );

$table->montaHTML();

echo $table->getHtml();

// listar parcelas
$obRARRParcela->listarConsulta ( $rsListaParcelas );
$rsListaParcelas->ordena ("nr_parcela");
$rsListaParcelas->addFormatacao("valor","NUMERIC_BR");
###################################### TABELA DOM

$rsListaParcelas->setPrimeiroElemento();
$boTemNumeracaoMigrada = false;
while ( !$rsListaParcelas->eof() ) {
    if ( $rsListaParcelas->getCampo('numeracao_migracao') ) {
        $boTemNumeracaoMigrada = true;
        break;
    }
    $rsListaParcelas->proximo();
}

$rsListaParcelas->setPrimeiroElemento();
$table = new TableTree();
$table->setMostrarTodos ( false );
$table->setRecordset( $rsListaParcelas );
$table->setSummary('Lista de Parcelas');

//$table->setArquivo( 'FMConsultaArrecadacaoDetalheParcela.php' );
$table->setArquivo('OCConsultaArrecadacao.php?&stCtrl=detalheParcela&');
$table->setParametros( array( "cod_lancamento" , "numeracao" , 'exercicio', 'cod_parcela', 'pagamento', 'database_br', 'vencimento', 'ocorrencia_pagamento', 'info_parcela') );
#$table->setComplementoParametros( "stSonic=Guerra");

// lista zebrada
//$table->setConditional( true , "#efefef" );
// destaca campo boolean no recordset

if ($boTemNumeracaoMigrada) {
    $table->Head->addCabecalho( 'Numeração' , 15  );
    $table->Head->addCabecalho( 'Numeração Migrada' , 15  );
} else {
    $table->Head->addCabecalho( 'Numeração' , 25  );
}
$table->Head->addCabecalho( 'Parcela' , 10  );

if ($boTemNumeracaoMigrada) {
    $table->Head->addCabecalho( 'Valor' , 10  );
} else {
    $table->Head->addCabecalho( 'Valor' , 15  );
}

$table->Head->addCabecalho( 'Vencimento' , 10  );
$table->Head->addCabecalho( 'Situação Atual' , 20  );

$table->Body->addCampo( '[numeracao]/[exercicio]', "C" );
if ($boTemNumeracaoMigrada) {
    $table->Body->addCampo( '[numeracao_migracao]' );
}
$table->Body->addCampo( 'info_parcela', "C" );
$table->Body->addCampo( 'valor', 'D' );
$table->Body->addCampo( 'vencimento_original','C' );
$table->Body->addCampo( 'situacao_resumida', 'C', "[situacao]" );

$table->montaHTML();

echo $table->getHtml();

if ($_REQUEST['stFormOrigem']) {
    $stLocation =  $_REQUEST['stFormOrigem'].'.php?'.Sessao::getId().'&stAcao='.$stAcao;
    $stLocation .= "&inInscricao=".$_REQUEST['inInscricao']."&stDados=".$_REQUEST['stDados'];
} else {
    $stFiltro = Sessao::read( 'filtro' );
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro."&pg=".$pg."&pos=".$pos;
}

$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick( "Cancelar('".$stLocation."');" );

$stLocation2  = 'OCGeraRelatorioConsultaArrecadacao.php?'.Sessao::getId().'&stAcao='.$stAcao;
$stLocation2 .= '&inCodLancamento='.$inCodLancamento.'&stProprietarios='.$stProprietarios.'&inCodGrupo='.$inCodGrupo;
$stLocation2 .= '&inInscricao='.$inInscricao.'&stDados='.$stDados;
$stLocation2 .= '&stOrigem='.$stOrigem.'&stSituacao='.$stSituacaoImovel.'&flValorVenal='.$stValorVenalTotal;
$stLocation2 .= '&inCodModulo='.$inCodModulo.'&inExercicio='.$inExercicio;

$obButtonRelatorio = new Button;
$obButtonRelatorio->setName  ( "Relatorio" );
$obButtonRelatorio->setValue ( "Relatorio" );
$obButtonRelatorio->obEvento->setOnClick( "window.parent.frames['oculto'].location='".$stLocation2."';");

$obButtonCarne = new Button;
$obButtonCarne->setName  ( "Imprimir Carnê" );
$obButtonCarne->setValue ( "ImprimirCarne" );
//$obButtonCarne->obEvento->setOnClick( "relatorio();");

// detalhes
/*$obSpnDetalhes = new Span;
$obSpnDetalhes->setId('spnDetalhes');
*/
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
#$obFormulario->addSpan($obSpnDetalhes);

$obFormulario->defineBarra( array( $obButtonRelatorio ), "left", "" );
$obFormulario->defineBarra( array( $obButtonVoltar), "left", "" );

/*if ($_REQUEST["inNumeracao"] && $_REQUEST['dtVencimentoPR']) {

    $boFlagHeaders = true;
    //include_once("FMConsultaArrecadacaoDetalheParcela.php");
    $stDados = "'parcela', ";
    $stDados .= "'".$_REQUEST['inCodLancamento']. "', '".$_REQUEST['inNumeracao']. "', ";
    $stDados .= "'".$_REQUEST['stExercicio']. "', '".$_REQUEST['inCodParcela']. "', ";
    $stDados .= "'".$_REQUEST['dtPagamento']. "', '".$_REQUEST['dtDataBase']. "', ";
    $stDados .= "'".$_REQUEST['dtVencimentoPR']."'";
    $stDados .= ", '".$_REQUEST['inOcorrencia']."'";

    sistemaLegado::executaFramePrincipal ("javaScript:visualizarDetalhesAtualizaReemitida(". $stDados.");");
}
*/

$obFormulario->show();
?>
