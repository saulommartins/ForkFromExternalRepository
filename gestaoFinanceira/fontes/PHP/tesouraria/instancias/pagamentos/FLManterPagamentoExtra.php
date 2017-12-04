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
    * Página de Formulário para Filtro de Estorno de Pagamento de Despesas Extra Orçamentárias
    * Data de Criação   : 05/09/2006
    *
    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 31732 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.27

*/
/*
$Log$
Revision 1.5  2006/11/25 10:39:23  cleisson
Bug #7626#

Revision 1.4  2006/09/18 15:46:01  cako
implementação do uc-02.04.27

Revision 1.3  2006/09/18 11:07:02  cako
implementação do uc-02.04.27

Revision 1.2  2006/09/14 10:26:26  cako
implementação do uc-02.04.27

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CLA_IAPPLETTERMINAL );
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php");
include_once( CAM_GF_EMP_COMPONENTES."IPopUpCredor.class.php");
include_once( CAM_GF_ORC_COMPONENTES."IPopUpRecurso.class.php");
include_once( CAM_GF_CONT_COMPONENTES."IPopUpContaAnalitica.class.php");
include_once ( CAM_GF_EMP_MAPEAMENTO.'TEmpenhoConfiguracao.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPagamentoExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OCManterPagamentoExtraEstorno.php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
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
    $obTEmpenhoConfiguracao = new TEmpenhoConfiguracao;
    $obTEmpenhoConfiguracao->setDado( 'parametro', 'numero_empenho' );
    $obTEmpenhoConfiguracao->consultar ();
    $tipoNumeracao = $obTEmpenhoConfiguracao->getDado( 'valor' );

    $obForm = new Form;
    $obForm->setAction( $pgList );
    $obForm->setTarget( "telaPrincipal" );

    //sessao->link = array();

    //Define o objeto da aÃ§Ã£o stAcao
    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ( "stAcao" );
    $obHdnAcao->setValue( $stAcao );

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ( "stCtrl" );
    $obHdnCtrl->setValue( $stCtrl );

    //Define Objeto TextBox para Código de Barras
    $obTxtCodBarras = new TextBox();
    $obTxtCodBarras->setTitle     ( 'Informe o Código de Barras, posicionando o leitor sobre o mesmo.' );
    $obTxtCodBarras->setRotulo    ( 'Código de Barras'                                    );
    $obTxtCodBarras->setName      ( 'inCodBarras'                                       );
    $obTxtCodBarras->setId        ( 'inCodBarras'                                       );
    $obTxtCodBarras->setInteiro   ( true                                                  );
    $obTxtCodBarras->setNull      ( true                                                  );
    $obTxtCodBarras->setSize      ( 23                                                    );
    $obTxtCodBarras->setMaxLength ( 20                                                    );
    $obTxtCodBarras->obEvento->setOnChange(" if (this.value.length == 20) ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodBarras='+this.value, 'verificaCodBarras');\n
                                             if (this.value.length < 20) { \n
                                                executaFuncaoAjax('limparCampos');\n
                                                alertaAviso('@Código de barras inválido.','form','erro','".Sessao::getId()."'); \n } ");

    //Define objeto textbox para o Recibo
    $obTxtCodRecibo = new TextBox();
    $obTxtCodRecibo->setTitle       ( 'Informe o número do Recibo.');
    $obTxtCodRecibo->setRotulo      ( 'Nr. Recibo');
    $obTxtCodRecibo->setName        ( 'inCodRecibo' );
    $obTxtCodRecibo->setId          ( 'inCodRecibo' );
    $obTxtCodRecibo->setInteiro     ( true );
    $obTxtCodRecibo->setSize        ( 15 );
    $obTxtCodRecibo->setMaxLength   ( 10 );
    $obTxtCodRecibo->obEvento->setOnChange(" if (this.value != '') montaParametrosGET('buscaDadosRecibo', 'inCodRecibo, inCodEntidade');
                                             if (this.value == '') executaFuncaoAjax('limparCampos'); ");
    // Define Objeto Select para Entidade
    $obIEntidade = new ITextBoxSelectEntidadeUsuario();
    $obIEntidade->obTextBox->obEvento->setOnChange("frm.inCodBoletim.value = ''; frm.stDtBoletim.value = ''; montaParametrosGET('limparCampos'); ");

    $obIEntidade->obSelect->obEvento->setOnChange(
        "frm.inCodBoletim.value = '';
        frm.stDtBoletim.value = '';
        ajaxJavaScript('$pgOcul?".Sessao::getId()."&inCodEntidade='+
                        document.getElementById('inCodEntidade').value,'atualizaStatus'
        );
        montaParametrosGET('limparCampos');"
    );

    $obTxtCodBoletim = new TextBox;
    $obTxtCodBoletim->setRotulo ( 'Número Boletim' );
    $obTxtCodBoletim->setName( "inCodBoletim" );
    $obTxtCodBoletim->setValue ( $inCodBoletim );
    $obTxtCodBoletim->setTitle ( "Informe o nr. do Boletim" );
    $obTxtCodBoletim->setId    ( "inCodBoletim" );
    $obTxtCodBoletim->obEvento->setOnChange(" ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodBoletim='+this.value+'&inCodEntidade='+document.getElementById('inCodEntidade').value,'buscaBoletim');");

    $obTxtdtBoletim = new Data;
    $obTxtdtBoletim->setRotulo ( 'Data do Boletim ');
    $obTxtdtBoletim->setName ( "stDtBoletim" );
    $obTxtdtBoletim->setValue ( $stDtBoletim );
    $obTxtdtBoletim->setTitle ( "Informe a data do Boletim" );
    $obTxtdtBoletim->setId   ( "stDtBoletim" );
    $obTxtdtBoletim->obEvento->setOnChange(" ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stDtBoletim='+this.value+'&inCodEntidade='+document.getElementById('inCodEntidade').value,'buscaBoletim');");

    // Define objeto BuscaInner para cgm
    $obICredor = new IPopUpCredor($obForm);
    $obICredor->obCampoCod->setId ( "inCodCredor" );
    $obICredor->setNull ( true );

    /*// Define objeto BuscaInner para o Recurso
    $obIRecurso = new IPopUpRecurso();
    $obIRecurso->obCampoCod->setId( "inCodRecurso" ); */

    include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
    $obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
    $obIMontaRecursoDestinacao->setFiltro ( true );

    // Define Objeto BuscaInner da conta de despesa
    $obBscContaDebito = new IPopUpContaAnalitica();
    $obBscContaDebito->setRotulo                      ( "Conta de Despesa" );
    $obBscContaDebito->setTitle                       ( "Informe a conta de despesa extra-orçamentária vinculada a este recibo." );
    $obBscContaDebito->setId                          ( "stNomContaDebito" );
    $obBscContaDebito->setNull                        (  true             );
    $obBscContaDebito->obCampoCod->setName            ( "inCodPlanoDebito" );
    $obBscContaDebito->obCampoCod->setId              ( "inCodPlanoDebito" );
    $obBscContaDebito->obImagem->setId                ( "imgPlanoDebito"   );
    $obBscContaDebito->setTipoBusca                   ( "tes_pagamento_extra_despesa" );

    // Define Objeto BuscaInner da conta para caixa/banco
    $obBscContaCredito = new IPopUpContaAnalitica(  $obIEntidade->obSelect  );
    $obBscContaCredito->setRotulo                      ( "Conta Caixa/Banco"    );
    $obBscContaCredito->setTitle                       ( "Informe a conta Caixa/Banco onde foi efetuado o pagamento da despesa extra." );
    $obBscContaCredito->setId                          ( "stNomContaCredito" );
    $obBscContaCredito->setNull                        (  true              );
    $obBscContaCredito->obCampoCod->setName            ( "inCodPlanoCredito" );
    $obBscContaCredito->obCampoCod->setId              ( "inCodPlanoCredito" );
    $obBscContaCredito->obImagem->setId                ( "imgPlanoCredito"   );
    $obBscContaCredito->setTipoBusca                   ( "tes_pagamento_extra_caixa_banco"    );

    //****************************************//
    //Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );

    $obIAppletTerminal = new IAppletTerminal( $obForm );

    $obFormulario->addTitulo    ( 'Dados para Estorno de Pagamentos');
    $obFormulario->addHidden    ( $obHdnAcao                    );
    $obFormulario->addHidden    ( $obHdnCtrl                    );
    $obFormulario->addHidden    ( $obIAppletTerminal            );
    $obFormulario->addComponente( $obTxtCodBarras               );
    $obFormulario->addComponente( $obIEntidade                  );
    $obFormulario->addComponente( $obTxtCodRecibo               );
    $obFormulario->addComponente( $obTxtdtBoletim               );
    $obFormulario->addComponente( $obTxtCodBoletim              );
    $obFormulario->addComponente( $obICredor                    );
    //$obFormulario->addComponente( $obIRecurso                   );
    $obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
    $obFormulario->addComponente( $obBscContaDebito             );
    $obFormulario->addComponente( $obBscContaCredito            );

    $obOk  = new Ok;
    $obOk->setId ("Ok");

    $obLimpar = new Button;
    $obLimpar->setValue( "Limpar" );
    $obLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparCampos');");

    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
