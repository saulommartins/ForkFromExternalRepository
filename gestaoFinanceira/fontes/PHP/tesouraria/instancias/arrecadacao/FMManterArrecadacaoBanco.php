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
/*
   * Página de Formulário para Arrecadação via Banco
   * Data de Criação: 01/03/2007

   * @author Analista: Gelson W. Gonçalves
   * @author Desenvolvedor: Rodrigo S. Rodrigues

   * @package URBEM

   * @subpackage

   * $Id: FMManterArrecadacaoBanco.php 59612 2014-09-02 12:00:51Z gelson $
   *
   * Casos de uso: uc-02.04.33

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CLA_IAPPLETTERMINAL                                                                   );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"                                     );

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoBanco";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

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
  include_once( $pgJs );

  $obRTesourariaBoletim = new RTesourariaBoletim();
  $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
  $obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.Sessao::getExercicio() ) );
  $obRTesourariaBoletim->addArrecadacao();
  $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
  $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm'));
  $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
  $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listarUsuariosEntidade( $rsBoletim );

  $arFiltro = Sessao::read('filtro');

  if ( !count($arFiltro) > 0 ) {
      Sessao::write('filtro',$_REQUEST);
  } else {
      $_REQUEST = $arFiltro;
  }

  $obForm = new Form;
  $obForm->setAction ( $pgProc );
  $obForm->setTarget ( "oculto" );

  $obHdnAcao = new Hidden;
  $obHdnAcao->setName( "stAcao" );
  $obHdnAcao->setId( "stAcao" );
  $obHdnAcao->setValue( $stAcao );

  $obHdnCtrl = new Hidden;
  $obHdnCtrl->setName( "stCtrl" );
  $obHdnCtrl->setValue( "" );

  $obHdnCodBoletim = new Hidden();
  $obHdnCodBoletim->setName( 'inCodBoletim' );
  $obHdnCodBoletim->setValue( $inCodBoletim );

  $obHdnDtBoletim = new Hidden();
  $obHdnDtBoletim->setName( 'stDtBoletim' );
  $obHdnDtBoletim->setValue( $stDtBoletim );

  $obHdnEval = new HiddenEval();
  $obHdnEval->setName( "stEval" );
  $obHdnEval->setValue( $stEval );

  $obApplet = new IAppletTerminal( $obForm );

  // Define Objeto Select para Entidade
  $obCmbEntidade = new Select();
  $obCmbEntidade->setRotulo    ( "Entidade"                 );
  $obCmbEntidade->setName      ( "inCodEntidade"            );
  $obCmbEntidade->setId        ( "inCodEntidade"            );
  $obCmbEntidade->setTitle     ( "Selecione a Entidade"     );
  $obCmbEntidade->setCampoId   ( "cod_entidade"             );
  $obCmbEntidade->setCampoDesc ( "nom_cgm"                  );
  $obCmbEntidade->setNull      ( false                      );
  if ( $rsEntidade->getNumLinhas() > 1 ) {
          $obCmbEntidade->addOption            ( "", "Selecione"      );
          $obCmbEntidade->obEvento->setOnChange("montaParametrosGET('buscaBoletim','inCodEntidade');");
  } else {
      $jsSL = "montaParametrosGET('buscaBoletim','inCodEntidade');";
  }
  $obCmbEntidade->preencheCombo    ( $rsEntidade            );

  $obSpanBoletim = new Span;
  $obSpanBoletim->setId ( 'spnBoletim' );

  $obSpanLote = new Span;
  $obSpanLote->setId ('spnLote');

  //DEFINICAO DO FORMULARIO
  $obFormulario = new Formulario;
  $obFormulario->addForm       ( $obForm             );
  $obFormulario->addHidden     ( $obHdnAcao          );
  $obFormulario->addHidden     ( $obHdnCtrl          );
  $obFormulario->addHidden     ( $obHdnCodBoletim    );
  $obFormulario->addHidden     ( $obHdnDtBoletim     );
  $obFormulario->addHidden     ( $obApplet           );
  $obFormulario->addTitulo     ( "Arrecadação via Banco" );
  $obFormulario->addComponente ( $obCmbEntidade      );
  $obFormulario->addSpan       ( $obSpanBoletim      );
  $obFormulario->addSpan 		 ( $obSpanLote		   );

  $obFormulario->Ok();

  $obFormulario->show();
  if ($jsSL) {
    $jsOnLoad = $jsSL;
  }
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
