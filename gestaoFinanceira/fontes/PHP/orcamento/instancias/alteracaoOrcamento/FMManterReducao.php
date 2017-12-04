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
    * Página de Formulário de Suplementacao
    * Data de Criação   : 11/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 30813 $
    $Name$
    $Author: melo $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php");

$stPrograma = "ManterReducao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true') {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    $obRegra = new ROrcamentoSuplementacao;
    $obRegra->obRNorma->setExercicio( Sessao::getExercicio() );
    $obRegra->obRNorma->listarDecreto( $rsNorma );

    $obRegra->addDespesaReducao();
    $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade, "cod_entidade" );

    $stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

    // TIPO DE CRÉDITO
    if ($stAcao == 'Suplementa') {
        $stTituloForm = "Dados para Crédito Suplementar por Redução";
        $inCodTipo = 1;
    } elseif ($stAcao == 'Especial') {
        $stTituloForm = "Dados para Crédito Especial por Redução";
        $inCodTipo = 6;
    }
    Sessao::remove('arRedutoras');
    Sessao::remove('arSuplementada');
    //sessao->transf3['arRedutoras']    = array();
    //sessao->transf3['arSuplementada'] = array ();

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName( "stAcao" );
    $obHdnAcao->setValue( $stAcao );

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName( "stCtrl" );
    $obHdnCtrl->setValue( "" );

    $obHdnTipo = new Hidden;
    $obHdnTipo->setName( "inCodTipo" );
    $obHdnTipo->setValue( $inCodTipo );

    // Define Objeto Select para Entidade
    $obCmbEntidade = new Select;
    $obCmbEntidade->setRotulo    ( "Entidade" );
    $obCmbEntidade->setTitle     ( "Selecione a entidade." );
    $obCmbEntidade->setName      ( "inCodEntidade"  );
    $obCmbEntidade->setId        ( "inCodEntidade"  );
    $obCmbEntidade->setValue     ( $inCodEntidade   );
    if ($rsEntidade->getNumLinhas()>1) {
        $obCmbEntidade->addOption    ( "", "Selecione" );
        $obCmbEntidade->obEvento->setOnChange('Limpar();');
    }
    $obCmbEntidade->setCampoId   ( "cod_entidade"   );
    $obCmbEntidade->setCampoDesc ( "[cod_entidade] - [nom_cgm]" );
    $obCmbEntidade->setStyle     ( "width: 520"  );
    $obCmbEntidade->preencheCombo( $rsEntidade   );
    $obCmbEntidade->setNull      ( false );

    // Define Objeto BuscaInner para Norma
    $obBscNorma = new BuscaInner;
    $obBscNorma->setRotulo ( "Lei/Decreto"   );
    $obBscNorma->setTitle  ( "Selecione uma lei ou decreto." );
    $obBscNorma->setNulL   ( false                    );
    $obBscNorma->setId     ( "stNomTipoNorma"         );
    $obBscNorma->setValue  ( $stNomTipoNorma          );
    $obBscNorma->obCampoCod->setName     ( "inCodNorma" );
    $obBscNorma->obCampoCod->setId       ( "inCodNorma" );
    $obBscNorma->obCampoCod->setSize     ( 10           );
    $obBscNorma->obCampoCod->setMaxLength( 7            );
    $obBscNorma->obCampoCod->setValue    ( $inCodNorma  );
    $obBscNorma->obCampoCod->setAlign    ( "left"       );
    $obBscNorma->obCampoCod->obEvento->setOnBlur("buscaDado('buscaNorma');");
    $obBscNorma->setFuncaoBusca("abrePopUp('".CAM_GA_NORMAS_POPUPS."normas/FLNorma.php','frm','inCodNorma','stNomTipoNorma','','".Sessao::getId()."','800','550');");

    $obTxtDataNorma = new Data;
    $obTxtDataNorma->setRotulo        ( "Data"   );
    $obTxtDataNorma->setTitle         ( "Informe a data."   );
    $obTxtDataNorma->setName          ( "stData" );
    $obTxtDataNorma->setId            ( "stData" );
    $obTxtDataNorma->setValue         ( ''       );
    //$obTxtDataNorma->setValue         ( date('d/m/Y') );
    $obTxtDataNorma->setNull          ( false    );

    // Define Objeto TextArea para Motivo
    $obTxtMotivo = new TextArea;
    $obTxtMotivo->setName   ( "stMotivo" );
    $obTxtMotivo->setId     ( "stMotivo" );
    $obTxtMotivo->setValue  ( $stMotivo  );
    $obTxtMotivo->setRotulo ( "Motivo" );
    $obTxtMotivo->setTitle  ( "Informe o motivo." );
    $obTxtMotivo->setNull   ( true );
    $obTxtMotivo->setRows   ( 2 );
    $obTxtMotivo->setCols   ( 100 );

    // Define Objeto Numeric para Total
    $obTxtTotal = new Numerico;
    $obTxtTotal->setName     ( "nuVlTotal" );
    $obTxtTotal->setId       ( "nuVlTotal" );
    $obTxtTotal->setValue    ( $nuVltotal  );
    $obTxtTotal->setRotulo   ( "Valor Total"   );
    $obTxtTotal->setTitle    ( "Informe o valor total." );
    $obTxtTotal->setDecimais ( 2 );
    $obTxtTotal->setNegativo ( false );
    $obTxtTotal->setNull     ( false );
    $obTxtTotal->setSize     ( 23 );
    $obTxtTotal->setMaxLength( 23 );
    $obTxtTotal->setMinValue ( 1 );
    $obTxtTotal->obEvento->setOnChange("buscaDado('validaValorLimiteSuplementacao');");

    // Define Objeto BuscaInner para Dotacao Redutoras
    $obBscDespesaReducao = new BuscaInner;
    $obBscDespesaReducao->setRotulo ( "Dotação Orçamentária"   );
    $obBscDespesaReducao->setTitle  ( "Informe a dotação orçamentária." );
    $obBscDespesaReducao->setNulL   ( true                     );
    $obBscDespesaReducao->setId     ( "stNomDotacaoRedutora"   );
    $obBscDespesaReducao->setValue  ( $stNomDotacaoRedutora    );
    $obBscDespesaReducao->obCampoCod->setName ( "inCodDotacaoReducao" );
    $obBscDespesaReducao->obCampoCod->setId   ( "inCodDotacaoReducao" );
    $obBscDespesaReducao->obCampoCod->setSize ( 10 );
    $obBscDespesaReducao->obCampoCod->setMaxLength( 5 );
    $obBscDespesaReducao->obCampoCod->setValue ( $inCodDotacaoReducao );
    $obBscDespesaReducao->obCampoCod->setAlign ("left");
    $obBscDespesaReducao->obCampoCod->obEvento->setOnBlur("buscaDado('buscaDespesaReducao');");
    $obBscDespesaReducao->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDotacaoReducao','stNomDotacaoRedutora','&inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");

    // Define Objeto Numeric para Total
    $obTxtTotalReducao = new Numerico;
    $obTxtTotalReducao->setName     ( "nuVlDotacaoRedutora" );
    $obTxtTotalReducao->setId       ( "nuVlDotacaoRedutora" );
    $obTxtTotalReducao->setValue    ( $nuVlDotacaoRedutora  );
    $obTxtTotalReducao->setRotulo   ( "Valor"   );
    $obTxtTotalReducao->setTitle    ( "Informe o valor." );
    $obTxtTotalReducao->setDecimais ( 2 );
    $obTxtTotalReducao->setNegativo ( false );
    $obTxtTotalReducao->setNull     ( true );
    $obTxtTotalReducao->setSize     ( 23 );
    $obTxtTotalReducao->setMaxLength( 23 );

    // Define Objeto Button para  Incluir Item
    $obBtnIncluirReducao = new Button;
    $obBtnIncluirReducao->setValue( "Incluir" );
    $obBtnIncluirReducao->obEvento->setOnClick( "incluirDotacaoRedutora();" );

    // Define Objeto Button para Limpar
    $obBtnLimparReducao = new Button;
    $obBtnLimparReducao->setValue( "Limpar" );
    $obBtnLimparReducao->obEvento->setOnClick( "limparRedutora();" );

    $obSpnRegistroReducoes = new Span;
    $obSpnRegistroReducoes->setId('spnReducoes');

    //----------------------------------------------------------
    // Define Objeto BuscaInner para Dotacao Suplementada
    $obBscDespesaSuplementada = new BuscaInner;
    $obBscDespesaSuplementada->setRotulo ( "Dotação Orçamentária"   );
    $obBscDespesaSuplementada->setTitle  ( "Informe a dotação orçamentária." );
    $obBscDespesaSuplementada->setNulL   ( true                     );
    $obBscDespesaSuplementada->setId     ( "stNomDotacaoSuplementada"   );
    $obBscDespesaSuplementada->setValue  ( $stNomDotacaoSuplementada    );
    $obBscDespesaSuplementada->obCampoCod->setName ( "inCodDotacaoSuplementada" );
    $obBscDespesaSuplementada->obCampoCod->setSize ( 10 );
    $obBscDespesaSuplementada->obCampoCod->setMaxLength( 5 );
    $obBscDespesaSuplementada->obCampoCod->setValue ( $inCodDotacaoOrcamentaria );
    $obBscDespesaSuplementada->obCampoCod->setAlign ("left");
    if ($stAcao == 'Especial') {
        $obBscDespesaSuplementada->obCampoCod->obEvento->setOnBlur("buscaDado('buscaDespesaSuplementadaEspecial');");
        $obBscDespesaSuplementada->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDotacaoSuplementada','stNomDotacaoSuplementada','alteracaoOrcamento&inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");
    } else {
        $obBscDespesaSuplementada->obCampoCod->obEvento->setOnBlur("buscaDado('buscaDespesaSuplementada');");
        $obBscDespesaSuplementada->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDotacaoSuplementada','stNomDotacaoSuplementada','&inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");
    }

    // Define Objeto Numeric para Total
    $obTxtTotalSuplementada = new Numerico;
    $obTxtTotalSuplementada->setName     ( "nuVlDotacaoSuplementada" );
    $obTxtTotalSuplementada->setId       ( "nuVlDotacaoSuplementada" );
    $obTxtTotalSuplementada->setValue    ( $nuVlDotacaoSuplementada  );
    $obTxtTotalSuplementada->setRotulo   ( "Valor"   );
    $obTxtTotalSuplementada->setTitle    ( "Informe o valor." );
    $obTxtTotalSuplementada->setDecimais ( 2 );
    $obTxtTotalSuplementada->setNegativo ( false );
    $obTxtTotalSuplementada->setNull     ( true );
    $obTxtTotalSuplementada->setSize     ( 23 );
    $obTxtTotalSuplementada->setMaxLength( 23 );

    // Define Objeto Button para  Incluir Item
    $obBtnIncluirSuplementada = new Button;
    $obBtnIncluirSuplementada->setValue( "Incluir" );
    $obBtnIncluirSuplementada->obEvento->setOnClick( "incluirDotacaoSuplementada();" );

    // Define Objeto Button para Limpar
    $obBtnLimparSuplementada = new Button;
    $obBtnLimparSuplementada->setValue( "Limpar" );
    $obBtnLimparSuplementada->obEvento->setOnClick( "limparSuplementada();" );

    $obSpnRegistroSuplementada = new Span;
    $obSpnRegistroSuplementada->setId('spnSuplementada');

    $obOk = new Ok;
    $obLimpar = new Limpar();
    $obLimpar->obEvento->setOnClick( "Limpar();" );

    //DEFINICAO DOS COMPONENTES
    $obForm = new Form;
    $obForm->setAction                  ( $pgProc );
    $obForm->setTarget                  ( "oculto" );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->setAjuda             ( "UC-02.01.07" );
    $obFormulario->addForm              ( $obForm    );
    $obFormulario->addHidden            ( $obHdnAcao );
    $obFormulario->addHidden            ( $obHdnCtrl );
    $obFormulario->addHidden            ( $obHdnTipo );

    $obFormulario->addTitulo            ( $stTituloForm );

    $obFormulario->addComponente        ( $obCmbEntidade   );
    $obFormulario->addComponente        ( $obBscNorma      );
    $obFormulario->addComponente        ( $obTxtDataNorma  );
    $obFormulario->addComponente        ( $obTxtMotivo     );
    $obFormulario->addComponente        ( $obTxtTotal      );

    $obFormulario->addTitulo            ( "Dotações Redutoras"    );
    $obFormulario->addComponente        ( $obBscDespesaReducao    );
    $obFormulario->addComponente        ( $obTxtTotalReducao      );
    $obFormulario->agrupaComponentes    ( array( $obBtnIncluirReducao, $obBtnLimparReducao ) );
    $obFormulario->addSpan              ( $obSpnRegistroReducoes  );

    $obFormulario->addTitulo            ( "Dotações Suplementadas"   );
    $obFormulario->addComponente        ( $obBscDespesaSuplementada  );
    $obFormulario->addComponente        ( $obTxtTotalSuplementada    );
    $obFormulario->agrupaComponentes    ( array( $obBtnIncluirSuplementada, $obBtnLimparSuplementada ) );
    $obFormulario->addSpan              ( $obSpnRegistroSuplementada );

    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );

    $obFormulario->show                 ();

    include_once( $pgJs );
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>