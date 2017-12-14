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
    * Página filtro para Relatório Definível de Servidor
    * Data de Criação   : 02/03/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30860 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-03-10 11:58:23 -0300 (Seg, 10 Mar 2008) $

    * Casos de uso: uc-04.04.48
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoFolhaSituacao.class.php';
include_once CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoPeriodoMovimentacao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'DefinivelServidor';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';
$jsOnload   = "montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');";

Sessao::write('arCampos', array());
Sessao::write('pontos', 0);

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('telaPrincipal');

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue($stCtrl);

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName ('stCaminho');
$obHdnCaminho->setValue(CAM_GRH_PES_INSTANCIAS.'relatorio/PRDefinivelServidor.php');

$obHdnTipoFiltroExtra = new hiddenEval();
$obHdnTipoFiltroExtra->setName ('hdnTipoFiltroExtra');
$obHdnTipoFiltroExtra->setValue('eval(document.frm.hdnTipoFiltro.value);');

$obSpnCampos = new Span();
$obSpnCampos->setId('spnCampos');

$obSpnBotoes = new Span();
$obSpnBotoes->setId('spnBotoes');

$obBtnLimparCampos = new Limpar();
$obBtnLimparCampos->obEvento->setOnClick("executaFuncaoAjax('limparCampos'); $jsOnload");

$obBtnOK = new Ok;
$obBtnOK->obEvento->setOnClick("executaFuncaoAjax('submeter','',true);");

$botoesForm  = array ( $obBtnOK , $obBtnLimparCampos );

$obChkAtivos = new Radio();
$obChkAtivos->setName   ('stSituacao');
$obChkAtivos->setRotulo ('Cadastro');
$obChkAtivos->setLabel  ('Ativos');
$obChkAtivos->setValue  ('ativos');
$obChkAtivos->setNull   (false);
$obChkAtivos->setChecked(true);
$obChkAtivos->setTitle  ('Informe o cadastro para filtro para emissão de arquivo');
$obChkAtivos->obEvento->setOnChange("executaFuncaoAjax('limparCampos'); montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$obChkAposentados = new Radio();
$obChkAposentados->setName ('stSituacao');
$obChkAposentados->setLabel('Aposentados');
$obChkAposentados->setValue('aposentados');
$obChkAposentados->obEvento->setOnChange("executaFuncaoAjax('limparCampos'); montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$obChkPensionistas = new Radio();
$obChkPensionistas->setName ('stSituacao');
$obChkPensionistas->setLabel('Pensionistas');
$obChkPensionistas->setValue('pensionistas');
$obChkPensionistas->obEvento->setOnChange("executaFuncaoAjax('limparCampos'); montaParametrosGET('gerarSpanPensionistas','stSituacao');");

$obChkRescindidos = new Radio();
$obChkRescindidos->setName ('stSituacao');
$obChkRescindidos->setLabel('Rescindidos');
$obChkRescindidos->setValue('rescindidos');
$obChkRescindidos->obEvento->setOnChange("executaFuncaoAjax('limparCampos'); montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$arChkCadastro = array($obChkAtivos,$obChkRescindidos,$obChkAposentados,$obChkPensionistas);

$obSpnCadastro = new Span();
$obSpnCadastro->setId('spnCadastro');

$obSpnListaCampos = new Span();
$obSpnListaCampos->setId('spnListaCampos');

$obSpnFiltroTipoFolha = new Span();
$obSpnFiltroTipoFolha->setId('spnFiltroTipoFolha');

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnCtrl);
$obFormulario->addHidden        ($obHdnCaminho);
$obFormulario->addHidden        ($obHdnTipoFiltroExtra, true);
$obFormulario->addTitulo        ($obRFolhaPagamentoFolhaSituacao->consultarCompetencia(), 'right');
$obFormulario->addTitulo        ('Opção de Filtro');
$obFormulario->agrupaComponentes($arChkCadastro);
$obFormulario->addSpan          ($obSpnCadastro);
$obFormulario->addSpan          ($obSpnCampos);
$obFormulario->addSpan          ($obSpnBotoes);
$obFormulario->addSpan          ($obSpnListaCampos);
$obFormulario->addSpan          ($obSpnFiltroTipoFolha);
$obFormulario->defineBarra      ($botoesForm);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
