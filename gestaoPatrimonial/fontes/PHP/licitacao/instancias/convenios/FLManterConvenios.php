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
    * Formulario de Convenio
    * Data de Criação   : 03/10/2006

    * @author Analista:
    * @author Desenvolvedor:  Lucas Teixeira Stephanou
    * @ignore

    $Id: FLManterConvenios.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    *Casos de uso: uc-03.05.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_COM_COMPONENTES."IPopUpObjeto.class.php"                                      );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php"                                      );

if ($_REQUEST['stAcao'] == 'consultar') {
    $stAcao = "consultar";
} elseif ($_REQUEST['stAcao'] == 'anular') {
    $stAcao = "anular";
} elseif ($_REQUEST['stAcao'] == 'rescindir') {
    $stAcao = "rescindir";
} else {
    $stAcao = "alterar";
}

if ( stristr( $_SERVER[ 'HTTP_REFERER' ] , 'menu' ) ) {
    Sessao::remove('filtro');
}

$arFiltro = Sessao::read('filtro');

$cod_uf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio());
//Define o nome dos arquivos PHP
$stPrograma    = "ManterConvenios";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormConsulta= "FMConsultaConvenios.php";
$pgFormAnular  = "FMAnularConvenios.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/ajax.php';
include_once( $pgOcul );

// limpar sessao de veiculos
Sessao::remove('boAlteracao');
Sessao::remove('nuValorAtual');
Sessao::remove('nuPercentualAtual');
Sessao::remove('rsVeiculos');
Sessao::remove('participantes');
//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );
$obForm->setTarget ( 'telaPrincipal');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setId     ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $request->get('stCtrl')  );

/* EXERCÍCIO DO CONVENIO*/
$obIntExercicio = new Inteiro;
$obIntExercicio->setName  ( "inExercicio"                                                                          );
$obIntExercicio->setRotulo( "Exercício do Convênio"                                                                );
$obIntExercicio->setTitle ( "Informe o exercício do convênio"                                                      );
$obIntExercicio->setNull  ( true                                                                                   );
$obIntExercicio->setValue ( ($arFiltro['inExercicio']) ? $arFiltro['inExercicio'] : Sessao::getExercicio() );

/* NUMERO DO CONVENIO*/
$obIntNumConvenio = new Inteiro;
$obIntNumConvenio->setName  ( "inNumConvenio" );
$obIntNumConvenio->setId    ( "inNumConvenio" );
$obIntNumConvenio->setRotulo( "Número do Convênio" );
$obIntNumConvenio->setTitle ( "Informe o número do convênio" );
$obIntNumConvenio->setNull  ( true  );
$obIntNumConvenio->setValue ( $arFiltro['inNumConvenio'] );

/* TIPO DE CONVENIO */
require_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoTipoConvenio.class.php");
$obTLicitacaoTipoConvenio = new TLicitacaoTipoConvenio;
$obTLicitacaoTipoConvenio->setDado('cod_uf_tipo_convenio', $cod_uf);
$obTLicitacaoTipoConvenio->recuperaPorChave ( $rsTiposConvenio );

$obCmbTiposConvenio = new Select;
$obCmbTiposConvenio->setTitle ( "Selecione o tipo de convênio" );
$obCmbTiposConvenio->setName ( "inCodTipoConvenio" );
$obCmbTiposConvenio->setId   ( "inCodTipoConvenio" );
$obCmbTiposConvenio->setRotulo ( "Tipo de Convênio" );
$obCmbTiposConvenio->addOption ( "", "Selecione" );
$obCmbTiposConvenio->setCampoId ( "cod_tipo_convenio" );
$obCmbTiposConvenio->setCampoDesc ( "descricao" );
$obCmbTiposConvenio->preencheCombo ( $rsTiposConvenio );
$obCmbTiposConvenio->setNull ( true  );
$obCmbTiposConvenio->setValue ( $arFiltro['inCodTipoConvenio']);

/* OBJETO */
$obObjeto = new IPopUpObjeto($obForm);
$obObjeto->setNull( true        );
$obObjeto->obCampoCod->setValue ( $arFiltro['stObjeto']);

/* CGM */
$obCgmParticipante =  new IPopUpCGMVinculado($obForm);
$obCgmParticipante->setTabelaVinculo ( 'licitacao.participante_certificacao' );
$obCgmParticipante->setCampoVinculo ( 'cgm_fornecedor' );
$obCgmParticipante->setNomeVinculo ( 'Participante' );
$obCgmParticipante->setRotulo("CGM");
$obCgmParticipante->setTitle("Selecione o CGM do participante");
$obCgmParticipante->setNull ( true );
$obCgmParticipante->setName   ( 'stNomCgmParticipante');
$obCgmParticipante->setId     ( 'stNomCgmParticipante');
$obCgmParticipante->obCampoCod->setName ( 'inCgmParticipante' );
$obCgmParticipante->obCampoCod->setId   ( 'inCgmParticipante' );
$obCgmParticipante->obCampoCod->setNull ( false               );
$obCgmParticipante->obCampoCod->setValue( $arFiltro['inCgmParticipante']);

$obBtnOk = new Ok();
$obBtnOk->setName   ( "btnOk" );
$obBtnOk->setId     ( "btnOk" );

$obBtnLimpar = new Limpar();
$obBtnLimpar->obEvento->setOnClick  ( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."', 'limpaFiltro');" );

$arBotoes = array ( $obBtnOk , $obBtnLimpar);

/* span*/
$obSpanResultado = new Span;
$obSpanResultado->setId ( 'spnResultado' );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->setAjuda     ( "UC-03.05.14" );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addTitulo    ( "Dados para o filtro");
$obFormulario->addComponente( $obIntExercicio );
$obFormulario->addComponente( $obIntNumConvenio );
$obFormulario->addComponente( $obCmbTiposConvenio );
$obFormulario->addComponente( $obObjeto );
$obFormulario->addComponente( $obCgmParticipante );
$obFormulario->defineBarra  ( $arBotoes, 'left', '' );
$obFormulario->addSpan      ( $obSpanResultado   );
$obFormulario->show();

if ( is_array( $arFiltro ) ) {
    echo "<script type=\"text/javascript\">             \r\n";
    if ($arFiltro['stObjeto']) {
        echo "   var e = document.getElementById('stObjeto').onchange;          \r\n";
        echo "   setTimeout(e,300);                                             \r\n";
    }
    if ($arFiltro['inCgmParticipante']) {
        echo "   var c = document.getElementById('inCgmParticipante').onchange; \r\n";
        echo "   setTimeout(c,400);                                             \r\n";
    }
    echo "   var d= document.getElementById('btnOk').onclick; \r\n";
    echo "   setTimeout(d,500);                                                           \r\n";
    echo "</script>                                                             \r\n";
}

Sessao::remove('filtro');
