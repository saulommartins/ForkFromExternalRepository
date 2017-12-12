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
    * Oculto de Vale-Tranporte Servidor
    * Data de Criação: 11/10/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30925 $
    $Name$
    $Author: tiago $
    $Date: 2007-06-28 14:51:34 -0300 (Qui, 28 Jun 2007) $

    * Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                        );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                                         );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioContratoServidorConcessaoValeTransporte.class.php"       );
include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroContrato.class.php'                                     );
include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroCGMContrato.class.php'                                  );

global $stAcao;

$stAcao = $request->get('stAcao');

function geraSpan1($boExecuta=false)
{
    global $inRegistro, $inNumCgm, $stNomCgm, $stAcao;
    $stCgm = "";
    if ($inNumCgm != "") {
        $stNomCgm = $inNumCgm ."-".stripslashes($stNomCgm) ;
    }

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                    ( "Concessão por Matrícula"                  );

    if ($stAcao == 'incluir') {

        $obIFiltroContrato = new IFiltroContrato;
        $obIFiltroContrato->setInformacoesFuncao  ( false );

        $obIFiltroContrato->geraFormulario ( $obFormulario );
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);

        $obChkUtilizarGrupo = new CheckBox;
        $obChkUtilizarGrupo->setName                ( "boUtilizarGrupo"                         );
        $obChkUtilizarGrupo->setRotulo              ( "Utilizar Grupo"                          );
        $obChkUtilizarGrupo->setTitle               ( "Informe se a concessão será por grupo."  );
        $obChkUtilizarGrupo->setValue               ( "true"                                    );
        $obChkUtilizarGrupo->obEvento->setOnChange  ( "buscaValor('geraSpan4')"                 );
        $obFormulario->addComponente            ( $obChkUtilizarGrupo                       );

        $stConcessao = ( $_POST['stRdoConcessao'] != "" ) ? $_POST['stRdoConcessao'] : "contrato";
        $stJs .= "f.stConcessao.value  = '$stConcessao';    \n";

    } else {

        $obLblContrato = new Label;
        $obLblContrato->setRotulo                   ( "Matrícula"                                );
        $obLblContrato->setValue                    ( $inRegistro                               );

        $obHdnContrato = new Hidden;
        $obHdnContrato->setName                     ( "inRegistro"                              );
        $obHdnContrato->setValue                    ( $inRegistro                               );
        $stLblGrupo = ( $_GET['boUtilizarGrupo'] ) ? "Sim" : "Não";
        $obLblGrupo = new Label;
        $obLblGrupo->setRotulo                      ( "Utilizar Grupo"                          );
        $obLblGrupo->setValue                       ( $stLblGrupo                               );

        $obHdnGrupo = new Hidden;
        $obHdnGrupo->setName                        ( "boUtilizarGrupo"                         );
        $obHdnGrupo->setValue                       ( $boUtilizarGrupo                          );

        $obFormulario->addComponente            ( $obLblContrato                            );
        $obFormulario->addHidden                ( $obHdnContrato                            );
        $obFormulario->addComponente            ( $obLblGrupo                               );
        $obFormulario->addHidden                ( $obHdnGrupo                               );
    }

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "window.parent.frames['telaPrincipal'].document.getElementById('spnConcessao').innerHTML ='".$obFormulario->getHTML()."';\n" ;
    $stJs .= "f.stOpcaoEval.value  = '".$stEval."';                     \n";

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }

}

function geraSpan2($boExecuta=false)
{
    global $stDescricaoGrupo,$stAcao;
    $obTxtDescricaoGrupo= new TextBox;
    $obTxtDescricaoGrupo->setRotulo         ( "Descrição do grupo"                                      );
    $obTxtDescricaoGrupo->setTitle          ( "Informe a descrição do grupo que possuirá a concessão."  );
    $obTxtDescricaoGrupo->setName           ( "stDescricaoGrupo"                                        );
    $obTxtDescricaoGrupo->setValue          ( $stDescricaoGrupo                                         );
    $obTxtDescricaoGrupo->setMaxLength      ( 80                                                        );
    $obTxtDescricaoGrupo->setSize           ( 40                                                        );
    $obTxtDescricaoGrupo->setNull           ( false                                                     );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                ( "Concessão por Grupo"                                     );
    $obFormulario->addComponente            ( $obTxtDescricaoGrupo                                      );

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    if ($stAcao == 'incluir') {
        $stConcessao = ( $_POST['stRdoConcessao'] != "" ) ? $_POST['stRdoConcessao'] : "contrato";
        $stJs .= "f.stConcessao.value  = '$stConcessao';    \n";
    }
    $stJs .= "window.parent.frames['telaPrincipal'].document.getElementById('spnConcessao').innerHTML ='".$obFormulario->getHTML()."';" ;
    $stJs .= "f.stOpcaoEval.value  = '".$stEval."';     \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function geraSpan3($boExecuta=false)
{
    global $stMes, $inAno;

    $obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
    $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->listarMes( $rsMes );
    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->listarTipo( $rsTipo );
    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->listar( $rsCalendario );

    while (!$rsMes->eof()) {
        if ( $rsMes->getCampo('cod_mes') == $_GET['inCodMes'] ) {
            $inCodMes = $rsMes->getCampo('cod_mes');
            $stLblMes = $rsMes->getCampo('descricao');
        }
        $rsMes->proximo();
    }
    $rsMes->setPrimeiroElemento();
    $dtVigencia = date('d/m/Y');

    if ( Sessao::read('stAcao') == 'incluir' ) {
        // buscando mes e ano da competencia atual
        include_once ( CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoPeriodoMovimentacao.class.php' );
        $obRFolhaPagamentoPeriodoMovimentacao =  new RFolhaPagamentoPeriodoMovimentacao ;
        $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
        $arData = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));
        $inCodMes = $arData[1];
        $inAno    = $arData[2];
        Sessao::write('inCodMes', $inCodMes);
        Sessao::write('inAno', $inAno);

        $obTxtAno = new Inteiro;
        $obTxtAno->setName              ( "inAno"                           );
        $obTxtAno->setRotulo            ( "Ano"                             );
        $obTxtAno->setTitle             ( "Informe o ano da concessão"      );
        $obTxtAno->setValue             ( $inAno                            );
        $obTxtAno->setNull              ( false                             );
        $obTxtAno->setAlign             ( "RIGHT"                           );
        $obTxtAno->setMaxLength         ( 4                                 );
        $obTxtAno->setSize              ( 4                                 );
        $obTxtAno->setMaxValue          ( 2050                              );
        $obTxtAno->setMinValue          ( date("Y")                         );
        $obTxtAno->setNegativo          ( false                             );
        $obTxtAno->setValue             ( date("Y")                         );
        $obTxtAno->obEvento->setOnChange  ( "buscaValor('habilitaQuantidadeSemanal'); buscaValor('preencheMes');" );

        $obTxtCodMes = new TextBox;
        $obTxtCodMes->setRotulo         ( "Mês"                             );
        $obTxtCodMes->setTitle          ( "Informe o mês da concessão"      );
        $obTxtCodMes->setName           ( "inCodMes"                        );
        $obTxtCodMes->setValue          ( $inCodMes                         );
        $obTxtCodMes->setMaxLength      ( 2                                 );
        $obTxtCodMes->setSize           ( 10                                );
        $obTxtCodMes->setNull           ( false                             );
        $obTxtCodMes->setInteiro        ( true                              );
        $obTxtCodMes->obEvento->setOnChange  ( "buscaValor('habilitaQuantidadeSemanal');" );

        $obCmbMes = new Select;
        $obCmbMes->setName              ( "stMes"                           );
        $obCmbMes->setTitle             ( "Informe o mês da concessão"      );
        $obCmbMes->setStyle             ( "width: 250px"                    );
        $obCmbMes->setRotulo            ( "Mês"                             );
        $obCmbMes->setValue             ( $inCodMes                         );
        $obCmbMes->setNull              ( false                             );
        $obCmbMes->addOption            ( "", "Selecione"                   );
        $obCmbMes->setCampoID           ( "[cod_mes]"                       );
        $obCmbMes->setCampoDesc         ( "[descricao]"                     );
        $obCmbMes->obEvento->setOnChange( "buscaValor('habilitaQuantidadeSemanal');" );

        $arComponentes = array();
        while ( !$rsTipo->eof() ) {
            $stVariavel = "obRdoTipo".$rsTipo->getCampo('descricao');
            $$stVariavel = new Radio;
            $$stVariavel->setName       ( "inCodTipo"                                                   );
            $$stVariavel->setTitle      ( "Informe o tipo da concessão."                                );
            $$stVariavel->setRotulo     ( "Tipo"                                                        );
            $$stVariavel->setLabel      ( $rsTipo->getCampo('descricao')                                );
            $$stVariavel->setValue      ( $rsTipo->getCampo('cod_tipo')                                 );
            $$stVariavel->setNull       ( false                                                         );
            $$stVariavel->obEvento->setOnChange  ( "buscaValor('sequenciaAlternativa1');"               );
            if ( $inCodTipo == $rsTipo->getCampo('cod_tipo') or (!$inCodTipo and $rsTipo->getCampo('descricao') == 'Mensal') ) {
                $$stVariavel->setChecked( true                                                          );
            }
            $arComponentes[] = $$stVariavel;
            $rsTipo->proximo();
        }

    } else {

        $obHdnId = new Hidden;
        $obHdnId->setName               ( "inId"                            );
        $obHdnId->setValue              ( $inId                             );

        $obHdnAno = new Hidden;
        $obHdnAno->setName              ( "inAno"                           );
        $obHdnAno->setValue             ( $inAno                            );

        $obHdnMes = new Hidden;
        $obHdnMes->setName              ( "inCodMes"                        );
        $obHdnMes->setValue             ( $inCodMes                         );

        $obHdnMes2 = new Hidden;
        $obHdnMes2->setName             ( "stMes"                           );
        $obHdnMes2->setValue            ( $stMes                            );

        $obHdnCalendario = new Hidden;
        $obHdnCalendario->setName       ( "hdnCodCalendario"                );
        $obHdnCalendario->setValue      ( $inCodCalendario                  );

        $obHdnTipo = new Hidden;
        $obHdnTipo->setName             ( "hdnTipo"                         );
        $obHdnTipo->setValue            ( $inCodTipo                        );

        $obLblAno = new Label;
        $obLblAno->setRotulo            ( "Ano"                             );
        $obLblAno->setValue             ( $inAno                            );
        $obLblAno->setId                ( "stLblAno"                        );

        $obLblMes = new Label;
        $obLblMes->setRotulo            ( "Mês"                             );
        $obLblMes->setId                ( "stLblMes"                        );
        $obLblMes->setValue             ( $stLblMes                         );

        $obLblTipo = new Label;
        $obLblTipo->setRotulo            ( "Tipo"                             );
        $obLblTipo->setId                ( "stLblTipo"                        );
        $obLblTipo->setValue             ( $stLblTipo                         );

    }

    $obSpanCal = new Span;
    $obSpanCal->setId                                 ( "spnCalendario"                              );

    $obDtVigencia = new Data;
    $obDtVigencia->setName          ( "dtVigencia"                      );
    $obDtVigencia->setValue         ( $dtVigencia                       );
    $obDtVigencia->setRotulo        ( "Vigência"                        );
    $obDtVigencia->setNull          ( false                             );
    $obDtVigencia->setTitle         ( "Informe a vigência da concessão.");

    $obBscValeTransporte = new BuscaInner;
    $obBscValeTransporte->setRotulo ( "Vale-Transporte"                 );
    $obBscValeTransporte->setTitle  ( "Informe a linha do vale-transporte utilizado." );
    $obBscValeTransporte->setNull   ( false                             );
    $obBscValeTransporte->setId     ( "stValeTransporte"                );
    $obBscValeTransporte->obCampoCod->setName ( "inCodValeTransporte"   );
    $obBscValeTransporte->obCampoCod->setValue( $inCodValeTransporte    );
    $obBscValeTransporte->obCampoCod->obEvento->setOnBlur("buscaValor('buscaValeTransporte');" );
    $obBscValeTransporte->setFuncaoBusca                 ( "abrePopUp('".CAM_GRH_BEN_POPUPS."valeTransporteServidor/LSProcurarValeTransporte.php','frm','inCodValeTransporte','stValeTransporte','','".Sessao::getId()."','800','550')" );

    $obFormulario = new Formulario;
    if ( Sessao::read('stAcao') == "alterar" ) {
        $obFormulario->addHidden        ( $obHdnId                                  );
        $obFormulario->addHidden        ( $obHdnAno                                 );
        $obFormulario->addHidden        ( $obHdnMes                                 );
        $obFormulario->addHidden        ( $obHdnMes2                                );
        $obFormulario->addHidden        ( $obHdnCalendario                          );
        $obFormulario->addHidden        ( $obHdnTipo                                );
        $obFormulario->addComponente    ( $obLblAno                                 );
        $obFormulario->addComponente    ( $obLblMes                                 );
        $obFormulario->addComponente    ( $obLblTipo                                );
    } else {
        $obFormulario->addComponente    ( $obTxtAno                                 );
        $obFormulario->addComponenteComposto( $obTxtCodMes          , $obCmbMes     );
        $obFormulario->agrupaComponentes( $arComponentes                            );
    }
    $obFormulario->addSpan              ( $obSpanCal                                );
    $obFormulario->addComponente        ( $obDtVigencia                             );
    $obFormulario->addComponente        ( $obBscValeTransporte                      );

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "window.parent.frames['telaPrincipal'].document.getElementById('spnSpan3').innerHTML ='".$obFormulario->getHTML()."';" ;

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function geraSpan4($boExecuta=false)
{
    global $inCodGrupo;
    $obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->listarGrupoConcessao( $rsGrupo );
    $obFormulario = new Formulario;
    $obFormulario->addTitulo                    ( "Informações por Grupo"                   );

    $obTxtCodGrupo = new TextBox;
    $obTxtCodGrupo->setRotulo                   ( "Grupos"                                  );
    $obTxtCodGrupo->setName                     ( "inCodGrupo"                              );
    $obTxtCodGrupo->setValue                    ( $inCodGrupo                               );
    $obTxtCodGrupo->setMaxLength                ( 10                                        );
    $obTxtCodGrupo->setSize                     ( 10                                        );
    $obTxtCodGrupo->setInteiro                  ( true                                      );

    $obCmbGrupo = new Select;
    $obCmbGrupo->setName                        ( "stGrupo"                                 );
    $obCmbGrupo->setStyle                       ( "width: 250px"                            );
    $obCmbGrupo->setRotulo                      ( "Grupos"                                  );
    $obCmbGrupo->setValue                       ( $inCodGrupo                               );
    $obCmbGrupo->setNull                        ( false                                     );
    $obCmbGrupo->addOption                      ( "", "Selecione"                           );
    $obCmbGrupo->setCampoID                     ( "[cod_grupo]"                             );
    $obCmbGrupo->setCampoDesc                   ( "[descricao]"                             );
    $obCmbGrupo->obEvento->setOnChange          ( "buscaValor('preencheSpan6');"            );
    $obCmbGrupo->preencheCombo                  ( $rsGrupo                                  );

    $obFormulario->addComponenteComposto        ( $obTxtCodGrupo          , $obCmbGrupo     );

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stHtml = $obFormulario->getHTML();
    if ($_REQUEST['boUtilizarGrupo']) {
        $stJs .= "d.getElementById('spnSpan4').innerHTML   = '".$stHtml."'; \n";
        $stJs .= "d.getElementById('spnSpan3').innerHTML   = '';            \n";
        $stJs .= "d.getElementById('spnSpan7').innerHTML   = '';            \n";
        $stJs .= "d.getElementById('spnSpan8').innerHTML   = '';            \n";
        $stJs .= "f.stRdoConcessao[0].disabled = true;                      \n";
        $stJs .= "f.stRdoConcessao[1].disabled = true;                      \n";
        $stJs .= "f.stRdoConcessao[2].disabled = true;                      \n";
    } else {
        Sessao::write('concessoes', array());
        $stJs .= "d.getElementById('spnSpan4').innerHTML   = '';            \n";
        $stJs .= "d.getElementById('spnSpan5').innerHTML   = '';            \n";
        $stJs .= "d.getElementById('spnSpan6').innerHTML   = '';            \n";
        $stJs .= "f.stRdoConcessao[0].disabled = false;                     \n";
        $stJs .= "f.stRdoConcessao[1].disabled = false;                     \n";
        $stJs .= "f.stRdoConcessao[2].disabled = false;                     \n";
        $stJs .= geraSpan3();
        $stJs .= geraSpan7();
        $stJs .= geraSpan8();
        $stJs .= preencheMes();
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function geraSpan5($boExecuta=false)
{
    if ( Sessao::read('inCodTipo') == 2 ) {

        $obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
        $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->addRBeneficioConcessaoValeTransporteSemanal();
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->listarDiasSemana( $rsDiasSemana );
        $obLista = new Lista;

        $obLista->setTitulo( "Quantidade Semanal" );
        $obLista->setRecordSet( $rsDiasSemana );
        $obLista->setMostraPaginacao( false );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Dia da Semana");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Obrigatório");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Quantidade");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "nom_dia" );
        $obLista->commitDado();

        $obChkObrigatorio = new CheckBox;
        $obChkObrigatorio->setName                ( "boObrigatorio"                     );
        $obChkObrigatorio->setValue               ( "true"                              );
        $obChkObrigatorio->setDisabled            ( true                                );
        $obChkObrigatorio->obEvento->setOnBlur    ( "buscaValor('calculaQuantidade');"  );
        $obLista->addDadoComponente( $obChkObrigatorio );
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->ultimoDado->setCampo('[boObrigatorio]');
        $obLista->commitDadoComponente();

        $obTxtQuantidade = new TextBox;
        $obTxtQuantidade->setName                 ( "inQuantidade"                      );
        $obTxtQuantidade->setValue                ( $inQuantidade                       );
        $obTxtQuantidade->setMaxlength            ( 2                                   );
        $obTxtQuantidade->setInteiro			  (	true								);
        $obTxtQuantidade->setDisabled             ( true                                );
        $obTxtQuantidade->obEvento->setOnBlur     ( "buscaValor('calculaQuantidade');"  );
        $obLista->addDadoComponente( $obTxtQuantidade );
        $obLista->ultimoDado->setCampo('[inQuantidade]');
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->commitDadoComponente();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "DETALHAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:buscaValor('exibeAviso');");
        $obLista->ultimaAcao->setLinkId( "popupDetalhar" );
        $obLista->ultimaAcao->setUnicoBotao( true );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "RESUMIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:buscaValor('exibeAviso');");
        $obLista->ultimaAcao->setLinkId( "cancelarDetalhar" );
        $obLista->ultimaAcao->setUnicoBotao( true );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    } else {
        $stHtml = "";
    }
    $stJs .= "d.getElementById('spnSpan5').innerHTML   = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

/* Monta a lista de concessões*/

function geraSpan6($boExecuta=false,$stId="spnSpan6",$boAcao=true)
{
    global $stAcao;
    $arConcessoes = ( is_array(Sessao::read('concessoes')) ) ? Sessao::read('concessoes') : array();
    $rsConcessoesCadastradas = new recordset;
    $rsConcessoesCadastradas->preenche( $arConcessoes );

    $obLista = new Lista;
    $obLista->setTitulo( "Concessões Cadastradas" );
    $obLista->setRecordSet( $rsConcessoesCadastradas );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Linha" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Exercício" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Mês" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Vigência" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Quantidade Total" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    if ($boAcao) {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();
    }

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "stValeTransporte" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "inAno" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "inCodMes" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "stTipo" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dtVigencia" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "inQuantidadeMensal" );
    $obLista->commitDado();

    if ($boAcao) {
        Sessao::write('boUtilizarGrupo', ( isset($_GET['boUtilizarGrupo']) ) ? $_GET['boUtilizarGrupo'] : Sessao::read('boUtilizarGrupo'));
        if ( Sessao::read('boUtilizarGrupo') ) {
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "CONSULTAR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:alteraDado('consultarConcessao');" );
            $obLista->ultimaAcao->addCampo("1","inId");
            $obLista->commitAcao();
        } else {
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "ALTERAR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:alteraDado('montaAlterarConcessao');" );
            $obLista->ultimaAcao->addCampo("1","inId");
            $obLista->commitAcao();

            if ($stAcao != 'alterar') {
                $obLista->addAcao();
                $obLista->ultimaAcao->setAcao( "EXCLUIR" );
                $obLista->ultimaAcao->setFuncao( true );
                $obLista->ultimaAcao->setLink( "JavaScript:alteraDado('excluirConcessao');" );
                $obLista->ultimaAcao->addCampo("1","inId");
                $obLista->commitAcao();
            }
        }
    }

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('".$stId."').innerHTML   = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function geraSpan7($boExecuta=false)
{
    //Define o objeto TEXT para a Quantidade Mensal
    $obTxtQuantidadeMensal = new TextBox;
    $obTxtQuantidadeMensal->setRotulo               ( "*Quantidade"                             );
    $obTxtQuantidadeMensal->setName                 ( "inQuantidadeMensal"                      );
    $obTxtQuantidadeMensal->setValue                ( $inQuantidadeMensal                       );
    $obTxtQuantidadeMensal->setTitle                ( "Quantidade total de vales-transporte."   );
    $obTxtQuantidadeMensal->setSize                 ( 10                                        );
    $obTxtQuantidadeMensal->setMaxLength            ( 3                                         );
    $obTxtQuantidadeMensal->setInteiro              ( true                                      );
    $obTxtQuantidadeMensal->setNull                 ( true                                      );
    $obTxtQuantidadeMensal->obEvento->setOnChange   ( "buscaValor('setHdnQuantidadeMensal');"   );

    $obHdnQuantidadeMensal = new Hidden;
    $obHdnQuantidadeMensal->setName                 ( "hdnQuantidadeMensal"                     );
    $obHdnQuantidadeMensal->setValue                ( $inQuantidadeMensal                       );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                        ( "Quantidade Mensal"                       );
    $obFormulario->addComponente                    ( $obTxtQuantidadeMensal                    );
    $obFormulario->addHidden                        ( $obHdnQuantidadeMensal                    );

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stHtml = $obFormulario->getHTML();

    $stJs .= "d.getElementById('spnSpan7').innerHTML   = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function geraSpan8($boExecuta=false)
{
    global $stAcao;
    //Define os objeto BUTTON para o processo de inclusão/alteração/Limpar
    if ($stAcao == 'incluir') {
       $obBtnIncluirConcessao = new Button;
       $obBtnIncluirConcessao->setName                 ( "btnIncluir"                      );
       $obBtnIncluirConcessao->setValue                ( "Incluir"                         );
       $obBtnIncluirConcessao->setTipo                 ( "button"                          );
       $obBtnIncluirConcessao->obEvento->setOnClick    ( "buscaValor('incluirConcessao');" );
    }

    $obBtnAlterarConcessao = new Button;
    $obBtnAlterarConcessao->setName                 ( "btnAlterar"                      );
    $obBtnAlterarConcessao->setValue                ( "Alterar"                         );
    $obBtnAlterarConcessao->setTipo                 ( "button"                          );
    $obBtnAlterarConcessao->obEvento->setOnClick    ( "buscaValor('alterarConcessao');" );

    $obBtnLimparConcessao = new Button;
    $obBtnLimparConcessao->setName                  ( "btnLimpar"                       );
    $obBtnLimparConcessao->setValue                 ( "Limpar"                          );
    $obBtnLimparConcessao->setTipo                  ( "button"                          );
    $obBtnLimparConcessao->obEvento->setOnClick     ( "buscaValor('limparConcessao');"  );

    $obFormulario = new Formulario;
    if ($stAcao == 'incluir') {
        $arBotoes =  array($obBtnIncluirConcessao,$obBtnAlterarConcessao,$obBtnLimparConcessao);
    } else {
        $arBotoes =  array($obBtnAlterarConcessao,$obBtnLimparConcessao);
    }
    $obFormulario->defineBarra( $arBotoes,"","" );

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stHtml = $obFormulario->getHTML();

    $stJs .= "d.getElementById('spnSpan8').innerHTML   = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}
/*
Gera o span com a busca por CGM
*/

function geraSpan9($boExecuta=false)
{
    global $stAcao;

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                    ( "Concessão por Matrícula"                  );

    $obIFiltroCGMContrato = new IFiltroCGMContrato;
    $obIFiltroCGMContrato->setInformacoesFuncao  ( false );
    $obIFiltroCGMContrato->obCmbContrato->setNull( false );
    $obIFiltroCGMContrato->geraFormulario       ( $obFormulario        );

    $obChkUtilizarGrupo = new CheckBox;
    $obChkUtilizarGrupo->setName                ( "boUtilizarGrupo"                         );
    $obChkUtilizarGrupo->setRotulo              ( "Utilizar Grupo"                          );
    $obChkUtilizarGrupo->setTitle               ( "Informe se a concessão será por grupo."  );
    $obChkUtilizarGrupo->setValue               ( "true"                                    );
    $obChkUtilizarGrupo->obEvento->setOnChange  ( "buscaValor('geraSpan4')"                 );

    $obFormulario->addComponente                ( $obChkUtilizarGrupo                       );

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $obFormulario->montaInnerHtml();
    if ($stAcao == 'incluir') {
        $stConcessao = ( $_POST['stRdoConcessao'] != "" ) ? $_POST['stRdoConcessao'] : "contrato";
        $stJs .= "f.stConcessao.value  = '$stConcessao';    \n";
    }
    $stJs .= "window.parent.frames['telaPrincipal'].document.getElementById('spnConcessao').innerHTML ='".$obFormulario->getHTML()."';\n" ;
    $stJs .= "f.stOpcaoEval.value  = '".$stEval."';     \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpans($boExecuta=false)
{
    global $stAcao,$stConcessao;
    if ($stAcao == 'incluir') {
        $stJs .= geraSpan1();
        $stJs .= geraSpan3();
        $stJs .= geraSpan6();
        $stJs .= geraSpan7();
        $stJs .= geraSpan8();
        $stJs .= preencheMes();
    } else {
        switch ($stConcessao) {
            case 'contrato':
                $stJs .= geraSpan1();
            break;
            case 'grupo':
                $stJs .= geraSpan2();
            break;
        }
        if ($_GET['boUtilizarGrupo']) {
            $stJs .= geraSpan6(false,"spnSpan3",false);
            $stJs .= "f.Ok.disabled = true;\n";
        } else {
            $stJs .= geraSpan6(false,"spnSpan3");
        }
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function habilitaCalendario($boExecuta=false)
{
    if ( Sessao::read('inCodTipo') == 2 ) {
        $obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
        $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->listar( $rsCalendario );

        $obTxtCodCalendario = new TextBox;
        $obTxtCodCalendario->setRotulo  ( "Calendário"                      );
        $obTxtCodCalendario->setName    ( "inCodCalendario"                 );
        $obTxtCodCalendario->setValue   ( $inCodCalendario                  );
        $obTxtCodCalendario->setTitle   ("Informe o calendário."            );
        $obTxtCodCalendario->setMaxLength( 10                               );
        $obTxtCodCalendario->setSize    ( 10                                );
        $obTxtCodCalendario->setNull    ( false                             );
        $obTxtCodCalendario->setInteiro ( true                              );
        $obTxtCodCalendario->obEvento->setOnChange  ( "buscaValor('habilitaQuantidadeSemanal');" );

        $obCmbCalendario = new Select;
        $obCmbCalendario->setName       ( "stCalendario"                    );
        $obCmbCalendario->setStyle      ( "width: 250px"                    );
        $obCmbCalendario->setRotulo     ( "Tipo"                            );
        $obCmbCalendario->setValue      ( $inCodCalendario                  );
        $obCmbCalendario->setNull       ( false                             );
        $obCmbCalendario->addOption     ( "", "Selecione"                   );
        $obCmbCalendario->setCampoID    ( "[cod_calendar]"                  );
        $obCmbCalendario->setCampoDesc  ( "[descricao]"                     );
        $obCmbCalendario->obEvento->setOnChange  ( "buscaValor('habilitaQuantidadeSemanal');" );
        $obCmbCalendario->preencheCombo ( $rsCalendario                     );

        $obFormulario = new Formulario;
        $obFormulario->addComponenteComposto( $obTxtCodCalendario   , $obCmbCalendario  );
        $obFormulario->montaInnerHtml();
        $stHTML = $obFormulario->getHTML();
    } else {
        $stHTML = "";
    }
    $stJs .= "d.getElementById('spnCalendario').innerHTML ='".$stHTML."';" ;
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function habilitaQuantidade($boExecuta=false)
{
    if ($_POST['inCodTipo'] == 2) {
        $stJs .= "f.inQuantidadeMensal.disabled = true; \n";
    } else {
        $stJs .= "f.inQuantidadeMensal.disabled = false; \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function habilitaQuantidadeSemanal($inCodMes,$inAno)
{
    $inCodCalendario = ( Sessao::read('inCodCalendario') ) ? Sessao::read('inCodCalendario') : $_POST['inCodCalendario'] ;
    //#inAno           = ( #sessao->transf['inAno']           ) ? #sessao->transf['inAno']           : $_POST['inAno']           ;
    //#inCodMes        = ( #sessao->transf['inCodMes']        ) ? #sessao->transf['inCodMes']        : $_POST['inCodMes']        ;
    $inCodTipo       = ( Sessao::read('inCodTipo')       ) ? Sessao::read('inCodTipo')       : $_POST['inCodTipo']       ;
    //#sessao->transf['inAno']            = $inAno;
    //#sessao->transf['inCodMes']         = $inCodMes;
    Sessao::write('inCodCalendario', $inCodCalendario);
    if ($inCodCalendario == '') {
        $stJs .= "  stLink = \"javascript:buscaValor('exibeAviso');\";                                                              \n";
        $stJs .= "  stLink2= \"javascript:buscaValor('exibeAviso');\";                                                              \n";
    } else {
        $stJs .= "  stLink = \"JavaScript:abrePopUp('".CAM_GRH_BEN_POPUPS."valeTransporteServidor/FMQuantidadeDiaria.php','frm','','','','".Sessao::getId()."&inCodMes=".$inCodMes."&inAno=".$inAno."','800','550');\";  \n";
        $stJs .= "  stLink2= \"JavaScript:buscaValor('cancelarDetalhar');\";                                                        \n";
    }
    if ($inCodCalendario != '' and $inAno != '' and $inCodMes != '' and $inCodTipo == 2) {
        $stJs .= "  for (inIndex=1;inIndex<=7;inIndex++) {                                                                            \n";
        $stJs .= "       eval('f.inQuantidade_'+inIndex+'.disabled = false;');                                                      \n";
        $stJs .= "  }                                                                                                               \n";
        $stJs .= "  d.links['popupDetalhar_1'].href = stLink;                                                                       \n";
        $stJs .= "  d.links['cancelarDetalhar_1'].href = stLink2;                                                                   \n";
    } elseif ( ($inCodCalendario == '' or $inAno == '' or $inCodMes == '') and $inCodTipo == 2 ) {
        $stJs .= "  for (inIndex=1;inIndex<=7;inIndex++) {                                                                            \n";
        $stJs .= "       eval('f.boObrigatorio_'+inIndex+'.disabled = true;');                                                      \n";
        $stJs .= "       eval('f.inQuantidade_'+inIndex+'.disabled = true;');                                                       \n";
        $stJs .= "  }                                                                                                               \n";
    }

    return $stJs;
}

function calculaQuantidade($boExecuta=false)
{
    $obErro = new erro;
    $obCalendario = new Calendario;

    if ( !$obErro->ocorreu() and $_POST['inCodMes'] != '' and $_POST['inAno'] != '' and $_POST['inCodCalendario'] != '' ) {
        $inCodMes  = ( strlen($_POST['inCodMes']) == 1 ) ? '0'.$_POST['inCodMes'] : $_POST['inCodMes'];
        $inDiasMes = $obCalendario->retornaUltimoDiaMes( $inCodMes,$_POST['inAno'] );
        $inDomingo = 0;
        $inSegunda = 0;
        $inTerca   = 0;
        $inQuarta  = 0;
        $inQuinta  = 0;
        $inSexta   = 0;
        $inSabado  = 0;

        $dtInicial = "01/".$inCodMes."/".$_POST['inAno'];
        $dtFinal   = $inDiasMes."/".$inCodMes."/".$_POST['inAno'];
        $obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
        $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->setCodCalendar( $_POST['inCodCalendario'] );
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->addFeriadoVariavel();
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->ultimoFeriadoVariavel->setDtInicial($dtInicial);
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->ultimoFeriadoVariavel->setDtFinal($dtFinal);
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->listarFeriados( $rsFeriados );

        for ($inDia=1;$inDia<=$inDiasMes;$inDia++) {
            $inDia = ( strlen($inDia) == 1 ) ? '0'.$inDia : $inDia;
            $inMes = ( strlen($_POST['inCodMes']) == 1 ) ? '0'.$_POST['inCodMes'] : $_POST['inCodMes'];
            $stData = $inDia."/".$inMes."/".$_POST['inAno'];
            $boCalcula = true;
            while ( !$rsFeriados->eof() ) {
                if ( $rsFeriados->getCampo('dt_feriado') == $stData ) {
                    $boCalcula = false;
                }
                $rsFeriados->proximo();
            }
            $rsFeriados->setPrimeiroElemento();
            $inDiaSemana = $obCalendario->retornaDiaSemana($inDia,$_POST['inCodMes'],$_POST['inAno']);
            $boObrigatorio = ( $_POST['boObrigatorio_'.($inDiaSemana+1)] == 'on' ) ? true : false;
            if ($boCalcula or $boObrigatorio) {
                switch ($inDiaSemana) {
                    case 0:
                        $inDomingo++;
                        $stJs .= "if (f.inQuantidade_1.value == '') {   \n";
                        $stJs .= "  f.boObrigatorio_1.disabled = true;  \n";
                        $stJs .= "  f.boObrigatorio_1.checked  = false; \n";
                        $stJs .= "} else {                                \n";
                        $stJs .= "  f.boObrigatorio_1.disabled = false; \n";
                        $stJs .= "}                                     \n";
                    break;
                    case 1:
                        $inSegunda++;
                        $stJs .= "if (f.inQuantidade_2.value == '') {   \n";
                        $stJs .= "  f.boObrigatorio_2.disabled = true;  \n";
                        $stJs .= "  f.boObrigatorio_2.checked  = false; \n";
                        $stJs .= "} else {                                \n";
                        $stJs .= "  f.boObrigatorio_2.disabled = false; \n";
                        $stJs .= "}                                     \n";
                    break;
                    case 2:
                        $inTerca++;
                        $stJs .= "if (f.inQuantidade_3.value == '') {   \n";
                        $stJs .= "  f.boObrigatorio_3.disabled = true;  \n";
                        $stJs .= "  f.boObrigatorio_3.checked  = false; \n";
                        $stJs .= "} else {                                \n";
                        $stJs .= "  f.boObrigatorio_3.disabled = false; \n";
                        $stJs .= "}                                     \n";
                    break;
                    case 3:
                        $inQuarta++;
                        $stJs .= "if (f.inQuantidade_4.value == '') {   \n";
                        $stJs .= "  f.boObrigatorio_4.disabled = true;  \n";
                        $stJs .= "  f.boObrigatorio_4.checked  = false; \n";
                        $stJs .= "} else {                                \n";
                        $stJs .= "  f.boObrigatorio_4.disabled = false; \n";
                        $stJs .= "}                                     \n";
                    break;
                    case 4:
                        $inQuinta++;
                        $stJs .= "if (f.inQuantidade_5.value == '') {   \n";
                        $stJs .= "  f.boObrigatorio_5.disabled = true;  \n";
                        $stJs .= "  f.boObrigatorio_5.checked  = false; \n";
                        $stJs .= "} else {                                \n";
                        $stJs .= "  f.boObrigatorio_5.disabled = false; \n";
                        $stJs .= "}                                     \n";
                    break;
                    case 5:
                        $inSexta++;
                        $stJs .= "if (f.inQuantidade_6.value == '') {   \n";
                        $stJs .= "  f.boObrigatorio_6.disabled = true;  \n";
                        $stJs .= "  f.boObrigatorio_6.checked  = false; \n";
                        $stJs .= "} else {                                \n";
                        $stJs .= "  f.boObrigatorio_6.disabled = false; \n";
                        $stJs .= "}                                     \n";
                    break;
                    case 6:
                        $inSabado++;
                        $stJs .= "if (f.inQuantidade_7.value == '') {   \n";
                        $stJs .= "  f.boObrigatorio_7.disabled = true;  \n";
                        $stJs .= "  f.boObrigatorio_7.checked  = false; \n";
                        $stJs .= "} else {                                \n";
                        $stJs .= "  f.boObrigatorio_7.disabled = false; \n";
                        $stJs .= "}                                     \n";
                    break;
                }
            }
        }
        //$stJs .= "f.inCodMes.disabled = true;        \n";
        //$stJs .= "f.stMes.disabled    = true;        \n";
        //$stJs .= "f.inAno.disabled    = true;        \n";
        //$stJs .= "f.inCodCalendario.disabled = true; \n";
        //$stJs .= "f.stCalendario.disabled = true;    \n";
        preencheValoresSemanalSessao();
        $inQuantidadeTotal = $inDomingo * Sessao::read('inDomingo');
        $inQuantidadeTotal = $inQuantidadeTotal + ($inSegunda * Sessao::read('inSegunda'));
        $inQuantidadeTotal = $inQuantidadeTotal + ($inTerca   * Sessao::read('inTerca')  );
        $inQuantidadeTotal = $inQuantidadeTotal + ($inQuarta  * Sessao::read('inQuarta') );
        $inQuantidadeTotal = $inQuantidadeTotal + ($inQuinta  * Sessao::read('inQuinta') );
        $inQuantidadeTotal = $inQuantidadeTotal + ($inSexta   * Sessao::read('inSexta')  );
        $inQuantidadeTotal = $inQuantidadeTotal + ($inSabado  * Sessao::read('inSabado') );
        $stJs .= "f.inQuantidadeMensal.value = ".$inQuantidadeTotal.";    \n";
        $stJs .= "f.hdnQuantidadeMensal.value= ".$inQuantidadeTotal.";    \n";
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencheValoresSemanalSessao()
{
    Sessao::write('inDomingo', ( isset($_POST['inQuantidade_1'])   ) ? $_POST['inQuantidade_1'] : Sessao::read('inDomingo'));
    Sessao::write('inSegunda', ( isset($_POST['inQuantidade_2'])   ) ? $_POST['inQuantidade_2'] : Sessao::read('inSegunda'));
    Sessao::write('inTerca'  , ( isset($_POST['inQuantidade_3'])   ) ? $_POST['inQuantidade_3'] : Sessao::read('inTerca'));
    Sessao::write('inQuarta' , ( isset($_POST['inQuantidade_4'])   ) ? $_POST['inQuantidade_4'] : Sessao::read('inQuarta'));
    Sessao::write('inQuinta' , ( isset($_POST['inQuantidade_5'])   ) ? $_POST['inQuantidade_5'] : Sessao::read('inQuinta'));
    Sessao::write('inSexta'  , ( isset($_POST['inQuantidade_6'])   ) ? $_POST['inQuantidade_6'] : Sessao::read('inSexta'));
    Sessao::write('inSabado' , ( isset($_POST['inQuantidade_7'])   ) ? $_POST['inQuantidade_7'] : Sessao::read('inSabado'));
    Sessao::write('boDomingo', $_POST['boObrigatorio_1'] );
    Sessao::write('boSegunda', $_POST['boObrigatorio_2'] );
    Sessao::write('boTerca'  , $_POST['boObrigatorio_3'] );
    Sessao::write('boQuarta' , $_POST['boObrigatorio_4'] );
    Sessao::write('boQuinta' , $_POST['boObrigatorio_5'] );
    Sessao::write('boSexta'  , $_POST['boObrigatorio_6'] );
    Sessao::write('boSabado' , $_POST['boObrigatorio_7'] );
}

function validaComum()
{
    $obErro = new erro;
    if ($_POST['inAno'] == "") {
        $obErro->setDescricao("Campo Ano inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['inCodMes'] == "" ) {
        $obErro->setDescricao("Campo Mês inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['inCodTipo'] == "" and $_POST['hdnTipo'] == "" ) {
        $obErro->setDescricao("Campo Tipo inválido!()");
    }
    if ( !$obErro->ocorreu() and isset($_POST['inCodCalendario']) and  $_POST['inCodCalendario'] == "" ) {
        $obErro->setDescricao("Campo Calendário inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['dtVigencia'] == "" ) {
        $obErro->setDescricao("Campo Vigência inválido!()");
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php");
        $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao();
        $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao,$boTransacao);
    }
    if ( !$obErro->ocorreu() and sistemaLegado::comparaDatas($rsUltimaMovimentacao->getCampo('dt_final'),$_POST['dtVigencia']) ) {
        $obErro->setDescricao("Campo Vigência deve ser superior ou igual a data final da competência!()");
    }
    if ( !$obErro->ocorreu() and Sessao::read('dtVigencia') != "" and sistemaLegado::comparaDatas(Sessao::read('dtVigencia'),$_POST['dtVigencia']) ) {
        $obErro->setDescricao("Campo Vigência deve ser superior ou igual a última vigência!()");
    }
    if ( !$obErro->ocorreu() and $_POST['inCodValeTransporte'] == "" ) {
        $obErro->setDescricao("Campo Vale-Transporte inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['inQuantidadeMensal'] == "" and $_POST['hdnQuantidadeMensal'] == "" ) {
        $obErro->setDescricao("Campo Quantidade inválido!()");
    }

    return $obErro;
}

function incluirConcessao($boExecuta=false)
{
    $obErro = new erro;
    if ( Sessao::read('stAcaoTemp') == "alterarConcessao" ) {
        $obErro->setDescricao("Processo de alteração de concessão em progresso, conclua a alteração ou limpe o formulário!()");
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = validaComum();
    }
    if ( !$obErro->ocorreu() ) {
        $arConcessoes = ( is_array(Sessao::read('concessoes')) ) ? Sessao::read('concessoes') : array();

        $rsRecordSet = new Recordset;
        $rsRecordSet->preenche( $arConcessoes );
        $inCodMes = ( strlen($_POST['inCodMes']) == 1 ) ? '0'.$_POST['inCodMes'] : $_POST['inCodMes'] ;
        while ( !$rsRecordSet->eof() ) {
            if( $rsRecordSet->getCampo('inCodValeTransporte') == $_POST['inCodValeTransporte']
            and $rsRecordSet->getCampo('inAno')               == $_POST['inAno']
            and $rsRecordSet->getCampo('inCodMes')            == $inCodMes){
                $obErro->setDescricao("Linha ".$_POST['stValeTransporte']." já cadastrada para este ano e mês.");
                break;
            }
            $rsRecordSet->proximo();
        }
    }
    if ( !$obErro->ocorreu() ) {
        $rsRecordSet->setUltimoElemento();
        $inUltimoId = $rsRecordSet->getCampo("inId");
        if (!$inUltimoId) {
            $inProxId = 1;
        } else {
            $inProxId = $inUltimoId + 1;
        }
        $arElementos = array();
        $arElementos['inId']                = $inProxId;
        $arElementos['stValeTransporte']    = $_POST['stValeTransporte'];
        $arElementos['inCodValeTransporte'] = $_POST['inCodValeTransporte'];
        $arElementos['inAno']               = $_POST['inAno'];
        $arElementos['inCodMes']            = ( strlen($_POST['inCodMes']) == 1 ) ? '0'.$_POST['inCodMes'] : $_POST['inCodMes'] ;
        $arElementos['stTipo']              = ( $_POST['inCodTipo'] == 1 ) ? 'Mensal' : 'Diários';
        $arElementos['inCodTipo']           = $_POST['inCodTipo'];
        $arElementos['dtVigencia']          = $_POST['dtVigencia'];
        if ($_POST['inCodTipo'] == 1) {
            $arElementos['inQuantidadeMensal']  = $_POST['inQuantidadeMensal'];
        } else {
            $arElementos['inQuantidadeMensal']  = $_POST['hdnQuantidadeMensal'];
            $arElementos['inCodCalendario']     = $_POST['inCodCalendario'];
            $arElementos['arQuantidadeSemanal'] = incluirQuantidadeSemanal();
            $arElementos['arQuantidadeMensal']  = incluirQuantidadeMensal();

        }

        $arConcessoes[]                     = $arElementos;
        Sessao::write('concessoes', $arConcessoes);

        $stJs .= geraSpan6();
        $stJs .= limparConcessao();
        $stJs .= "f.stRdoConcessao[0].disabled = true;      \n";
        $stJs .= "f.stRdoConcessao[1].disabled = true;      \n";
        $stJs .= "f.stRdoConcessao[2].disabled = true;      \n";
        $stJs .= "f.boUtilizarGrupo.disabled = true;        \n";
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function incluirQuantidadeSemanal()
{
    $arQuantidadeSemanal = array();
    $arQuantidadeSemanal[0]['boObrigatorio'] = Sessao::read('boDomingo');
    $arQuantidadeSemanal[0]['inQuantidade']  = Sessao::read('inDomingo');
    $arQuantidadeSemanal[1]['boObrigatorio'] = Sessao::read('boSegunda');
    $arQuantidadeSemanal[1]['inQuantidade']  = Sessao::read('inSegunda');
    $arQuantidadeSemanal[2]['boObrigatorio'] = Sessao::read('boTerca');
    $arQuantidadeSemanal[2]['inQuantidade']  = Sessao::read('inTerca');
    $arQuantidadeSemanal[3]['boObrigatorio'] = Sessao::read('boQuarta');
    $arQuantidadeSemanal[3]['inQuantidade']  = Sessao::read('inQuarta');
    $arQuantidadeSemanal[4]['boObrigatorio'] = Sessao::read('boQuinta');
    $arQuantidadeSemanal[4]['inQuantidade']  = Sessao::read('inQuinta');
    $arQuantidadeSemanal[5]['boObrigatorio'] = Sessao::read('boSexta');
    $arQuantidadeSemanal[5]['inQuantidade']  = Sessao::read('inSexta');
    $arQuantidadeSemanal[6]['boObrigatorio'] = Sessao::read('boSabado');
    $arQuantidadeSemanal[6]['inQuantidade']  = Sessao::read('inSabado');

    return $arQuantidadeSemanal;
}

function incluirQuantidadeMensal()
{
    return Sessao::read('valeDias');
}

function montaAlterarConcessao()
{
    $stJs .= geraSpan3();
    $stJs .= geraSpan6();
    $stJs .= geraSpan7();
    $stJs .= geraSpan8();

    if ($_REQUEST['stAcao'] != 'alterar') {
        $stJs .= preencheMes();
    }

    $stJs .= _montaAlterarConcessao();

    return $stJs;
}

function _montaAlterarConcessao($boExecuta=false)
{
    global $stAcao;
    $rsRecordSet = new Recordset;

    $rsRecordSet->preenche( Sessao::read('concessoes') );
    while ( !$rsRecordSet->eof() ) {
        if ( $rsRecordSet->getCampo('inId') == $_GET['inId'] ) {
            Sessao::write('inId', $_GET['inId']);
            $arMeses = array(1=>"Janeiro",2=>"Fevereiro",3=>"Março",
                             4=>"Abril",5=>"Maio",6=>"Junho",
                             7=>"Julho",8=>"Agosto",9=>"Setembro",
                             10=>"Outubro",11=>"Novembro",12=>"Dezembro");
            //switch ($_POST['stConcessao']) {
            //    case 'contrato':
                    Sessao::write('inCodConcessao' , $rsRecordSet->getCampo('inCodConcessao'));
                    Sessao::write('inCodTipo'      , $rsRecordSet->getCampo('inCodTipo'));
                    Sessao::write('inAno'          , $rsRecordSet->getCampo('inAno'));
                    Sessao::write('inCodMes'       , $rsRecordSet->getCampo('inCodMes'));
                    Sessao::write('inCodCalendario', $rsRecordSet->getCampo('inCodCalendario'));
                    $stJs .= habilitaCalendario();
                    $stJs .= geraSpan5();
                    $stJs .= habilitaQuantidadeSemanal($rsRecordSet->getCampo('inCodMes'),$rsRecordSet->getCampo('inAno'));
                    $stJs .= "f.inAno.value                 = '".$rsRecordSet->getCampo('inAno')."';                \n";
                    if ($stAcao == "alterar") {
                        //$stJs .= "var d = window.parent.frames['telaPrincipal'].document; \n";
                        $stJs .= "d.getElementById('stLblAno').innerHTML = '".$rsRecordSet->getCampo('inAno')."';       \n";
                        $stJs .= "d.getElementById('stLblMes').innerHTML = '".$arMeses[(int) $rsRecordSet->getCampo('inCodMes')]."';    \n";
                    }
                    $stJs .= "f.inCodMes.value              = '".$rsRecordSet->getCampo('inCodMes')."';        \n";
                    $stJs .= "f.stMes.value                 = '".$rsRecordSet->getCampo('inCodMes')."';        \n";
                    $stJs .= "f.dtVigencia.value            = '".$rsRecordSet->getCampo('dtVigencia')."';           \n";
                    Sessao::write('dtVigencia'     , $rsRecordSet->getCampo('dtVigencia'));
                    $stJs .= "f.inCodValeTransporte.value   = '".$rsRecordSet->getCampo('inCodValeTransporte')."';  \n";
                    $stJs .= "f.stValeTransporte.value      = '".$rsRecordSet->getCampo('stValeTransporte')."';     \n";
                    $stJs .= "d.getElementById('stValeTransporte').innerHTML = '".$rsRecordSet->getCampo('stValeTransporte')."'; \n";
                    $stJs .= "f.inQuantidadeMensal.value    = '".$rsRecordSet->getCampo('inQuantidadeMensal')."';   \n";
                    $stJs .= "f.hdnQuantidadeMensal.value   = '".$rsRecordSet->getCampo('inQuantidadeMensal')."';   \n";
                    if ( $rsRecordSet->getCampo('inCodTipo') == 2 ) {
                        $stJs .= "f.inQuantidadeMensal.disabled = true;                                             \n";

                        if ($stAcao == "incluir") {
                            $stJs .= "f.inCodTipo[1].checked = true;                                                    \n";
                        }

                        if ($stAcao == "alterar") {
                            $stJs .= "d.getElementById('stLblTipo').innerHTML = 'Diário';                               \n";
                            $stJs .= "f.hdnTipo.value = 2;                                                              \n";
                        }
                        $stJs .= "f.inCodCalendario.value = '".$rsRecordSet->getCampo('inCodCalendario')."';        \n";
                        $stJs .= "f.stCalendario.value    = '".$rsRecordSet->getCampo('inCodCalendario')."';        \n";
                        $arDiasSemana = array('Domingo','Segunda','Terca','Quarta','Quinta','Sexta','Sabado');
                        $arQuantidadeSemanal = $rsRecordSet->getCampo('arQuantidadeSemanal');
                        Sessao::write('valeDias', $rsRecordSet->getCampo('arQuantidadeMensal'));
                        for ($inIndex=1;$inIndex<=7;$inIndex++) {
                            $stJs .= "f.boObrigatorio_".$inIndex.".checked = '".$arQuantidadeSemanal[$inIndex-1]['boObrigatorio']."'\n";
                            $stJs .= "f.inQuantidade_".$inIndex.".value    = '".$arQuantidadeSemanal[$inIndex-1]['inQuantidade']."' \n";
                            Sessao::write('in'.$arDiasSemana[$inIndex-1], $arQuantidadeSemanal[$inIndex-1]['inQuantidade']);
                            Sessao::write('bo'.$arDiasSemana[$inIndex-1], $arQuantidadeSemanal[$inIndex-1]['boObrigatorio']);
                            if ( count(Sessao::read('valeDias')) > 0 ) {
                                $stJs .= "f.boObrigatorio_".$inIndex.".disabled = true;                                             \n";
                                $stJs .= "f.inQuantidade_".$inIndex.".disabled = true;                                              \n";
                            }
                        }

                    } else {

                        if ($stAcao == "incluir") {
                            $stJs .= "f.inCodTipo[0].checked = true;\n";
                        }

                        if ($stAcao == "alterar") {
                            $stJs .= "d.getElementById('stLblTipo').innerHTML = 'Mensal';\n";
                            $stJs .= "f.hdnTipo.value = 1;                                                              \n";
                        }

                    }
            //    break;
            //}
            Sessao::write('stAcaoTemp', 'alterarConcessao');
        }
        $rsRecordSet->proximo();
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function alterarConcessao($boExecuta=false)
{
    $obErro = new erro;
    if ( Sessao::read('stAcaoTemp') != "alterarConcessao" ) {
        $obErro->setDescricao("Processo de inclusão de concessão em progresso, conclua a inclusão.");
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = validaComum();
    }
    if ( !$obErro->ocorreu() ) {
        $inCodTipo = ( $_POST['inCodTipo'] != "" ) ? $_POST['inCodTipo'] : $_POST['hdnTipo'];
        $arElementos = array();
        $arElementos['inId']                = Sessao::read('inId');
        $arElementos['stValeTransporte']    = $_POST['stValeTransporte'];
        $arElementos['inCodValeTransporte'] = $_POST['inCodValeTransporte'];
        $arElementos['inAno']               = $_POST['inAno'];
        $arElementos['inCodMes']            = ( strlen($_POST['inCodMes']) == 1 ) ? '0'.$_POST['inCodMes'] : $_POST['inCodMes'] ;
        $arElementos['stTipo']              = ( $inCodTipo == 1 ) ? 'Mensal' : 'Diários';
        $arElementos['inCodTipo']           = $inCodTipo;
        $arElementos['inCodCalendario']     = $_POST['inCodCalendario'];
        $arElementos['dtVigencia']          = $_POST['dtVigencia'];

        if ($inCodTipo == 1) {
            $arElementos['inQuantidadeMensal']  = ( !empty($_POST['inQuantidadeMensal']) ) ? $_POST['inQuantidadeMensal'] : $_POST['hdnQuantidadeMensal'];
        } else {
            $arElementos['inQuantidadeMensal']  = ( !empty($_POST['inQuantidadeMensal']) ) ? $_POST['inQuantidadeMensal'] : $_POST['hdnQuantidadeMensal'];
            $arElementos['arQuantidadeSemanal'] = incluirQuantidadeSemanal();
            $arElementos['arQuantidadeMensal']  = incluirQuantidadeMensal();
        }
        $arElementos['inCodConcessao']      = Sessao::read('inCodConcessao');
        $arTemp      = array();
        foreach ( Sessao::read('concessoes') as $arConcessao ) {
            if ($arConcessao['inId'] != $arElementos['inId']) {
                $arTemp[] = $arConcessao;
            } else {
                $arTemp[] = $arElementos;
            }
        }

        Sessao::write('concessoes', $arTemp);
        $stJs .= geraSpan6();
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    if ( !$obErro->ocorreu() ) {
        $stJs .= limparConcessao();
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function excluirConcessao($boExecuta=false)
{
    $obErro = new erro;
    if ( !$obErro->ocorreu() ) {
        $arTemp      = array();
        foreach ( Sessao::read('concessoes') as $arConcessao ) {
            if ($arConcessao['inId'] != $_GET['inId']) {
                $arTemp[] = $arConcessao;
            }
        }
        Sessao::write('concessoes', $arTemp);
        if ( count(Sessao::read('concessoes')) == 0 ) {
            $stJs .= "f.stRdoConcessao[0].disabled = false;                         \n";
            $stJs .= "f.stRdoConcessao[1].disabled = false;                         \n";
            $stJs .= "f.stRdoConcessao[2].disabled = false;                         \n";
        }
        $stJs .= geraSpan6();
    } else {
         $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limparConcessao($boExecuta=false)
{
    $stJs  = "var f = window.parent.frames[\"telaPrincipal\"].document.frm;\n";
    $stJs .= "var d = window.parent.frames[\"telaPrincipal\"].document;\n";
    if ($_POST['stAcao'] == "incluir") {
        $stJs .= "f.inAno.value = '".date('Y')."';                  \n";
        //$stJs .= "f.inCodMes.value = '';                          \n";
        $stJs .= "f.stMes.value    = '';                            \n";
        //$stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;' \n";
        //$stJs .= "f.inContrato.value = ''							\n";
    }

    $stJs .= "f.dtVigencia.value = '".date('d/m/Y')."';             \n";
    $stJs .= "f.inCodValeTransporte.value = '';                     \n";
    $stJs .= "f.stValeTransporte.value = '';                        \n";
    $stJs .= "d.getElementById('stValeTransporte').innerHTML = '&nbsp;';\n";
    $stJs .= "f.inQuantidadeMensal.value = '';                      \n";

    if ($_POST['inCodTipo'] == 2) {
        $stJs .= "f.inCodCalendario.value = '';                     \n";
        $stJs .= "f.stCalendario.value    = '';                     \n";
        $arSemana = array(1=>'Domingo',
                          2=>'Segunda',
                          3=>'Terca',
                          4=>'Quarta',
                          5=>'Quinta',
                          6=>'Sexta',
                          7=>'Sabado');
        for ($inIndex=1;$inIndex<=7;$inIndex++) {
            $stJs .= "f.boObrigatorio_$inIndex.checked = false;     \n";
            $stJs .= "f.boObrigatorio_$inIndex.disabled= true;      \n";
            $stJs .= "f.inQuantidade_$inIndex.value = '';           \n";
            Sessao::write('in'.$arSemana[$inIndex], '');
            Sessao::write('bo'.$arSemana[$inIndex], '');
        }
    }
    Sessao::write('stAcaoTemp', "");
    Sessao::write('dtVigencia', "");
    if ($_POST['stAcao'] == "alterar") {
        $stJs .= "d.getElementById('spnSpan3').innerHTML = '';  \n";
        $stJs .= "d.getElementById('spnSpan4').innerHTML = '';  \n";
        $stJs .= "d.getElementById('spnSpan5').innerHTML = '';  \n";
        $stJs .= "d.getElementById('spnSpan7').innerHTML = '';  \n";
        $stJs .= "d.getElementById('spnSpan8').innerHTML = '';  \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limparForm($boExecuta=false)
{
    $stJs  = "var f = window.parent.frames[\"telaPrincipal\"].document.frm;\n";
    $stJs .= "var d = window.parent.frames[\"telaPrincipal\"].document;\n";
    switch ($_POST['stConcessao']) {
        case "contrato":
            //$stJs .= "f.inRegistro.value = '';                                     \n";
            //$stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;' 		   \n";
               //$stJs .= "f.inContrato.value = ''							           \n";
            $stJs .= "f.boUtilizarGrupo.checked = false;                           \n";
        break;
        case 'cgm_contrato':
            $stJs .= "f.inNumCGM.value = '';                               		   \n";
            $stJs .= "d.getElementById('inCampoInner').innerHTML = '&nbsp;';       \n";
            $stJs .= "f.inRegistro.options[0].selected = true;                     \n";
            $stJs .= "f.boUtilizarGrupo.checked = false;                           \n";
            $stJs .= "f.boUtilizarGrupo.disabled = false;                          \n";
        break;
        case "grupo":
            $stJs .= "f.stDescricaoGrupo.value = '';                               \n";
        break;
    }
    $stJs .= limparConcessao();
    $stJs .= "d.getElementById('spnSpan6').innerHTML = '';                  \n";
    $stJs .= "f.stRdoConcessao[0].disabled = false;                         \n";
    $stJs .= "f.stRdoConcessao[1].disabled = false;                         \n";
    $stJs .= "f.stRdoConcessao[2].disabled = false;                         \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencheCGMdoContrato($boExecuta=false)
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRCGMPessoaFisica = new RCGMPessoaFisica;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setRegistro($_REQUEST['inRegistro']);
    $obRPessoalServidor->roUltimoContratoServidor->listarContratosServidorResumido($rsContratoServidor);
    if ( $rsContratoServidor->getNumLinhas() > 0 ) {
        $obRPessoalServidor->setCodServidor( $rsContratoServidor->getCampo('cod_servidor') );
        $obRPessoalServidor->consultarServidor($rsServidor,$boTransacao);
        $obRCGMPessoaFisica->setNumCGM($rsServidor->getCampo('numcgm'));
        $obRCGMPessoaFisica->consultarCGM($rsCGM);
        $stNomCGM = $rsCGM->getCampo('numcgm') ." - ". $rsCGM->getCampo('nom_cgm');
    }
    if ($rsContratoServidor->getNumLinhas() > 0 and $_REQUEST['inRegistro'] ) {
        $stJs .= "d.getElementById('inNomCGM').innerHTML = '".$stNomCGM."';       \n";
    } else {
        $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;';                                \n";
        $stJs .= "alertaAviso('@Matrícula informada não existe.','form','erro','".Sessao::getId()."');   \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function buscaValeTransporte($boExecuta=false)
{
    $stText = "inCodValeTransporte";
    $stSpan = "stValeTransporte";
    $obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
    $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
    if ($_REQUEST[ $stText ] != "") {
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRBeneficioValeTransporte->setCodValeTransporte( $_REQUEST[ $stText ] );
        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRBeneficioValeTransporte->listarValeTransporte( $rsRecordSet );
        $stNull = "&nbsp;";
        if ( $rsRecordSet->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
            $stDescValeTransporte = $rsRecordSet->getCampo("nom_municipio_o")."/".$rsRecordSet->getCampo("origem")." - ".$rsRecordSet->getCampo('nom_municipio_d')."/".$rsRecordSet->getCampo("destino");
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML             = "'.$stDescValeTransporte.'";';
            $stJs .= "f.".$stSpan.".value = '".$stDescValeTransporte."'\n;";
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function geraSpan1Filtro($boExecuta=false)
{
    $obTxtContrato= new TextBox;
    $obTxtContrato->setRotulo               ( "Matrícula"                                                );
    $obTxtContrato->setTitle                ( "Informe o contrato."                                     );
    $obTxtContrato->setName                 ( "inRegistro"                                              );
    $obTxtContrato->setValue                ( $inRegistro                                               );
    $obTxtContrato->setMaxLength            ( 10                                                        );
    $obTxtContrato->setSize                 ( 10                                                        );
    $obTxtContrato->setInteiro              ( true                                                      );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                    ( "Filtrar por Matrícula"                    );
    $obFormulario->addComponente                ( $obTxtContrato                            );

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $Html = $obFormulario->getHtml();

    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$Html."';    \n";
    $stJs .= "f.stOpcaoEval.value  = '".$stEval."';                     \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function geraSpan2Filtro($boExecuta=false)
{
    $obTxtCodGrupo= new TextBox;
    $obTxtCodGrupo->setRotulo               ( "Código do grupo"                                         );
    $obTxtCodGrupo->setTitle                ( "Informe o código do grupo."                              );
    $obTxtCodGrupo->setName                 ( "inCodGrupo"                                              );
    $obTxtCodGrupo->setValue                ( $inCodGrupo                                               );
    $obTxtCodGrupo->setMaxLength            ( 10                                                        );
    $obTxtCodGrupo->setSize                 ( 10                                                        );
    $obTxtCodGrupo->setInteiro              ( true                                                      );

    $obTxtDescricaoGrupo= new TextBox;
    $obTxtDescricaoGrupo->setRotulo         ( "Descrição do grupo"                                      );
    $obTxtDescricaoGrupo->setTitle          ( "Informe a descrição do grupo."                           );
    $obTxtDescricaoGrupo->setName           ( "stDescricaoGrupo"                                        );
    $obTxtDescricaoGrupo->setValue          ( $stDescricaoGrupo                                         );
    $obTxtDescricaoGrupo->setMaxLength      ( 80                                                        );
    $obTxtDescricaoGrupo->setSize           ( 40                                                        );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                ( "Filtrar por Grupo"                                       );
    $obFormulario->addComponente            ( $obTxtCodGrupo                                            );
    $obFormulario->addComponente            ( $obTxtDescricaoGrupo                                      );

    $obFormulario->montaInnerHtml();
    $Html = $obFormulario->getHtml();

    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$Html."';    \n";
    $stJs .= "f.stOpcaoEval.value  = '';                                \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function geraSpan3Filtro($boExecuta=false)
{
    $obBscValeTransporte = new BuscaInner;
    $obBscValeTransporte->setRotulo                         ( "Vale-Transporte"                             );
    $obBscValeTransporte->setTitle                          ( "Informe o vale-transporte."                  );
    $obBscValeTransporte->setId                             ( "stValeTransporte"                            );
    $obBscValeTransporte->obCampoCod->setName               ( "inCodValeTransporte"                         );
    $obBscValeTransporte->obCampoCod->setValue              ( $inCodValeTransporte                          );
    $obBscValeTransporte->obCampoCod->obEvento->setOnBlur   ("buscaValorFiltro('buscaValeTransporte');"     );
    $obBscValeTransporte->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_BEN_POPUPS."valeTransporteServidor/LSProcurarValeTransporte.php','frm','inCodValeTransporte','stValeTransporte','','".Sessao::getId()."','800','550')" );

    $obRdoConcessaoContrato = new Radio;
    $obRdoConcessaoContrato->setName                ( "stConcessaoVT"                                       );
    $obRdoConcessaoContrato->setId                  ( "stConcessaoVT"                                       );
    $obRdoConcessaoContrato->setTitle               ( "Selecione o filtro para concessão."                  );
    $obRdoConcessaoContrato->setRotulo              ( "Concessão"                                           );
    $obRdoConcessaoContrato->setLabel               ( "Matrícula"                                            );
    $obRdoConcessaoContrato->setValue               ( "contrato"                                            );
    $obRdoConcessaoContrato->setNull                ( false                                                 );
    $obRdoConcessaoContrato->setChecked             ( true                                                  );

    $obRdoConcessaoGrupo = new Radio;
    $obRdoConcessaoGrupo->setName                   ( "stConcessaoVT"                                       );
    $obRdoConcessaoGrupo->setId                     ( "stConcessaoVT"                                       );
    $obRdoConcessaoGrupo->setTitle                  ( "Selecione o filtro para concessão."                  );
    $obRdoConcessaoGrupo->setRotulo                 ( "Concessão"                                           );
    $obRdoConcessaoGrupo->setLabel                  ( "Grupo"                                               );
    $obRdoConcessaoGrupo->setValue                  ( "grupo"                                               );
    $obRdoConcessaoGrupo->setNull                   ( false                                                 );
    $obRdoConcessaoGrupo->setChecked                ( false                                                 );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                ( "Filtrar por Vale-Transporte"                             );
    $obFormulario->addComponente            ( $obBscValeTransporte                                      );
    $obFormulario->agrupaComponentes        ( array($obRdoConcessaoContrato,$obRdoConcessaoGrupo)       );

    $obFormulario->montaInnerHtml();
    $Html = $obFormulario->getHtml();

    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$Html."';    \n";
    $stJs .= "f.stOpcaoEval.value  = '';                                \n";

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function geraSpan4Filtro($boExecuta=false)
{
    $obBscCGM = new BuscaInner;
    $obBscCGM->setRotulo                        ( "CGM"                                     );
    $obBscCGM->setTitle                         ( "Informe o CGM para a consulta."          );
    $obBscCGM->setId                            ( "inCampoInner"                            );
    $obBscCGM->obCampoCod->setName              ( "inNumCGM"                                );
    $obBscCGM->obCampoCod->setValue             ( $inNumCGM                                 );
    $obBscCGM->obCampoCod->obEvento->setOnBlur  ( "buscaValorFiltro('buscaCGMFiltro');"     );
    $obBscCGM->setFuncaoBusca                   ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgm.php','frm','inNumCGM','inCampoInner','','".Sessao::getId()."&inFiltro=2','800','550')" );

    $obCmbRegistro = new Select;
    $obCmbRegistro->setRotulo                   ( "Matrícula"                                );
    $obCmbRegistro->setTitle                    ( "Informe o contrato do CGM selecionado."  );
    $obCmbRegistro->setName                     ( "inRegistro"                              );
    $obCmbRegistro->setValue                    ( $inRegistro                               );
    $obCmbRegistro->setStyle                    ( "width: 200px"                            );
    $obCmbRegistro->addOption                   ( "", "Selecione"                           );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                    ( "Filtrar por Matrícula"                    );
    $obFormulario->addComponente                ( $obBscCGM                                 );
    $obFormulario->addComponente                ( $obCmbRegistro                            );

    $obFormulario->montaInnerHtml();
    $Html = $obFormulario->getHtml();

    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$Html."';    \n";
    $stJs .= "f.stOpcaoEval.value  = '';                                \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limparFiltro($boExecuta=false)
{
    $stJs .= "f.stConcessao[0].checked = true;  \n";
    $stJs .= geraSpan1Filtro();
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montaMensagemVTCalculado($inCodContrato)
{
    include_once (CAM_GRH_BEN_MAPEAMENTO."TBeneficioConcessaoValeTransporte.class.php"  );
    include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );

    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);

    $obTBeneficioConcessaoValeTransporte = new TBeneficioConcessaoValeTransporte();
    $obTBeneficioConcessaoValeTransporte->setDado("cod_contrato", $inCodContrato);
    $obTBeneficioConcessaoValeTransporte->setDado("cod_periodo_movimentacao", $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
    $obTBeneficioConcessaoValeTransporte->recuperaVTCalculado($rsValeTransporte);

    $stDescricao = "";
    if ($rsValeTransporte->getNumLinhas() == 1 ) {
        $stDescricao = ", Matrícula possui cálculo na folha salário";
    }

    return $stDescricao;
}

function processaConcessoesCadastradasPorContrato($rsRecordSet , $boAgrupar=false)
{
    $arConcessoes = array();
    while ( !$rsRecordSet->eof() ) {
        $arTemp = array();
        if ( $rsRecordSet->getCampo('grupo') == "" ) {
            $stDescValeTransporte = trim($rsRecordSet->getCampo("nom_municipio_o"))."/".trim($rsRecordSet->getCampo("origem"))." - ".trim($rsRecordSet->getCampo('nom_municipio_d'))."/".trim($rsRecordSet->getCampo("destino"));
            $stValeTransporte = $rsRecordSet->getCampo('vigencia') ." - ". $stDescValeTransporte;
            $arTemp['cod_concessao']        = $rsRecordSet->getCampo('cod_concessao');
            $arTemp['cod_mes']              = $rsRecordSet->getCampo('cod_mes');
            $arTemp['exercicio']            = $rsRecordSet->getCampo('exercicio');
            $arTemp['cod_vale_transporte']  = $rsRecordSet->getCampo('cod_vale_transporte');
            $arTemp['cod_tipo']             = $rsRecordSet->getCampo('cod_tipo');
            $arTemp['quantidade']           = $rsRecordSet->getCampo('quantidade');
            $arTemp['inicializado']         = $rsRecordSet->getCampo('inicializado');
            $arTemp['registro']             = $rsRecordSet->getCampo('registro');
            $arTemp['cod_contrato']         = $rsRecordSet->getCampo('cod_contrato');
            $arTemp['grupo']                = ($rsRecordSet->getCampo('cod_grupo') == 0)? '&nbsp;' : trim($rsRecordSet->getCampo('grupo'));
            $arTemp['cod_grupo']            = ($rsRecordSet->getCampo('cod_grupo') == 0)? '' : $rsRecordSet->getCampo('cod_grupo');
            $arTemp['bo_grupo']             = ($rsRecordSet->getCampo('cod_grupo') == 0)? false : true;
            $arTemp['numcgm']               = $rsRecordSet->getCampo('numcgm');
            $arTemp['nom_cgm']              = $rsRecordSet->getCampo('nom_cgm');
            $arTemp['mes']                  = ($rsRecordSet->getCampo('cod_grupo') == 0)? $rsRecordSet->getCampo('mes') : '&nbsp;';
            $arTemp['vigencia']             = $rsRecordSet->getCampo('vigencia');
            $arTemp['vale_transporte']      = ($rsRecordSet->getCampo('cod_grupo') == 0)? $stValeTransporte : '&nbsp;';
            $arTemp['stDescQuestao']        = $rsRecordSet->getCampo('registro') ."/". $rsRecordSet->getCampo('mes').montaMensagemVTCalculado($rsRecordSet->getCampo('cod_contrato'));
            $arConcessoes[]                 = $arTemp;
        }
        $rsRecordSet->proximo();
    }
    $rsRecordSet->setPrimeiroElemento();
    while ( !$rsRecordSet->eof() ) {
        $rsRecordSet->proximo();
        $stGrupo = $rsRecordSet->getCampo('grupo');
        $rsRecordSet->anterior();
        if ( $rsRecordSet->getCampo('grupo') != "" and $stGrupo != $rsRecordSet->getCampo('grupo') ) {
            $arTemp['cod_concessao']        = $rsRecordSet->getCampo('cod_concessao');
            $arTemp['cod_mes']              = $rsRecordSet->getCampo('cod_mes');
            $arTemp['exercicio']            = $rsRecordSet->getCampo('exercicio');
            $arTemp['cod_vale_transporte']  = $rsRecordSet->getCampo('cod_vale_transporte');
            $arTemp['cod_tipo']             = $rsRecordSet->getCampo('cod_tipo');
            $arTemp['quantidade']           = $rsRecordSet->getCampo('quantidade');
            $arTemp['inicializado']         = $rsRecordSet->getCampo('inicializado');
            $arTemp['registro']             = $rsRecordSet->getCampo('registro');
            $arTemp['cod_contrato']         = $rsRecordSet->getCampo('cod_contrato');
            $arTemp['grupo']                = ($rsRecordSet->getCampo('cod_grupo') == 0)? '&nbsp;' : trim($rsRecordSet->getCampo('grupo'));
            $arTemp['cod_grupo']            = ($rsRecordSet->getCampo('cod_grupo') == 0)? '' : $rsRecordSet->getCampo('cod_grupo');
            $arTemp['bo_grupo']             = ($rsRecordSet->getCampo('cod_grupo') == 0)? false : true;
            $arTemp['numcgm']               = $rsRecordSet->getCampo('numcgm');
            $arTemp['nom_cgm']              = $rsRecordSet->getCampo('nom_cgm');
            $arTemp['mes']                  = ($rsRecordSet->getCampo('cod_grupo') == 0)? $rsRecordSet->getCampo('mes') : '&nbsp;';
            $arTemp['vigencia']             = $rsRecordSet->getCampo('vigencia');
            $arTemp['vale_transporte']      = '&nbsp;';
            $arTemp['stDescQuestao']        = $rsRecordSet->getCampo('registro') . "/" . trim($rsRecordSet->getCampo('grupo'));
            $arConcessoes[]                 = $arTemp;
        }
        $rsRecordSet->proximo();
    }
    if ($boAgrupar) {
        $arConsulta = array();
        $arTemp     = array();
        foreach ($arConcessoes as $arConcessao) {
            $arConcessao['boAgrupar'] = true;
            $stConsulta = $arConcessao['registro']."-".$arConcessao['cod_mes']."-".$arConcessao['exercicio'];
            if ($arTemp[$stConsulta]['cod_concessao'] != "") {
                $stCodConcessao = $arTemp[$stConsulta]['cod_concessao'] ."-".$arConcessao['cod_concessao'];
                $arConcessao['cod_concessao'] = $stCodConcessao;
            }
            $arTemp[$stConsulta] = $arConcessao;
        }
        $arConcessoes = array();
        foreach ($arTemp as $arConcessao) {
            $arConcessoes[] = $arConcessao;
        }
    }
    $rsRecordSet = new recordset;
    $rsRecordSet->preenche($arConcessoes);

    return $rsRecordSet;
}

function processaConcessoesCadastradasPorGrupo($rsRecordSet,$boAgrupar=false)
{
    $arConcessoes = array();
    if ($boAgrupar) {
        $arConcessoes = $rsRecordSet->getElementos();
        $arConsulta = array();
        $arTemp     = array();
        foreach ($arConcessoes as $arConcessao) {
            $arConcessao['boAgrupar'] = true;
            $stConsulta = $arConcessao['cod_grupo']."-".$arConcessao['cod_mes']."-".$arConcessao['exercicio'];
            if ($arTemp[$stConsulta]['cod_concessao'] != "") {
                $stCodConcessao = $arTemp[$stConsulta]['cod_concessao'] ."-".$arConcessao['cod_concessao'];
                $arConcessao['cod_concessao'] = $stCodConcessao;
            }
            $arTemp[$stConsulta] = $arConcessao;
        }
        $arConcessoes = array();
        foreach ($arTemp as $arConcessao) {
            $arConcessoes[] = $arConcessao;
        }
    } else {
        while ( !$rsRecordSet->eof() ) {
            $arTemp = array();
            $stGrupo        = $rsRecordSet->getCampo('grupo');
            $inExercicio    = $rsRecordSet->getCampo('exercicio');
            $rsRecordSet->proximo();
            if( $stGrupo     != $rsRecordSet->getCampo('grupo')
            or  $inExercicio != $rsRecordSet->getCampo('exercicio')
            ){
                $rsRecordSet->anterior();
                $stDescValeTransporte = trim($rsRecordSet->getCampo("nom_municipio_o"))."/".trim($rsRecordSet->getCampo("origem"))." - ".trim($rsRecordSet->getCampo('nom_municipio_d'))."/".trim($rsRecordSet->getCampo("destino"));
                $stValeTransporte .= $rsRecordSet->getCampo('mes') ." - ". $rsRecordSet->getCampo('vigencia') ." - ". $stDescValeTransporte;
                $arTemp['cod_concessao']        = $rsRecordSet->getCampo('cod_concessao');
                $arTemp['grupo']                = $rsRecordSet->getCampo('grupo');
                $arTemp['cod_grupo']            = $rsRecordSet->getCampo('cod_grupo');
                $arTemp['mes']                  = $rsRecordSet->getCampo('mes');
                $arTemp['vigencia']             = $rsRecordSet->getCampo('vigencia');
                $arTemp['cod_mes']              = $rsRecordSet->getCampo('cod_mes');
                $arTemp['exercicio']            = $rsRecordSet->getCampo('exercicio');
                $arTemp['vale_transporte']      = $stValeTransporte;
                $arConcessoes[]                 = $arTemp;
                $stValeTransporte               = '';
            } else {
                $rsRecordSet->anterior();
                $stDescValeTransporte = $rsRecordSet->getCampo("nom_municipio_o")."/".$rsRecordSet->getCampo("origem")." - ".$rsRecordSet->getCampo('nom_municipio_d')."/".$rsRecordSet->getCampo("destino");
                $stValeTransporte .= $rsRecordSet->getCampo('mes') ." - ". $rsRecordSet->getCampo('vigencia') ." - ". $stDescValeTransporte ."<br>";
            }
            $rsRecordSet->proximo();
        }
    }
    $rsRecordSet = new recordset;
    $rsRecordSet->preenche($arConcessoes);

    return $rsRecordSet;
}

function sequenciaAlternativa1()
{
    Sessao::write('inCodTipo', $_POST['inCodTipo']);
    $stJs.= habilitaCalendario();
    $stJs.= habilitaQuantidade();
    $stJs.= geraSpan5();
    $stJs.= habilitaQuantidadeSemanal($_POST['inCodMes'],$_POST['inAno']);

    return $stJs;
}

function buscaCGM($boExecuta=false,$inOpcao=1)
{
    $obRCGMPessoaFisica = new RCGMPessoaFisica;
    $obRPessoalServidor = new RPessoalServidor;
    $obRCGMPessoaFisica->setNumCGM( $_POST['inNumCGM'] );
    $obRCGMPessoaFisica->consultarCGM( $rsCGMPessoaFisica );
    $boErro = false;
    if ( $rsCGMPessoaFisica->getNumLinhas() <= 0 or $obRCGMPessoaFisica->getNumCGM() == 0  ) {
        $stJs .= "alertaAviso('@CGM ".$_POST['inNumCGM']." não encontrado.','form','erro','".Sessao::getId()."');";
        $stJs .= 'f.inNumCGM.value = "";';
        $stJs .= 'f.inCampoInner.focus();';
        $stJs .= 'd.getElementById("inCampoInner").innerHTML = "&nbsp;&nbsp;";';
        $boErro = true;
    }
    if ( $obRCGMPessoaFisica->getNumCGM() and !$boErro ) {
        $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM( $_POST['inNumCGM'] );
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->consultaCGMServidor( $rsServidor, "", $boTransacao );
        if ( $rsServidor->getNumLinhas() <= 0 ) {
            $stJs .= "alertaAviso('@CGM ".$_POST['inNumCGM']." não cadastrado como servidor.','form','erro','".Sessao::getId()."');\n";
            $stJs .= "f.inNumCGM.value = '';\n";
            $stJs .= "f.inCampoInner.focus();\n";
            $stJs .= 'd.getElementById("inCampoInner").innerHTML = "&nbsp;&nbsp;";';
            $boErro = true;
        } else {
            $stJs .= 'd.getElementById("inCampoInner").innerHTML = "'.$rsCGMPessoaFisica->getCampo('nom_cgm').'";';
            $obRPessoalServidor->consultaRegistrosServidor( $rsRegistros );
            $stJs .= "limpaSelect(f.inRegistro,0);\n";
            $stJs .= "f.inRegistro[0] = new Option('Selecione','','selected');\n";
            $inIndex = 1;
            while ( !$rsRegistros->eof() ) {
                //if ($inOpcao == 1) {
                //    $stJs .= "f.inRegistro[".$inIndex."] = new Option('".$rsRegistros->getCampo('registro')."','".$rsRegistros->getCampo('cod_contrato')."','');\n";
                //} else {
                    $stJs .= "f.inRegistro[".$inIndex."] = new Option('".$rsRegistros->getCampo('registro')."','".$rsRegistros->getCampo('registro')."','');\n";
                //}
                $inIndex++;
                $rsRegistros->proximo();
            }
        }
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencheSpan6($boExecuta=false)
{
    Sessao::write('concessoes', array());
    if ($_POST['inCodGrupo']) {
        $obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo( $_POST['inCodGrupo'] );
        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->listarGrupoConcessaoValeTransporte($rsConcessaoValeTransporte);
        $arQuantidadeMensal = array();
        $inProxId = 1;
        while ( !$rsConcessaoValeTransporte->eof() ) {
            switch ( $rsConcessaoValeTransporte->getCampo('cod_dia') ) {
                case 1:
                    $inDomingo = $rsConcessaoValeTransporte->getCampo('quantidade_semanal');
                    $boDomingo = ( $rsConcessaoValeTransporte->getCampo('obrigatorio_semanal') == 't' ) ? 'on' : '';
                break;
                case 2:
                    $inSegunda = $rsConcessaoValeTransporte->getCampo('quantidade_semanal');
                    $boSegunda = ( $rsConcessaoValeTransporte->getCampo('obrigatorio_semanal') == 't' ) ? 'on' : '';
                break;
                case 3:
                    $inTerca = $rsConcessaoValeTransporte->getCampo('quantidade_semanal');
                    $boTerca = ( $rsConcessaoValeTransporte->getCampo('obrigatorio_semanal') == 't' ) ? 'on' : '';
                break;
                case 4:
                    $inQuarta = $rsConcessaoValeTransporte->getCampo('quantidade_semanal');
                    $boQuarta = ( $rsConcessaoValeTransporte->getCampo('obrigatorio_semanal') == 't' ) ? 'on' : '';
                break;
                case 5:
                    $inQuinta = $rsConcessaoValeTransporte->getCampo('quantidade_semanal');
                    $boQuinta = ( $rsConcessaoValeTransporte->getCampo('obrigatorio_semanal') == 't' ) ? 'on' : '';
                break;
                case 6:
                    $inSexta = $rsConcessaoValeTransporte->getCampo('quantidade_semanal');
                    $boSexta = ( $rsConcessaoValeTransporte->getCampo('obrigatorio_semanal') == 't' ) ? 'on' : '';
                break;
                case 7:
                    $inSabado = $rsConcessaoValeTransporte->getCampo('quantidade_semanal');
                    $boSabado = ( $rsConcessaoValeTransporte->getCampo('obrigatorio_semanal') == 't' ) ? 'on' : '';
                break;
                $arTemp = array();
            }
            $arTemp['stData']        = $rsConcessaoValeTransporte->getCampo('stdata');
            $arTemp['boObrigatorio'] = ( $rsConcessaoValeTransporte->getCampo('obrigatorio') == 't' ) ? 'on' : '';
            $arTemp['inQuantidade']  = $rsConcessaoValeTransporte->getCampo('quantidade');
            $arQuantidadeMensal[]    = $arTemp;

            $inQuantidadeMensal  = $rsConcessaoValeTransporte->getCampo('quantidade_mensal');
            $inCodCalendario     = $rsConcessaoValeTransporte->getCampo('cod_calendario');
            $inCodValeTransporte = $rsConcessaoValeTransporte->getCampo('cod_vale_transporte');
            $inAno               = $rsConcessaoValeTransporte->getCampo('exercicio');
            $inMes               = $rsConcessaoValeTransporte->getCampo('cod_mes');
            $inCodTipo           = $rsConcessaoValeTransporte->getCampo('cod_tipo');
            $dtVigencia          = $rsConcessaoValeTransporte->getCampo('vigencia');
            $inCodConcessao      = $rsConcessaoValeTransporte->getCampo('cod_concessao');

            $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRBeneficioValeTransporte->setCodValeTransporte( $inCodValeTransporte );
            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRBeneficioValeTransporte->listarValeTransporte( $rsValeTransporte );

            $arQuantidadeSemanal = array();
            $arQuantidadeSemanal[0]['boObrigatorio'] = $boDomingo;
            $arQuantidadeSemanal[0]['inQuantidade']  = $inDomingo;
            $arQuantidadeSemanal[1]['boObrigatorio'] = $boSegunda;
            $arQuantidadeSemanal[1]['inQuantidade']  = $inSegunda;
            $arQuantidadeSemanal[2]['boObrigatorio'] = $boTerca;
            $arQuantidadeSemanal[2]['inQuantidade']  = $inTerca;
            $arQuantidadeSemanal[3]['boObrigatorio'] = $boQuarta;
            $arQuantidadeSemanal[3]['inQuantidade']  = $inQuarta;
            $arQuantidadeSemanal[4]['boObrigatorio'] = $boQuinta;
            $arQuantidadeSemanal[4]['inQuantidade']  = $inQuinta;
            $arQuantidadeSemanal[5]['boObrigatorio'] = $boSexta;
            $arQuantidadeSemanal[5]['inQuantidade']  = $inSexta;
            $arQuantidadeSemanal[6]['boObrigatorio'] = $boSabado;
            $arQuantidadeSemanal[6]['inQuantidade']  = $inSabado;

            $stDescValeTransporte = $rsValeTransporte->getCampo("nom_municipio_o")."/".$rsValeTransporte->getCampo("origem")." - ".$rsValeTransporte->getCampo('nom_municipio_d')."/".$rsValeTransporte->getCampo("destino");

            $arElementos = array();
            $arElementos['inId']                = $inProxId;
            $arElementos['stValeTransporte']    = $stDescValeTransporte;
            $arElementos['inCodValeTransporte'] = $inCodValeTransporte;
            $arElementos['inAno']               = $inAno;
            $arElementos['inCodMes']            = $inMes;
            $arElementos['stTipo']              = ($inCodTipo == 1 ) ? 'Mensal' : 'Diários';
            $arElementos['inCodTipo']           = $inCodTipo;
            $arElementos['inCodCalendario']     = $inCodCalendario;
            $arElementos['dtVigencia']          = $dtVigencia;
            $arElementos['inQuantidadeMensal']  = $inQuantidadeMensal;
            $arElementos['arQuantidadeSemanal'] = $arQuantidadeSemanal;
            $arElementos['arQuantidadeMensal']  = $arQuantidadeMensal;
            $arElementos['inCodConcessao']      = $inCodConcessao;
            $rsConcessaoValeTransporte->proximo();
            $inCodConcessaoProx = $rsConcessaoValeTransporte->getCampo('cod_concessao');
            $inMesProx          = $rsConcessaoValeTransporte->getCampo('cod_mes');
            $inAnoProx          = $rsConcessaoValeTransporte->getCampo('exercicio');
            $rsConcessaoValeTransporte->anterior();
            if ($inCodConcessao != $inCodConcessaoProx or $inMes != $inMesProx or $inAno != $inAnoProx) {
                $arConcessoes   = Sessao::read('concessoes');
                $arConcessoes[] = $arElementos;
                Sessao::write('concessoes', $arConcessoes);
                $inProxId++;
            }
            $rsConcessaoValeTransporte->proximo();
        }
        $stJs .= geraSpan6();
        Sessao::write('boUtilizarGrupo', ( isset($_POST['boUtilizarGrupo']) ) ? $_POST['boUtilizarGrupo'] : Sessao::read('boUtilizarGrupo'));
        $stJs .= "f.boUtilizarGrupo.disabled = true;               \n";
    } else {
        $stJs .= "d.getElementById('spnSpan6').innerHTML   = '';    \n";
        $stJs .= "d.getElementById('spnSpan5').innerHTML   = '';    \n";
        $stJs .= "f.boUtilizarGrupo.disabled = false;               \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

//Preenche a combo de meses com o mês atual e o mês posterior
function preencheMes($boExecuta = false)
{
    include_once (CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoPeriodoMovimentacao.class.php');

    $obRBeneficioConcessaoValeTransporte = new RBeneficioConcessaoValeTransporte;
    $obRBeneficioConcessaoValeTransporte->listarMes($rsMeses);
    $arMeses = $rsMeses->getElementos();

    $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
    $stDataUltimaMovimentacao = $rsUltimaMovimentacao->getCampo( 'dt_final' );
    $stDataUltimaMovimentacao = explode( '/', $stDataUltimaMovimentacao );

    $inAnoAtual = $stDataUltimaMovimentacao[2];

    $inAno      = $_POST['inAno']? (int) $_POST['inAno']: date('Y');

    if ($inAno == $inAnoAtual) {
        $inMesAtual = $stDataUltimaMovimentacao[1];
    } else {
        $inMesAtual = 1;
    }

    $stJs .= "limpaSelect(f.stMes,0); \n";
    $stJs .= "f.inCodMes.value = $inMesAtual; \n";
    $i = 0;
    while ( ($inMesAtual <= 12 ) and ($inAno >= $inAnoAtual)) {
         $stJs .= "f.stMes[$i] = new Option('".$arMeses[$inMesAtual-1]['descricao']."','".$inMesAtual."'); \n";
         $inMesAtual++;
         $i++;
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function cancelarDetalhar($boExecuta=false)
{
    ;
    Sessao::write('inDomingo', '');
    Sessao::write('inSegunda', '');
    Sessao::write('inTerca'  , '');
    Sessao::write('inQuarta' , '');
    Sessao::write('inQuinta' , '');
    Sessao::write('inSexta'  , '');
    Sessao::write('inSabado' , '');
    Sessao::write('boDomingo', false);
    Sessao::write('boSegunda', false);
    Sessao::write('boTerca'  , false);
    Sessao::write('boQuarta' , false);
    Sessao::write('boQuinta' , false);
    Sessao::write('boSexta'  , false);
    Sessao::write('boSabado' , false);

    $stJs .= "f.inQuantidadeMensal.value = '0';    					    \n";
    $stJs .= "f.hdnQuantidadeMensal.value= '0';					        \n";
    $stJs .=  "for (i = 1; i < 7; i++) {   								\n";
    $stJs .=  "		eval(\"f.inQuantidade_\"+i+\".value = '';\");       \n";
    $stJs .=  "		eval(\"f.boObrigatorio_\"+i+\".checked = false;\"); \n";
    $stJs .=  "		eval(\"f.boObrigatorio_\"+i+\".disabled = true;\");	\n";
    $stJs .=  "} 														\n";

     if ( count(Sessao::read('valeDias')) > 0 ) {
        Sessao::remove('valeDias');
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

switch ($_POST["stCtrl"]) {
    case "geraSpan1":
        $stJs.= geraSpan1();
    break;
    case "geraSpan2":
        $stJs.= geraSpan2();
    break;
    case "geraSpan3":
        $stJs.= geraSpan3();
    break;
    case "geraSpan4":
        $stJs.= geraSpan4();
    break;
    case "geraSpan9":
        $stJs.= geraSpan9();
    break;
    case "geraSpan1Filtro":
        $stJs.= geraSpan1Filtro();
    break;
    case "geraSpan2Filtro":
        $stJs.= geraSpan2Filtro();
    break;
    case "geraSpan3Filtro":
        $stJs.= geraSpan3Filtro();
    break;
    case "geraSpan4Filtro":
        $stJs.= geraSpan4Filtro();
    break;
    case "sequenciaAlternativa1":
        $stJs.= sequenciaAlternativa1();
    break;
    case "habilitaQuantidadeSemanal":
        $stJs.= habilitaQuantidadeSemanal($_POST['inCodMes'],$_POST['inAno']);
    break;
    case "calculaQuantidade":
        $stJs.= calculaQuantidade();
    break;
    case "incluirConcessao":
        $stJs.= incluirConcessao();
    break;
    case "excluirConcessao":
        $stJs.= excluirConcessao();
    break;
    case "alterarConcessao":
        $stJs.= alterarConcessao();
    break;
    case "limparConcessao":
        $stJs.= limparConcessao();
    break;
    case "montaAlterarConcessao":
        $stJs.= montaAlterarConcessao();
    break;
    case "limparForm":
        $stJs.= limparForm();
    break;
    case "limparFiltro":
        $stJs.= limparFiltro();
    break;
    case "preencheCGMdoContrato":
        $stJs.= preencheCGMdoContrato();
    break;
    case "preencheSpan6":
        $stJs.= preencheSpan6();
    break;
    case "buscaValeTransporte":
        $stJs.= buscaValeTransporte();
    break;
    case "buscaCGM":
        $stJs.= buscaCGM();
    break;
    case "buscaCGMFiltro":
        $stJs.= buscaCGM(false,2);
    break;
    case 'preencheMes':
        preencheMes( true );
    break;
    case 'cancelarDetalhar':
        cancelarDetalhar( true );
    break;
}

if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
