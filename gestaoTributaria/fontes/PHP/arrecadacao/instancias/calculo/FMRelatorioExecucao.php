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

/**
  * Página de Formulario para Relatorio de dados do calculo realizado
  * Data de criação : 03/01/2006

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: FMRelatorioExecucao.php 62452 2015-05-12 16:56:06Z michel $

    Caso de uso: uc-05.03.05
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterCalculos";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PRManterCalculo.php";
$pgOcul          = "OCManterCalculo.php";
$pgJs            = "JSManterCalculo.js";
include_once( $pgJs );
Sessao::write( "sessao_tranf5", null );
Sessao::write( "lancados", -1 );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

if ($_REQUEST["inCodGrupo"]) {
    $obRARRGrupo = new RARRGrupo;
    list($inCodGrupo,$inExercicioGrupo) = explode ( '/' , $_REQUEST["inCodGrupo"] );
    $obRARRGrupo->setCodGrupo   ( $inCodGrupo);
    $obRARRGrupo->setExercicio  ( $inExercicioGrupo);
    $obRARRGrupo->consultarGrupo();
    $stDescGrupo = $obRARRGrupo->getCodGrupo()."/".$obRARRGrupo->getExercicio()." -  ".$obRARRGrupo->getDescricao();
    $stTipoCalculo = ucfirst($_REQUEST["stTipoCalculo"]);
    $obRARRGrupo->listarCreditos( $rsCreditosGrupo );
    $numeroCreditosGrupo = $rsCreditosGrupo->getNumLinhas();
} else { // se for calculo de credito
    $numeroCreditosGrupo =1;
}

#echo '<b>Memória utilizada: </b>'. memory_get_usage()/1024/1024;
set_time_limit(0);
$stCalculos = null;

if ($stTipoCalculo == 'Geral') {
    Sessao::write( "grupo_automatico", $_REQUEST["inCodGrupo"] );
    $stTipoCalculo = 'geral';
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    list( $inCodGrupo , $inExercicio ) = explode( '/' , $_REQUEST[ 'inCodGrupo' ] );
    if ($_REQUEST["boSimular"]) {
        $stSql = "   SELECT acgr.cod_calculo
                      FROM arrecadacao.calculo_grupo_credito    AS acgr

                      JOIN arrecadacao.calculo                  AS ac
                        ON ac.cod_calculo     = acgr.cod_calculo
                       AND ac.ativo           = FALSE
                       AND ac.simulado        = TRUE

                 LEFT JOIN arrecadacao.lancamento_calculo       AS alc
                        ON alc.cod_calculo    = acgr.cod_calculo

                     WHERE cod_grupo          = ".$inCodGrupo."
                       AND acgr.ano_exercicio = '".$inExercicio."'
                       AND alc.cod_calculo IS NULL ";
    } else {
        $stSql = "   SELECT acgr.cod_calculo
                      FROM arrecadacao.calculo_grupo_credito    AS acgr

                      JOIN arrecadacao.calculo                  AS ac
                        ON ac.cod_calculo     = acgr.cod_calculo
                       AND ac.ativo           = TRUE

                 LEFT JOIN arrecadacao.lancamento_calculo       AS alc
                        ON alc.cod_calculo    = acgr.cod_calculo

                     WHERE cod_grupo          = ".$inCodGrupo."
                       AND acgr.ano_exercicio = '".$inExercicio."'
                       AND alc.cod_calculo IS NULL ";
    }

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $inX = 0;
    while ( !$rsRecordSet->Eof() ) {
        if ($inX) {
            $stCalculos .= ",";
        }else
            $inX = 1;

        $stCalculos .= $rsRecordSet->getCampo("cod_calculo");
        $rsRecordSet->proximo();
    }
} else {
    $nome_arquivo_calculo = Sessao::read('arquivo_calculos');
    if (empty($nome_arquivo_calculo)) {
        $stErro = "Não foi possível abrir o arquivo de cálculos.";
    } else {
        if ( !$arquivo = fopen ( $nome_arquivo_calculo, 'r') ) {
            $stErro = "Não foi possível abrir o arquivo de cálculos.";
        } else {
            $tamanho_arquivo = filesize($nome_arquivo_calculo);
            if ($tamanho_arquivo > 0) {
                $stCalculos = fread ( $arquivo, filesize($nome_arquivo_calculo) );
                $stCalculos = substr( $stCalculos, 0, strlen ( $stCalculos) -1 );
            } else {
                $stErro = "Arquivo de cálculos com erro";
            }
        }
    }
}

if (!$stErro) {
    /*
        deve-se dividir a string de STCalculos em arrays de 1000 calculos,
        executar o lançamento de 1000 em 1000 e ir concatenando as
        arrays com os resultados, preencher o recordset, para depois exibir.
    */
    $arCalculos = explode ( ',', $stCalculos );
    $contTodosCalculos = count( $arCalculos );
    #echo 'Calculos: '.$contTodosCalculos; exit;
    $contArrayCalculosParaLancamento = 0;
    $cont = 0;
    $arCalculosParaLancamento = array();
    $limitador = 1000;
    $stLinha = null;
    while ($cont < $contTodosCalculos) {

        $boMudouLinha = false;
        $stLinha .= $arCalculos[$cont].", ";

        #$arCalculosParaLancamento
        if ( (($cont+1) % $numeroCreditosGrupo == 0) && $cont > $limitador ) {
            $limitador += 1000;
            $boMudouLinha = true;
        }
        if ( $boMudouLinha || ( ($cont+1) == $contTodosCalculos) ) {
            $stLinha = substr ( $stLinha, 0, (strlen( $stLinha ) -2) );
            $arCalculosParaLancamento[$contArrayCalculosParaLancamento] = $stLinha;
            $stLinha = null;
            $contArrayCalculosParaLancamento++;
        }

        $cont++;
    }
} else {
    SistemaLegado::exibeAviso( urlencode($stErro ), "n_erro", "erro", Sessao::getId(), "../" );
}

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obLblGrupoCredito = new Label;
$obLblGrupoCredito->setRotulo   ( "Grupo de Créditos"   );
$obLblGrupoCredito->setValue    ( $stDescGrupo          );
$obLblGrupoCredito->setName     ( "lblGrupo"            );

$obLblTipoCalculo = new Label;
$obLblTipoCalculo->setRotulo   ( "Tipo de Cálculo"   );
if ($_REQUEST["boSimular"]) {
    Sessao::write( "simular_relatorio", true );
    $obLblTipoCalculo->setValue ( $stTipoCalculo." ( simulação )" );
} else {
   $obLblTipoCalculo->setValue ( $stTipoCalculo      );
}
$obLblTipoCalculo->setName     ( "lblTipoCalculo"    );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget           ( "oculto"          );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_ARR_INSTANCIAS."calculo/OCRelatorioExecucao.php" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo     ( "Dados do Cálculo"  	    );
$obFormulario->addComponente ( $obLblGrupoCredito       );
$obFormulario->addComponente ( $obLblTipoCalculo        );

/* INTERVALOS */
$stIntervaloImobiliario = null;
$boIntervaloImobiliario = false;
if ($_REQUEST['inInscricaoImobiliariaFinal'] && !$_REQUEST['inInscricaoImobiliariaInicial']) {
    $stIntervaloImobiliario = $_REQUEST['inInscricaoImobiliariaFinal'];
    $boIntervaloImobiliario = true;
} else {
    if ($_REQUEST['inInscricaoImobiliariaInicial']) {
        $boIntervaloImobiliario = true;
        $stIntervaloImobiliario = $_REQUEST['inInscricaoImobiliariaInicial'];
    }
    if ($_REQUEST['inInscricaoImobiliariaFinal']) {
        $stIntervaloImobiliario .= " até ".$_REQUEST['inInscricaoImobiliariaFinal'];
    }
}
$obLblIntervaloImobiliario = new Label;
$obLblIntervaloImobiliario->setValue    ( $stIntervaloImobiliario );
$obLblIntervaloImobiliario->setRotulo   ( "Intervalo Inscrição Municipal" );
$obLblIntervaloImobiliario->setTitle    ( "Intervalo Inscrição Municipal" );

/* INTERVALOS ECONOMICO */
$stIntervaloEconomico = null;
$boIntervaloEconomico = false;
if ($_REQUEST['inNumInscricaoEconomicaFinal'] && !$_REQUEST['inNumInscricaoEconomicaInicial']) {
    $stIntervaloEconomico = $_REQUEST['inNumInscricaoEconomicaFinal'];
    $boIntervaloEconomico = true;
} else {
    if ($_REQUEST['inNumInscricaoEconomicaInicial']) {
        $stIntervaloEconomico = $_REQUEST['inNumInscricaoEconomicaInicial'];
        $boIntervaloEconomico = true;
    }
    if ($_REQUEST['inNumInscricaoEconomicaFinal']) {
        $stIntervaloEconomico .= " até ".$_REQUEST['inNumInscricaoEconomicaFinal'];
    }
}
$obLblIntervaloEconomico = new Label;
$obLblIntervaloEconomico->setValue    ( $stIntervaloEconomico );
$obLblIntervaloEconomico->setRotulo   ( "Intervalo Inscrição Econômica" );
$obLblIntervaloEconomico->setTitle    ( "Intervalo Inscrição Econômica" );

/* INTERVALOS CONTRIBUINTE */
$stIntervaloContribuinte = null;
$boIntervaloContribuinte = false;
if ($_REQUEST['inCodContribuinteFinal'] && !$_REQUEST['inCodContribuinteInicial']) {
    $stIntervaloContribuinte = $_REQUEST['inCodContribuinteFinal'];
    $boIntervaloContribuinte = true;
} else {
    if ($_REQUEST['inCodContribuinteInicial']) {
        $stIntervaloContribuinte = $_REQUEST['inCodContribuinteInicial'];
        $boIntervaloContribuinte = true;
    }
    if ($_REQUEST['inCodContribuinteFinal']) {
        $stIntervaloContribuinte .= " até ".$_REQUEST['inCodContribuinteFinal'];
    }
}
$obLblIntervaloContribuinte = new Label;
$obLblIntervaloContribuinte->setValue    ( $stIntervaloContribuinte );
$obLblIntervaloContribuinte->setRotulo   ( "Intervalo Inscrição Econômica" );
$obLblIntervaloContribuinte->setTitle    ( "Intervalo Inscrição Econômica" );

if ($stTipoCalculo == 'geral') {
    $boIntervaloImobiliario = true;
    if ($_REQUEST["boSimular"]) {
        $obLblIntervaloImobiliario->setValue    ( "<b>Simulação do Cálculo Geral</b>" );
    } else {
        $obLblIntervaloImobiliario->setValue    ( "<b>Cálculo Geral</b>" );
    }
}
if ( $boIntervaloImobiliario )  $obFormulario->addComponente ( $obLblIntervaloImobiliario );
if ( $boIntervaloEconomico )  $obFormulario->addComponente ( $obLblIntervaloEconomico );
if ( $boIntervaloContribuinte )  $obFormulario->addComponente ( $obLblIntervaloContribuinte );

$contCalculos = count ($arCalculosParaLancamento);
$cont = $contCalculosOK = $contCalculosErro = 0;
$obRARRCalculo = new RARRCalculo;
$arNovoTudo = array();

while ($cont < $contCalculos) {

    $obRARRCalculo->setCodCalculo( $arCalculosParaLancamento[$cont] );
    //echo '<h2>LISTANDO CALCULO</h2>';
    unset( $rsCalculos );
    $obRARRCalculo->listarRelatorioExecucao($rsCalculos);

    if ( $rsCalculos->getNumLinhas() > 0 ) {

        //echo '<h2>FOR EACH</h2>';
        foreach ($rsCalculos->arElementos as $valor) {
            if ($valor["status"] == "Erro") {
                $contCalculosErro ++;
            } else {
                $contCalculosOK ++;
            }
            $arNovoTudo[] = $valor;
        }
    }
    $cont++;
}

$obLblCalculosOK = new Label;
$obLblCalculosOK->setValue ( $contCalculosOK );
$obLblCalculosOK->setRotulo ( "Cálculos Realizados" );
$obLblCalculosOK->setTitle ( "Cálculos Realizados" );

$obLblCalculosNO = new Label;
$obLblCalculosNO->setValue ( $contCalculosErro );
$obLblCalculosNO->setRotulo ( "Cálculos Incorretos" );
$obLblCalculosNO->setTitle ( "Cálculos Incorretos" );

$obFormulario->addComponente ( $obLblCalculosOK );

$obFormulario->show();

unset( $stCalculos );
unset( $arCalculos );
unset( $rsCalculos );

if ($stTipoCalculo != 'geral') {
    $rsCalculos = new RecordSet;
    $rsCalculos->preenche ( $arNovoTudo );
    Sessao::write('rsCalculos', $rsCalculos );

    if ( (count ( $arNovoTudo ) < 500 ) and ( count ( $arNovoTudo ) > 0 ) && !$stErro ) {
        $rsCalculos->preenche ( $arNovoTudo );
        // orderna
        $rsCalculos->ordena ( 'inscricao' );

        //calculos VÀLIDOS, que vieram da regra de cálculo.
        //Caso seja calculo geral OU parcial, ao clicar no botao LANÇAR,
        //será realizado lançamento com estes calculos

        $obListaCalculos = new Lista;
        $obListaCalculos->setRecordSet          ( $rsCalculos                );
        $obListaCalculos->setTitulo             ( "Registros de Cálculo"     );
        $obListaCalculos->setMostraPaginacao    ( false                      );
        $obListaCalculos->setAlternado          ( true );
        $obListaCalculos->addCabecalho();
        $obListaCalculos->ultimoCabecalho->addConteudo("&nbsp;");
        $obListaCalculos->ultimoCabecalho->setWidth( 5 );
        $obListaCalculos->commitCabecalho();
        $obListaCalculos->addCabecalho();
        $obListaCalculos->ultimoCabecalho->addConteudo("Código");
        $obListaCalculos->ultimoCabecalho->setWidth( 3 );
        $obListaCalculos->commitCabecalho();
        $obListaCalculos->addCabecalho();
        $obListaCalculos->ultimoCabecalho->addConteudo("Crédito");
        $obListaCalculos->ultimoCabecalho->setWidth( 25 );
        $obListaCalculos->commitCabecalho();
        $obListaCalculos->addCabecalho();
        $obListaCalculos->ultimoCabecalho->addConteudo("Contribuinte");
        $obListaCalculos->commitCabecalho();
        $obListaCalculos->addCabecalho();
        $obListaCalculos->ultimoCabecalho->addConteudo("Inscrição");
        $obListaCalculos->ultimoCabecalho->setWidth( 3 );
        $obListaCalculos->commitCabecalho();
        $obListaCalculos->addCabecalho();
        $obListaCalculos->ultimoCabecalho->addConteudo("Estado");
        $obListaCalculos->ultimoCabecalho->setWidth( 10 );
        $obListaCalculos->commitCabecalho();

        $obListaCalculos->addDado();
        $obListaCalculos->ultimoDado->setCampo       ( "cod_calculo" );
        $obListaCalculos->ultimoDado->setAlinhamento ( "ESQUERDA" );
        $obListaCalculos->commitDado();
        $obListaCalculos->addDado();
        $obListaCalculos->ultimoDado->setCampo       ( "[cod_credito].[cod_especie].[cod_genero].[cod_natureza] - [descricao_credito]" );
        $obListaCalculos->ultimoDado->setAlinhamento ( "ESQUERDA" );
        $obListaCalculos->commitDado();
        $obListaCalculos->addDado();
        $obListaCalculos->ultimoDado->setCampo       ( "[numcgm] - [nom_cgm]" );
        $obListaCalculos->ultimoDado->setAlinhamento ( "ESQUERDA" );
        $obListaCalculos->commitDado();
        $obListaCalculos->addDado();
        $obListaCalculos->ultimoDado->setCampo       ( "inscricao" );
        $obListaCalculos->ultimoDado->setAlinhamento ( "ESQUERDA" );
        $obListaCalculos->commitDado();
        $obListaCalculos->addDado();
        $obListaCalculos->ultimoDado->setCampo       ( "status" );
        $obListaCalculos->ultimoDado->setAlinhamento ( "ESQUERDA" );
        $obListaCalculos->commitDado();

        $obListaCalculos->show();
    }
} else {
    unset( $arNovoTudo );
}

$arCalculosErro = Sessao::read('arCalculoErro');
IF( $arCalculosErro ){
    
    $rsCalculosErro = new RecordSet;
    $rsCalculosErro->preenche ( $arCalculosErro );
    $rsCalculosErro->addFormatacao ( 'NUMERIC_BR' , 'valor' );
    $rsCalculosErro->ordena ('registro');

    $obListaCalculosErro = new Lista;
    $obListaCalculosErro->setRecordSet          ( $rsCalculosErro               );
    $obListaCalculosErro->setTitulo             ( "Cálculos incorretos"     );
    $obListaCalculosErro->setMostraPaginacao    ( false                      );
    $obListaCalculosErro->setAlternado          ( true );
    $obListaCalculosErro->setCampoAgrupado      ( 'registro' );
    $obListaCalculosErro->addCabecalho();
    $obListaCalculosErro->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaCalculosErro->ultimoCabecalho->setWidth( 5 );
    $obListaCalculosErro->commitCabecalho();
    $obListaCalculosErro->addCabecalho();
    $obListaCalculosErro->ultimoCabecalho->addConteudo("Inscrição");
    $obListaCalculosErro->ultimoCabecalho->setWidth( 5 );
    $obListaCalculosErro->commitCabecalho();
    $obListaCalculosErro->addCabecalho();
    $obListaCalculosErro->ultimoCabecalho->addConteudo("Crédito");
    $obListaCalculosErro->ultimoCabecalho->setWidth( 40 );
    $obListaCalculosErro->commitCabecalho();
    $obListaCalculosErro->addCabecalho();
    $obListaCalculosErro->ultimoCabecalho->addConteudo("Função");
    $obListaCalculosErro->ultimoCabecalho->setWidth( 25 );
    $obListaCalculosErro->commitCabecalho();
    $obListaCalculosErro->addCabecalho();
    $obListaCalculosErro->ultimoCabecalho->addConteudo("Erro");
    $obListaCalculosErro->ultimoCabecalho->setWidth( 25 );
    $obListaCalculosErro->commitCabecalho();
    $obListaCalculosErro->addCabecalho();
    $obListaCalculosErro->ultimoCabecalho->addConteudo("Valor");
    $obListaCalculosErro->ultimoCabecalho->setWidth( 25 );
    $obListaCalculosErro->commitCabecalho();

    $obListaCalculosErro->addDado();
    $obListaCalculosErro->ultimoDado->setCampo       ( "registro" );
    $obListaCalculosErro->ultimoDado->setAlinhamento ( "ESQUERDA" );
    $obListaCalculosErro->commitDado();
    $obListaCalculosErro->addDado();
    $obListaCalculosErro->ultimoDado->setCampo       ( "credito" );
    $obListaCalculosErro->ultimoDado->setAlinhamento ( "ESQUERDA" );
    $obListaCalculosErro->commitDado();
    $obListaCalculosErro->addDado();
    $obListaCalculosErro->ultimoDado->setCampo       ( "funcao" );
    $obListaCalculosErro->ultimoDado->setAlinhamento ( "ESQUERDA" );
    $obListaCalculosErro->commitDado();
    $obListaCalculosErro->addDado();
    $obListaCalculosErro->ultimoDado->setCampo       ( "erro" );
    $obListaCalculosErro->ultimoDado->setAlinhamento ( "ESQUERDA" );
    $obListaCalculosErro->commitDado();
    $obListaCalculosErro->addDado();
    $obListaCalculosErro->ultimoDado->setCampo       ( "valor" );
    $obListaCalculosErro->ultimoDado->setAlinhamento ( "ESQUERDA" );
    $obListaCalculosErro->commitDado();

    $obListaCalculosErro->show();
}

$obButtonRelatorio = new Button;
$obButtonRelatorio->setName  ( "Relatorio" );
$obButtonRelatorio->setValue ( "Relatório" );
$obButtonRelatorio->obEvento->setOnClick( "document.frm2.submit();");

$obButtonValidarCalculos = new Button;
$obButtonValidarCalculos->setName  ( "validar_calculos" );
$obButtonValidarCalculos->setValue ( "Validar Cálculos" );
$obButtonValidarCalculos->obEvento->setOnClick("mudaAcao('".$_REQUEST['inCodGrupo']."');");    //////Quero atribuir uma ação a este botão

$obButtonLancamento = new Button;
$obButtonLancamento->setName  ( "lancar_calculos" );
$obButtonLancamento->setValue ( "Lançar Cálculos" );
$obButtonLancamento->setTitle ( "Calculos com Erro não serão lançados");
$obButtonLancamento->obEvento->setOnClick( "lancarCalculos();");

$obHdnLancar = new Hidden;
$obHdnLancar->setName  ( "boLancar" );
$obHdnLancar->setValue ( TRUE  );

$obHdnGrupo = new Hidden;
$obHdnGrupo->setName  ( "inCodGrupo" );
$obHdnGrupo->setValue ( $_REQUEST['inCodGrupo'] );

$obForm->setName    ("frm2");
$obFormulario = new Formulario;
$obFormulario->addForm ($obForm);
$obFormulario->addHidden     ( $obHdnCaminho );

if ($stTipoCalculo == 'geral') {
    if ($_REQUEST["boSimular"]) {
        $obFormulario->defineBarra( array( $obButtonRelatorio , $obButtonValidarCalculos), "left", "" );
    } else {
        $obFormulario->defineBarra( array( $obButtonRelatorio , $obButtonLancamento), "left", "" );
    }
}else
if ( $rsCalculos->getNumLinhas() > 0 ) {
    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName  ( "stCtrl" );
    $obHdnCtrl->setValue ( $stCtrl  );

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName  ( "stAcao" );
    $obHdnAcao->setValue ( $stAcao  );

    $obHdnRelatorio = new Hidden;
    $obHdnRelatorio->setName  ( "stRelatorio" );
    $obHdnRelatorio->setValue ( true );

    $obFormulario->addHidden     ( $obHdnAcao               );
    $obFormulario->addHidden     ( $obHdnCtrl               );
    $obFormulario->addHidden     ( $obHdnRelatorio          );
    $obFormulario->addHidden     ( $obHdnLancar             );
    $obFormulario->addHidden     ( $obHdnGrupo              );

    $obRdbEmissaoNaoEmitir = new Radio;
    $obRdbEmissaoNaoEmitir->setTitle ( "Informe se deverá ou não ser emitido carnê de cobrança." );
    $obRdbEmissaoNaoEmitir->setRotulo   ( "Emissão de Carnês"                            );
    $obRdbEmissaoNaoEmitir->setName     ( "emissao_carnes"                               );
    $obRdbEmissaoNaoEmitir->setId       ( "emissao_carnes"                               );
    $obRdbEmissaoNaoEmitir->setLabel    ( "Não Emitir"                                   );
    $obRdbEmissaoNaoEmitir->setValue    ( "nao_emitir"                                   );
    $obRdbEmissaoNaoEmitir->setNull     ( false                                          );
    $obRdbEmissaoNaoEmitir->setChecked  ( true                                           );
    $obRdbEmissaoNaoEmitir->obEvento->setOnChange( "montaModeloCarne2();"  );

    $obRdbEmissaoLocal = new Radio;
    $obRdbEmissaoLocal->setRotulo   ( "Emissão de Carnês"                            );
    $obRdbEmissaoLocal->setName     ( "emissao_carnes"                               );
    $obRdbEmissaoLocal->setId       ( "emissao_carnes"                               );
    $obRdbEmissaoLocal->setLabel    ( "Impressão Local"                              );
    $obRdbEmissaoLocal->setValue    ( "local"                                         );
    $obRdbEmissaoLocal->setNull     ( false                                          );
    $obRdbEmissaoLocal->setChecked  ( false                                          );
    $obRdbEmissaoLocal->obEvento->setOnChange( "montaModeloCarne2();"  );

    $obRdbEmissaoGrafica = new Radio;
    $obRdbEmissaoGrafica->setRotulo   ( "Emissão de Carnês"                            );
    $obRdbEmissaoGrafica->setName     ( "emissao_carnes"                               );
    $obRdbEmissaoGrafica->setId       ( "emissao_carnes"                               );
    $obRdbEmissaoGrafica->setLabel    ( "Gráfica"                                      );
    $obRdbEmissaoGrafica->setValue    ( "grafica"                                      );
    $obRdbEmissaoGrafica->setNull     ( false                                          );
    $obRdbEmissaoGrafica->setChecked  ( false                                          );
    $obRdbEmissaoGrafica->obEvento->setOnChange( "montaModeloCarne2();"  );

    $obSpnModelo = new Span;
    $obSpnModelo->setId( "spnModelo");

    if ($tamanho_arquivo) {
        $obFormulario->agrupaComponentes( array($obRdbEmissaoNaoEmitir,$obRdbEmissaoLocal,$obRdbEmissaoGrafica));
        $obFormulario->addSpan       ( $obSpnModelo            );
        $obFormulario->defineBarra( array( $obButtonRelatorio , $obButtonLancamento), "left", "" );
    }
} else {
    if ($tamanho_arquivo) {
        $obFormulario->defineBarra( array( $obButtonRelatorio), "left", "" );
    }
}
$obFormulario->show();

?>
