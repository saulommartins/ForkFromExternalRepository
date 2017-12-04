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
    * Página de Formulário para informar os veiculos de publicacao que serao publicados no edital
    * Data de Criação   : 06/10/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Thiago La Delfa Cabelleira

    * @ignore

    * Casos de uso : UC-03.04.29

    $Id: FMManterNotaCompra.php 59612 2014-09-02 12:00:51Z gelson $
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_COM_COMPONENTES."IPopUpOrdemCompra.class.php" );

//Definições padrões do framework
$stPrograma = "ManterNotaCompra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( Sessao::read('filtro') ) {
    $stFiltro = '';
    foreach ( Sessao::read('filtro') as $stCampo => $stValor ) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}
$stLocation = $pgList."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'].$stFiltro;

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//Define o Hidden de ação (padrão no framework)
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o Hidden de controle (padrão no framework)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );

//Define o Hidden do ExercicioEmpenho
$obHdnExercicioEmpenho = new Hidden;
$obHdnExercicioEmpenho->setName( "hdnExercicioEmpenho" );
$obHdnExercicioEmpenho->setId( "hdnExercicioEmpenho" );

//Define o Hidden do Empenho
$obHdnEmpenho = new Hidden;
$obHdnEmpenho->setName( "hdnEmpenho" );
$obHdnEmpenho->setId( "hdnEmpenho" );

//Define o Hidden do Entidade
$obHdnEntidade = new Hidden;
$obHdnEntidade->setName( "hdnEntidade" );
$obHdnEntidade->setId( "hdnEntidade" );

//Define o Hidden do Exercicio
$obHdnExercicio = new Hidden;
$obHdnExercicio->setName( "hdnExercicio" );
$obHdnExercicio->setId( "hdnExercicio" );

//Define o Hidden do Fornecedor
$obHdnFornecedor = new Hidden;
$obHdnFornecedor->setName( "hdnFornecedor" );
$obHdnFornecedor->setId( "hdnFornecedor" );

//Define o Hidden do Numero do Fornecedor
$obHdnNumFornecedor = new Hidden;
$obHdnNumFornecedor->setName( "hdnNumFornecedor" );
$obHdnNumFornecedor->setId( "hdnNumFornecedor" );

if ($stAcao == 'incluir') {
  $obOrdemCompra = new IPopUpOrdemCompra( $obForm );
  $obOrdemCompra->obCampoCod->setDisabled ( false );
  $obOrdemCompra->obTxtEntidade->obEvento->setOnBlur("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodOrdemCompra='+inCodOrdemCompra.value+'&stExercicioOrdemCompra='+stExercicioOrdemCompra.value+'&inCodEntidade='+this.value,'exibeDadosNota');");

  //Numero da Nota Fiscal
  $obTxtNumNota = new TextBox;
  $obTxtNumNota->setName  ( "num_nota" );
  $obTxtNumNota->setId  ( "num_nota" );
  $obTxtNumNota->setRotulo( "Número da Nota Fiscal" );

  //Número da Série
  $obTxtNumSerie = new TextBox;
  $obTxtNumSerie->setName  ( "num_serie" );
  $obTxtNumSerie->setId  ( "num_serie" );
  $obTxtNumSerie->setRotulo( "Série" );
  $obTxtNumSerie->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodOrdemCompra='+inCodOrdemCompra.value+'&hdnExercicio='+hdnExercicio.value+'&hdnEntidade='+hdnEntidade.value+'&hdnExercicioEmpenho='+hdnExercicioEmpenho.value+'&hdnEmpenho='+hdnEmpenho.value+'&num_serie='+num_serie.value+'&num_nota='+num_nota.value+'&hdnNumFornecedor='+hdnNumFornecedor.value,'exibeListaItens');");

  //Spam dados nota
  $obSpnDadosNota = new Span();
  $obSpnDadosNota->setId( 'spnDadosNota' );

  //Lista Ítens
  $obSpnListaItens = new Span();
  $obSpnListaItens->setId( 'spnListaItens' );
}

if ($stAcao == 'consultar') {
 include_once(CAM_GP_COM_MAPEAMENTO."TComprasOrdemCompraNota.class.php");
 $obDadosNota = new TComprasOrdemCompraNota();
 $obDadosNota->setDado('num_nota',$_REQUEST['numNota']);
 $obDadosNota->setDado('cod_ordem',$_REQUEST['inCodOrdem']);
 $obDadosNota->setDado('cod_empenho',$_REQUEST['inCodEmpenho']);
 $obDadosNota->setDado('exercicio',$_REQUEST['exercicio']);
 $obDadosNota->setDado('cod_entidade',$_REQUEST['codEntidade']);
 $obDadosNota->recuperaDadosNota($rsDadosNota);

 $obLbOrdemCompra = new Label();
 $obLbOrdemCompra->setValue($rsDadosNota->getCampo('cod_ordem'));
 $obLbOrdemCompra->setRotulo('Número da Ordem de Compra');

 $obLbNf = new Label();
 $obLbNf->setValue($rsDadosNota->getCampo('num_nota'));
 $obLbNf->setRotulo('Número da Nota Fiscal');

 $obLbSerie = new Label();
 $obLbSerie->setValue($rsDadosNota->getCampo('num_serie'));
 $obLbSerie->setRotulo('Série');

 $obLbEmpenho = new Label();
 $obLbEmpenho->setValue($rsDadosNota->getCampo('cod_empenho'));
 $obLbEmpenho->setRotulo('Número do Empenho');

 $obLbFornecedor = new Label();
 $obLbFornecedor->setValue($rsDadosNota->getCampo('nom_cgm'));
 $obLbFornecedor->setRotulo('Fornecedor');

 $filtro['cod_ordem'] = $rsDadosNota->getCampo('cod_ordem');
 $filtro['exercicio'] = $rsDadosNota->getCampo('exercicio');
 $filtro['cod_entidade'] = $rsDadosNota->getCampo('cod_entidade');
 $filtro['exercicio_empenho'] = $rsDadosNota->getCampo('exercicio_empenho');
 $filtro['cod_empenho'] = $rsDadosNota->getCampo('cod_empenho');

  Sessao::write('filtro' , $filtro);

 $jsOnLoad = "executaFuncaoAjax('consultarNotaCompra')";

 //Lista Ítens
 $obSpnListaItens = new Span();
 $obSpnListaItens->setId( 'spnListaItens' );
}

//-----------------------------------

//Definição do formulário
$obFormulario = new Formulario;
//carrega a lista por padrao
$obFormulario->addForm      ( $obForm               );
//Define o caminho de ajuda do Caso de uso (padrão no Framework)
$obFormulario->setAjuda     ("UC-03.04.29"          );
$obFormulario->addHidden    ( $obHdnCtrl            );
$obFormulario->addHidden    ( $obHdnAcao            );
$obFormulario->addHidden    ( $obHdnExercicioEmpenho);
$obFormulario->addHidden    ( $obHdnEmpenho         );
$obFormulario->addHidden    ( $obHdnEntidade        );
$obFormulario->addHidden    ( $obHdnExercicio       );
$obFormulario->addHidden    ( $obHdnFornecedor      );
$obFormulario->addHidden    ( $obHdnNumFornecedor   );

$obFormulario->addTitulo    ( "Nota de Compra"      );
if ($stAcao == 'incluir') {
  $obOrdemCompra->geraFormulario($obFormulario);
  $obFormulario->addComponente( $obTxtNumNota         );
  $obFormulario->addComponente( $obTxtNumSerie        );
  $obFormulario->addSpan      ( $obSpnDadosNota       );
  $obFormulario->addSpan      ( $obSpnListaItens      );
}

if ($stAcao == 'consultar') {
  $obFormulario->addComponente    ( $obLbOrdemCompra        );
  $obFormulario->addComponente    ( $obLbNf        );
  $obFormulario->addComponente    ( $obLbSerie        );
  $obFormulario->addComponente    ( $obLbEmpenho        );
  $obFormulario->addComponente    ( $obLbFornecedor        );

  $obFormulario->addSpan      ( $obSpnListaItens      );

}
if ($stAcao == 'incluir') {
    $obFormulario->Ok();
} else {
    $obFormulario->Cancelar($stLocation);
}

$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
