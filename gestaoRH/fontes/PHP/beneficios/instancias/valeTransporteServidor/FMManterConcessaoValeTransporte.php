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
    * Formulário de Vale-Tranporte Servidor
    * Data de Criação: 11/10/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30880 $
    $Name$
    $Author: souzadl $
    $Date: 2006-09-26 07:00:14 -0300 (Ter, 26 Set 2006) $

    * Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioContratoServidorConcessaoValeTransporte.class.php"        );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConcessaoValeTransporte";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
Sessao::write('stAcao', $stAcao);

$arSessaoLink = Sessao::read('link');

include_once ($pgJS);
include_once ($pgOcul);

if ($stAcao == "alterar") {
    $obRContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
    if ($arSessaoLink['stConcessao'] == 'grupo') {
        $stConcessao        = $arSessaoLink['stConcessao'];
        $stDescricaoGrupo   = $_REQUEST['stGrupo'];
        $inCodGrupo         = $_REQUEST['inCodGrupo'];
        $inCodMes           = $_REQUEST['inCodMes'];
        $inAno              = $_REQUEST['inExercicio'];
        Sessao::write('inCodGrupo', $inCodGrupo);
        $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
        $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo( $inCodGrupo );
        $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes( $inCodMes );
        $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio( $inAno );
        $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->listarConcessoesCadastradasPorGrupo( $rsGrupoConcessaoValeTransporte );
        $arTemp = $rsGrupoConcessaoValeTransporte->getElementos();
        $rsConcessoes = new recordset;
        $rsConcessoes->preenche( $arTemp );
    } elseif ($arSessaoLink['stConcessao'] == 'contrato' or $arSessaoLink['stConcessao'] == 'cgm_contrato') {
        $inCodGrupo    = $_REQUEST['inCodGrupo'];
        $inCodContrato = $_REQUEST['inCodContrato'];
        $inAno         = $_REQUEST['inExercicio'];
        $inCodMes      = $_REQUEST['inCodMes'];
        $inRegistro    = $_REQUEST['inRegistro'];
        $inNumCgm      = $_REQUEST['numcgm'];
        $stNomCgm      = $_REQUEST['nom_cgm'];
        $stConcessao   = $arSessaoLink['stConcessao'];
        if ($_REQUEST['boUtilizarGrupo']) {
            $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
            $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo( $inCodGrupo );
            $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio( $inAno );
            $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->listarConcessoesCadastradasPorGrupo( $rsContratoServidorConcessao );
        } else {
            $obRContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
            $obRContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->setCodContrato( $inCodContrato );
            $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes( $inCodMes );
            $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio( $inAno );
            $obRContratoServidorConcessaoValeTransporte->listarContratoServidorConcessaoValeTransporte($rsContratoServidorConcessao);
        }
        $arTemp = ( is_array($rsContratoServidorConcessao->getElementos()) ) ? $rsContratoServidorConcessao->getElementos() : array();
        $rsConcessoes = new recordset;
        $rsConcessoes->preenche( $arTemp );
    } elseif ($arSessaoLink['stConcessao'] == 'vale-transporte') {
        $inCodValeTransporte = $_REQUEST['inCodValeTransporte'];
        $inAno               = $_REQUEST['inExercicio'];
        $inCodMes            = $_REQUEST['inCodMes'];
        $inRegistro          = $_REQUEST['inRegistro'];
        $inCodGrupo          = $_REQUEST['inCodGrupo'];
        if ($arSessaoLink['stConcessaoVT'] == 'contrato') {
            $obRContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
            $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRBeneficioValeTransporte->setCodValeTransporte( $inCodValeTransporte );
            $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes( $inCodMes );
            $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio( $inAno );
            $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->listarValesTransportesCadastrados($rsConcessoesValeTransporte);
        } else {
            Sessao::write('inCodGrupo', $inCodGrupo);
            $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
            $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo( $inCodGrupo );
            $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio( $inAno );
            $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->listarConcessoesCadastradasPorGrupo( $rsConcessoesValeTransporte );
        }
        $arTemp = $rsConcessoesValeTransporte->getElementos();
        $rsConcessoes = new recordset;
        $rsConcessoes->preenche( $arTemp );
    }

    $arConcessoes = array();
    $inId         = 1;
    while ( !$rsConcessoes->eof() ) {
        if ($arSessaoLink['stConcessao'] == 'grupo' or $_REQUEST['boUtilizarGrupo']) {
            $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodConcessao( $rsConcessoes->getCampo('cod_concessao') );
            $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes      ( $rsConcessoes->getCampo('cod_mes')       );
            $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio   ( $rsConcessoes->getCampo('exercicio')     );
            $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->listarConcessaoValeTransporte( $rsConcessaoValeTransporte );
            $obRBeneficioConcessaoValeTransporte = &$obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte;
        } elseif ($arSessaoLink['stConcessao'] == 'contrato' or $arSessaoLink['stConcessao'] == 'cgm_contrato') {
            $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodConcessao( $rsConcessoes->getCampo('cod_concessao') );
            $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes      ( $rsConcessoes->getCampo('cod_mes')       );
            $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio   ( $rsConcessoes->getCampo('exercicio')     );
            $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->listarConcessaoValeTransporte( $rsConcessaoValeTransporte );
            $obRBeneficioConcessaoValeTransporte = &$obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte;
        } elseif ($arSessaoLink['stConcessao'] == 'vale-transporte') {
            if ($arSessaoLink['stConcessaoVT'] == 'contrato') {
                $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodConcessao( $rsConcessoes->getCampo('cod_concessao') );
                $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes      ( $rsConcessoes->getCampo('cod_mes')       );
                $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio   ( $rsConcessoes->getCampo('exercicio')     );
                $obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->listarConcessaoValeTransporte( $rsConcessaoValeTransporte );
                $obRBeneficioConcessaoValeTransporte = &$obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte;
            }
            if ($arSessaoLink['stConcessaoVT'] == 'grupo') {
                $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodConcessao( $rsConcessoes->getCampo('cod_concessao') );
                $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes      ( $rsConcessoes->getCampo('cod_mes')       );
                $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio   ( $rsConcessoes->getCampo('exercicio')     );
                $obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->listarConcessaoValeTransporte( $rsConcessaoValeTransporte );
                $obRBeneficioConcessaoValeTransporte = &$obRContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte;
            }
        }
        $arTemp = array();
        $arTemp['inId']                 = $inId;
        $arTemp['stValeTransporte']     = $rsConcessaoValeTransporte->getCampo('vale_transporte');
        $arTemp['inCodValeTransporte']  = $rsConcessaoValeTransporte->getCampo('cod_vale_transporte');
        $arTemp['inAno']                = $rsConcessaoValeTransporte->getCampo('exercicio');
        $arTemp['inCodMes']             = ( strlen($rsConcessaoValeTransporte->getCampo('cod_mes')) == 1 )? '0'.$rsConcessaoValeTransporte->getCampo('cod_mes') : $rsConcessaoValeTransporte->getCampo('cod_mes');
        $arTemp['stTipo']               = ( $rsConcessaoValeTransporte->getCampo('cod_tipo') == 1 )? 'Mensal' : 'Diários';
        $arTemp['inCodTipo']            = $rsConcessaoValeTransporte->getCampo('cod_tipo');
        $arTemp['inCodCalendario']      = $rsConcessaoValeTransporte->getCampo('cod_calendario');
        $arTemp['dtVigencia']           = $rsConcessoes->getCampo('vigencia');
        $arTemp['inQuantidadeMensal']   = $rsConcessaoValeTransporte->getCampo('quantidade');
        if ( $rsConcessaoValeTransporte->getCampo('cod_tipo') == 2 ) {
            //$obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->addRBeneficioConcessaoValeTransporteSemanal();
            //$obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->listarConcessaoValeTransporteSemanal( $rsConcessaoValeTransporteSemanal );
            $obRBeneficioConcessaoValeTransporte->addRBeneficioConcessaoValeTransporteSemanal();
            $obRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->listarConcessaoValeTransporteSemanal( $rsConcessaoValeTransporteSemanal );
            $arQuantidadeSemanal = array();
            while ( !$rsConcessaoValeTransporteSemanal->eof() ) {
                $arTempSemanal = array();
                $arTempSemanal['boObrigatorio'] = ( $rsConcessaoValeTransporteSemanal->getCampo('obrigatorio') == 't' ) ? 'on' : '';
                $arTempSemanal['inQuantidade']  = $rsConcessaoValeTransporteSemanal->getCampo('quantidade');
                $arQuantidadeSemanal[] = $arTempSemanal;
                $rsConcessaoValeTransporteSemanal->proximo();
            }
            $arQuantidadeMensal = array();
            //$obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->addRBeneficioConcessaoValeTransporteDiario();
            //$obRContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->listarConcessaoValeTransporteDiario( $rsConcessaoValeTransporteDiario );
            $obRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->addRBeneficioConcessaoValeTransporteDiario();
            $obRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->listarConcessaoValeTransporteDiario( $rsConcessaoValeTransporteDiario );

            while ( !$rsConcessaoValeTransporteDiario->eof() ) {
                $arTempDiario = array();
                $arTempDiario['stData']        = $rsConcessaoValeTransporteDiario->getCampo('dt_dia');
                $arTempDiario['boObrigatorio'] = ( $rsConcessaoValeTransporteDiario->getCampo('obrigatorio') == 't' ) ? 'on' : '';
                $arTempDiario['inQuantidade']  = $rsConcessaoValeTransporteDiario->getCampo('quantidade');
                $arQuantidadeMensal[]          = $arTempDiario;
                $rsConcessaoValeTransporteDiario->proximo();
            }
            $arTemp['arQuantidadeSemanal']  = $arQuantidadeSemanal;
            $arTemp['arQuantidadeMensal']   = $arQuantidadeMensal;
        }
        $arTemp['inCodConcessao'] = $rsConcessaoValeTransporte->getCampo('cod_concessao');
        $arConcessoes[]           = $arTemp;
        $inId++;
        $rsConcessoes->proximo();
    }
    Sessao::write('concessoes', $arConcessoes);
} else {
    $stConcessao = "contrato";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction                                  ( $pgProc                                                       );
$obForm->setTarget                                  ( "oculto"                                                      );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName                                 ( "stAcao"                                                      );
$obHdnAcao->setValue                                ( $stAcao                                                       );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                                 ( "stCtrl"                                                      );
$obHdnCtrl->setValue                                ( ""                                                            );

$obHdnConcessao = new Hidden;
$obHdnConcessao->setName                            ( "stConcessao"                                                 );
$obHdnConcessao->setValue                           ( $stConcessao                                                  );

if ($stAcao == "incluir") {
    //Define o objeto RADIO para Concessão
    $obRdoConcessaoContrato = new Radio;
    $obRdoConcessaoContrato->setName                ( "stRdoConcessao"                                              );
    $obRdoConcessaoContrato->setId                  ( "stRdoConcessao"                                              );
    $obRdoConcessaoContrato->setTitle               ( "Informe se a concessão será para o contrato ou para o grupo" );
    $obRdoConcessaoContrato->setRotulo              ( "Concessão"                                                   );
    $obRdoConcessaoContrato->setLabel               ( "Matrícula"                                                    );
    $obRdoConcessaoContrato->setValue               ( "contrato"                                                    );
    $obRdoConcessaoContrato->setNull                ( false                                                         );
    $obRdoConcessaoContrato->obEvento->setOnChange  ( "buscaValor('geraSpan1');"                                    );
    if ($stConcessao == 'contrato' or !$stConcessao) {
        $obRdoConcessaoContrato->setChecked         ( true                                                          );
    }

    $obRdoConcessaoGrupo = new Radio;
    $obRdoConcessaoGrupo->setName                   ( "stRdoConcessao"                                              );
    $obRdoConcessaoGrupo->setId                     ( "stRdoConcessao"                                              );
    $obRdoConcessaoGrupo->setTitle                  ( "Informe se a concessão será para o contrato ou para o grupo" );
    $obRdoConcessaoGrupo->setRotulo                 ( "Concessão"                                                   );
    $obRdoConcessaoGrupo->setLabel                  ( "Grupo"                                                       );
    $obRdoConcessaoGrupo->setValue                  ( "grupo"                                                       );
    $obRdoConcessaoGrupo->setNull                   ( false                                                         );
    $obRdoConcessaoGrupo->obEvento->setOnChange     ( "buscaValor('geraSpan2');"                                    );
    if ($stConcessao == 'grupo') {
        $obRdoConcessaoGrupo->setChecked            ( true                                                          );
    }

    $obRdoConcessaoCGMContrato = new Radio;
    $obRdoConcessaoCGMContrato->setName             ( "stRdoConcessao"                                              );
    $obRdoConcessaoCGMContrato->setId               ( "stRdoConcessao"                                              );
    $obRdoConcessaoCGMContrato->setTitle            ( "Informe se a concessão será para o contrato ou para o grupo" );
    $obRdoConcessaoCGMContrato->setRotulo           ( "Concessão"                                                   );
    $obRdoConcessaoCGMContrato->setLabel            ( "CGM/Matrícula"                                                );
    $obRdoConcessaoCGMContrato->setValue            ( "cgm_contrato"                                                );
    $obRdoConcessaoCGMContrato->setNull             ( false                                                         );
    $obRdoConcessaoCGMContrato->obEvento->setOnChange( "buscaValor('geraSpan9');"                                   );
    if ($stConcessao == 'cgm_contrato') {
        $obRdoConcessaoCGMContrato->setChecked      ( true                                                          );
    }

}

if ($stAcao == 'alterar') {
    switch ($stConcessao) {
        case 'contrato':
            $stLblConcessao = "Matrícula";
        break;
        case 'cgm_contrato':
            $stLblConcessao = "CGM/Matrícula";
        break;
        case 'grupo':
            $stLblConcessao = "Grupo";
        break;
    }
    $obLblConcessao = new Label;
    $obLblConcessao->setRotulo                  ( "Concessão"                                                   );
    $obLblConcessao->setValue                   ( $stLblConcessao                                               );

}

//Define o objeto SPAN para Concessão por contrato/Concessão do Grupo
$obSpanConcessao = new Span;
$obSpanConcessao->setId                         ( "spnConcessao"                                                );

$obHdnEval = new HiddenEval;
$obHdnEval->setName ( "stEval" );
$obHdnEval->setValue( "" );

$obHdnOpcaoEval = new HiddenEval;
$obHdnOpcaoEval->setName                        ( "stOpcaoEval"                                                 );
$obHdnOpcaoEval->setValue                       ( ""                                                            );

//Define o objeto SPAN para os componente do "span3"
$obSpan3 = new Span;
$obSpan3->setId                                 ( "spnSpan3"                                                    );

$obHdnOpcaoEval3 = new HiddenEval;
$obHdnOpcaoEval3->setName                       ( "stOpcaoEval3"                                                );
$obHdnOpcaoEval3->setValue                      ( ""                                                            );

//Define o objeto SPAN para os componentes do "span4"
$obSpan4 = new Span;
$obSpan4->setId                                 ( "spnSpan4"                                                    );

$obHdnOpcaoEval4 = new HiddenEval;
$obHdnOpcaoEval4->setName                       ( "stOpcaoEval4"                                                );
$obHdnOpcaoEval4->setValue                      ( ""                                                            );

//Define o objeto SPAN para os componente do "span5"
$obSpan5 = new Span;
$obSpan5->setId                                 ( "spnSpan5"                                                    );

$obHdnOpcaoEval5 = new HiddenEval;
$obHdnOpcaoEval5->setName                       ( "stOpcaoEval5"                                                );
$obHdnOpcaoEval5->setValue                      ( ""                                                            );

//Define o objeto SPAN para os componentes do "span6"
$obSpan6 = new Span;
$obSpan6->setId                                 ( "spnSpan6"                                                    );

$obHdnOpcaoEval6 = new HiddenEval;
$obHdnOpcaoEval6->setName                       ( "stOpcaoEval6"                                                );
$obHdnOpcaoEval6->setValue                      ( ""                                                            );

//Define o objeto SPAN para os componentes do "span7"
$obSpan7 = new Span;
$obSpan7->setId                                 ( "spnSpan7"                                                    );

$obHdnOpcaoEval7 = new HiddenEval;
$obHdnOpcaoEval7->setName                       ( "stOpcaoEval7"                                                );
$obHdnOpcaoEval7->setValue                      ( ""                                                            );

//Define o objeto SPAN para os componentes do "span8"
$obSpan8 = new Span;
$obSpan8->setId                                 ( "spnSpan8"                                                    );

$obHdnOpcaoEval8 = new HiddenEval;
$obHdnOpcaoEval8->setName                       ( "stOpcaoEval8"                                                );
$obHdnOpcaoEval8->setValue                      ( ""                                                            );

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimpar"                                                   );
$obBtnLimpar->setValue                          ( "Limpar"                                                      );
$obBtnLimpar->setTipo                           ( "button"                                                      );
$obBtnLimpar->obEvento->setOnClick              ( "buscaValor('limparForm');"                                   );
$obBtnLimpar->setDisabled                       ( false                                                         );

$obBtnOK = new Ok;

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnCtrl                                                );
$obFormulario->addHidden        ( $obHdnAcao                                                );
$obFormulario->addTitulo        ( "Informações da Concessão"                                );
$obFormulario->addHidden        ( $obHdnConcessao                                           );

if ($stAcao == 'incluir') {
    $obFormulario->agrupaComponentes( array( $obRdoConcessaoContrato,$obRdoConcessaoCGMContrato,$obRdoConcessaoGrupo)    );
} else {
    $obFormulario->addComponente( $obLblConcessao                                           );
}

$obFormulario->addSpan          ( $obSpanConcessao                                          );
$obFormulario->addHidden        ( $obHdnOpcaoEval,true                                      );
$obFormulario->addHidden        ( $obHdnEval, true                                          );
$obFormulario->addSpan          ( $obSpan3                                                  );
$obFormulario->addHidden        ( $obHdnOpcaoEval3,true                                     );
$obFormulario->addSpan          ( $obSpan4                                                  );
$obFormulario->addHidden        ( $obHdnOpcaoEval4,true                                     );
$obFormulario->addSpan          ( $obSpan7                                                  );
$obFormulario->addHidden        ( $obHdnOpcaoEval7,true                                     );
$obFormulario->addSpan          ( $obSpan5                                                  );
$obFormulario->addHidden        ( $obHdnOpcaoEval5,true                                     );
$obFormulario->addSpan          ( $obSpan8                                                  );
$obFormulario->addHidden        ( $obHdnOpcaoEval8,true                                     );
$obFormulario->addSpan          ( $obSpan6                                                  );
$obFormulario->addHidden        ( $obHdnOpcaoEval6,true                                     );
if ($stAcao == 'incluir') {
    $obFormulario->setFormFocus ( $obRdoConcessaoContrato->getId()                          );
    $obFormulario->defineBarra  ( array( $obBtnOK,$obBtnLimpar )                            );
} else {
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
}
$obFormulario->show();

//Preenche os span do formulário
gerarSpans  ( true );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
