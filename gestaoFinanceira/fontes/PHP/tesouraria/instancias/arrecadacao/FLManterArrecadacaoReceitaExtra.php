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
    * Página de Formulário para Filtro de Estorno de Arrecadação Extra Orçamentárias
    * Data de Criação   : 14/09/2006
    *
    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Id: FLManterArrecadacaoReceitaExtra.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 30691 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.26

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CLA_IAPPLETTERMINAL );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
include_once( CAM_GF_EMP_COMPONENTES."IPopUpCredor.class.php");
include_once( CAM_GF_ORC_COMPONENTES."IPopUpRecurso.class.php");
include_once( CAM_GF_CONT_COMPONENTES."IPopUpContaAnalitica.class.php");
include_once ( CAM_GF_EMP_MAPEAMENTO.'TEmpenhoConfiguracao.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoReceitaExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OCManterArrecadacaoReceitaExtraEstorno.php";
$pgJs   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( $request->get('stAcao') ) {
    $stAcao = "alterar";
}

$obTEmpenhoConfiguracao = new TEmpenhoConfiguracao;
$obTEmpenhoConfiguracao->setDado( 'parametro', 'numero_empenho' );
$obTEmpenhoConfiguracao->consultar ();
$tipoNumeracao = $obTEmpenhoConfiguracao->getDado( 'valor' );

$obTOrcamentoEntidade = new TOrcamentoEntidade;
$obTOrcamentoEntidade->setDado('exercicio', Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade );
$inCodEntidade = "";

if ($rsEntidade->getNumLinhas() == 1) {
    $inCodEntidade = $rsEntidade->getCampo('cod_entidade');
}

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $request->get('stCtrl') );

//Define Objeto TextBox para Código de Barras
$obTxtCodBarras = new TextBox();
$obTxtCodBarras->setTitle     ( 'Informe o Código de Barras, posicionando o leitor sobre o mesmo.' );
$obTxtCodBarras->setRotulo    ( 'Código de Barras'                                    );
$obTxtCodBarras->setName      ( 'inCodBarras'                                       );
$obTxtCodBarras->setId        ( 'inCodBarras'                                       );
$obTxtCodBarras->setInteiro   ( true                                                  );
$obTxtCodBarras->setNull      ( true                                                  );
$obTxtCodBarras->setSize      ( 23                                                    );
$obTxtCodBarras->setMaxLength ( 19                                                    );
$obTxtCodBarras->obEvento->setOnChange(" if (this.value.length == 19) ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodBarras='+this.value, 'verificaCodBarras');\n
                                         if (this.value.length < 19) { \n
                                            executaFuncaoAjax('limparCampos');\n
                                            alertaAviso('@Código de barras inválido.','form','erro','".Sessao::getId()."'); \n } ");

//Define objeto textbox para o Recibo
$obTxtCodRecibo = new TextBox();
$obTxtCodRecibo->setTitle       ( 'Informe o número do recibo de arrecadação Extra.');
$obTxtCodRecibo->setRotulo      ( 'Nr. Recibo');
$obTxtCodRecibo->setName        ( 'inCodRecibo' );
$obTxtCodRecibo->setId          ( 'inCodRecibo' );
$obTxtCodRecibo->setInteiro     ( true );
$obTxtCodRecibo->setSize        ( 15 );
$obTxtCodRecibo->setMaxLength   ( 10 );
$obTxtCodRecibo->obEvento->setOnChange("
if ((this.value != '') && (jq('#inCodEntidade').val() != '')) {
    montaParametrosGET('buscaDadosRecibo', 'inCodRecibo, inCodEntidade');
} else {
    executaFuncaoAjax('limparCampos');
    jq('#inCodRecibo').val('');
}");

$obTxtCodEntidade = new TextBox;
$obTxtCodEntidade->setName('inCodEntidade');
$obTxtCodEntidade->setId  ('inCodEntidade');
$obTxtCodEntidade->setRotulo ('Entidade');
$obTxtCodEntidade->setTitle  ('Selecione a entidade.');
$obTxtCodEntidade->setInteiro(true);
$obTxtCodEntidade->setValue  ( $inCodEntidade );

$obCmbNomEntidade = new Select;
$obCmbNomEntidade->setName ('stNomEntidade');
$obCmbNomEntidade->setId   ('stNomEntidade');
$obCmbNomEntidade->setCampoId   ('cod_entidade');
$obCmbNomEntidade->setCampoDesc ('nom_cgm');
$obCmbNomEntidade->setStyle     ('width: 520');
$obCmbNomEntidade->addOption    ('', 'Selecione');
$obCmbNomEntidade->preencheCombo($rsEntidade);
$obCmbNomEntidade->setNull      (false);
$obCmbNomEntidade->setValue($inCodEntidade);

$obTxtCodBoletim = new TextBox;
$obTxtCodBoletim->setRotulo ( 'Número Boletim' );
$obTxtCodBoletim->setName( "inCodBoletim" );
$obTxtCodBoletim->setTitle ( "Informe o nr. do Boletim" );
$obTxtCodBoletim->setId    ( "inCodBoletim" );
$obTxtCodBoletim->obEvento->setOnChange(" ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodBoletim='+this.value+'&inCodEntidade='+document.getElementById('inCodEntidade').value,'buscaBoletim');");

$obTxtdtBoletim = new Data;
$obTxtdtBoletim->setRotulo ( 'Data do Boletim ');
$obTxtdtBoletim->setName ( "stDtBoletim" );
$obTxtdtBoletim->setTitle ( "Informe a data do Boletim" );
$obTxtdtBoletim->setId   ( "stDtBoletim" );
$obTxtdtBoletim->obEvento->setOnChange(" ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stDtBoletim='+this.value+'&inCodEntidade='+document.getElementById('inCodEntidade').value,'buscaBoletim');");

// Define objeto BuscaInner para cgm
$obICredor = new IPopUpCredor($obForm);
$obICredor->obCampoCod->setId ( "inCodCredor" );
$obICredor->setNull ( true );

// Define objeto BuscaInner para o Recurso

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

// Define Objeto BuscaInner da conta de receita
$obBscContaCredito = new IPopUpContaAnalitica();
$obBscContaCredito->setRotulo                      ( "Conta de Receita" );
$obBscContaCredito->setTitle                       ( "Informe a conta de receita extra-orçamentária vinculada a este recibo." );
$obBscContaCredito->setId                          ( "stNomContaCredito" );
$obBscContaCredito->setNull                        (  true      );
$obBscContaCredito->obCampoCod->setName            ( "inCodPlanoCredito" );
$obBscContaCredito->obCampoCod->setId              ( "inCodPlanoCredito" );
$obBscContaCredito->obImagem->setId                ( "imgPlanoCredito"   );
$obBscContaCredito->setTipoBusca                   ( "tes_arrecadacao_extra_receita" );

// Define Objeto BuscaInner da conta para caixa/banco
$obBscContaDebito = new IPopUpContaAnalitica(  $obCmbNomEntidade  );
$obBscContaDebito->setRotulo                      ( "Conta Caixa/Banco"    );
$obBscContaDebito->setTitle                       ( "Informe a conta Caixa/Banco onde foi efetuado a arrecadação desta receita extra." );
$obBscContaDebito->setId                          ( "stNomContaDebito" );
$obBscContaDebito->setNull                        (  true       );
$obBscContaDebito->obCampoCod->setName            ( "inCodPlanoDebito" );
$obBscContaDebito->obCampoCod->setId              ( "inCodPlanoDebito" );
$obBscContaDebito->obImagem->setId                ( "imgPlanoDebito"   );
$obBscContaDebito->setTipoBusca                   ( "tes_arrecadacao_extra_caixa_banco"    );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obIAppletTerminal = new IAppletTerminal( $obForm );

$obFormulario->addTitulo    ( 'Dados para Estorno de Arrecadação Extra');
$obFormulario->addHidden    ( $obHdnAcao                    );
$obFormulario->addHidden    ( $obHdnCtrl                    );
$obFormulario->addHidden    ( $obIAppletTerminal            );
$obFormulario->addComponente( $obTxtCodBarras               );
$obFormulario->addComponenteComposto($obTxtCodEntidade, $obCmbNomEntidade);
$obFormulario->addComponente( $obTxtCodRecibo               );
$obFormulario->addComponente( $obTxtdtBoletim               );
$obFormulario->addComponente( $obTxtCodBoletim              );
$obFormulario->addComponente( $obICredor                    );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario  );
$obFormulario->addComponente( $obBscContaCredito            );
$obFormulario->addComponente( $obBscContaDebito             );

$obOk  = new Ok;
$obOk->setId ("Ok");

$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparCampos');");

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
