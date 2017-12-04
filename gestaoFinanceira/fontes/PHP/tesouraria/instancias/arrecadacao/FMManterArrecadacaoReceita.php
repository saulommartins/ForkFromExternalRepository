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
    * Página de Formulário para Arrecadação Receita
    * Data de Criação   : 16/12/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30702 $
    $Name$
    $Autor:$
    $Date: 2007-08-20 16:02:02 -0300 (Seg, 20 Ago 2007) $

    * Casos de uso: uc-02.04.04

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CLA_IAPPLETTERMINAL;
include_once CAM_GF_TES_NEGOCIO.'RTesourariaBoletim.class.php';
include_once CAM_GF_ORC_COMPONENTES.'IPopUpReceita.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ManterArrecadacaoReceita';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

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
    include_once $pgJs;

    sistemaLegado::LiberaFrames();

    $obRTesourariaBoletim = new RTesourariaBoletim();
    $obRTesourariaBoletim->setExercicio  (Sessao::getExercicio());
    $obRTesourariaBoletim->setDataBoletim(date('d/m/'.Sessao::getExercicio()));
    $obRTesourariaBoletim->addArrecadacao();
    $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setExercicio          (Sessao::getExercicio());
    $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM     (Sessao::read('numCgm'));
    $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listarUsuariosEntidade($rsEntidade);

    //Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
    $stAcao = $request->get('stAcao');
    if (empty($stAcao)) {
        $stAcao = 'incluir';
    }

    $obForm = new Form;
    $obForm->setAction($pgProc);
    $obForm->setTarget('oculto');

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ('stAcao');
    $obHdnAcao->setValue($stAcao);

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ('stCtrl');
    $obHdnCtrl->setValue('');

    $obHdnCodBoletim = new Hidden();
    $obHdnCodBoletim->setName ('inCodBoletim');
    $obHdnCodBoletim->setValue($inCodBoletim);

    $obHdnDtBoletim = new Hidden();
    $obHdnDtBoletim->setName ('stDtBoletim');
    $obHdnDtBoletim->setValue($stDtBoletim);

    $obHdnVlTotal = new Hidden;
    $obHdnVlTotal->setName ('nuVlTotal');
    $obHdnVlTotal->setValue('');

    $obHdnCodEntidade = new Hidden;
    $obHdnCodEntidade->setName ('inCodEntidade');
    $obHdnCodEntidade->setId   ('inCodEntidade');

    $obHdnVlTotalLista = new Hidden;
    $obHdnVlTotalLista->setName ('nuVlTotalLista');
    $obHdnVlTotalLista->setValue('');

    $obHdnEval = new HiddenEval();
    $obHdnEval->setName ('stEval');
    $obHdnEval->setValue($stEval);

    $obApplet = new IAppletTerminal($obForm);

    // Define Objeto Select para Entidade
    $obCmbEntidade = new Select();
    $obCmbEntidade->setRotulo    ('Entidade');
    $obCmbEntidade->setName      ('inCodigoEntidade');
    $obCmbEntidade->setId        ('inCodigoEntidade');
    $obCmbEntidade->setTitle     ('Selecione a Entidade.');
    $obCmbEntidade->setCampoId   ('cod_entidade');
    $obCmbEntidade->setCampoDesc ('nom_cgm');
    $obCmbEntidade->setNull      (false);

    if ($rsEntidade->getNumLinhas() > 1) {
        $obCmbEntidade->addOption            ('0', 'Selecione');
        $obCmbEntidade->obEvento->setOnChange("montaParametrosGET('buscaBoletim');");
    } else {
        $jsOnload = "montaParametrosGET('buscaBoletim');";
    }

    $obCmbEntidade->preencheCombo($rsEntidade);
    $obCmbEntidade->setValue     ($_REQUEST['inCodigoEntidade']);

    $obSpanBoletim = new Span;
    $obSpanBoletim->setId('spnBoletim');

    // Define Objeto BuscaInner para Receita
    $obBscReceita = new IPopUpReceita($obCmbEntidade);
    $obBscReceita->setId       ('stNomReceita');
    $obBscReceita->setTitle    ('Digite o Reduzido da Receita.');
    $obBscReceita->setTipoBusca('retencoes'); // Retenções podem ser informadas na rotina de Arrecadação 'normal'
                                              // para poder desfazer um ajuste via 'Devolução de Receita'.
    $obBscReceita->setUsaFiltro(true);
    $obBscReceita->setNull     (true);
    $obBscReceita->setRotulo   ( "*Receita" );
    $obBscReceita->obCampoCod->setValue    ($request->get('inCodReceita'));
    $obBscReceita->setValue                ($request->get('stNomReceita'));

    if ($stAcao == 'incluir') {
        $obBscReceita->obCampoCod->obEvento->setOnBlur("montaParametrosGET('montaTipo','inCodReceita,inCodEntidade,inCodPlano'); montaParametrosGET('montaBemAlienacao','inCodReceita,inCodEntidade');");
    }

    $obBscReceita->obImagem->setId('imgReceita');

    /**
     * Implementacao da arrecadacao via carne
     */
    // Define objeto TextBox para a leitura otica do codigo de barras
    $obTxtCodBarraOtico = new TextBox();
    $obTxtCodBarraOtico->setName     ('stCodBarraOtico');
    $obTxtCodBarraOtico->setId       ('stCodBarraOtico');
    $obTxtCodBarraOtico->setRotulo   ('Leitura Ótica do Código de Barras');
    $obTxtCodBarraOtico->setTitle    ('Informe o código de barras.');
    $obTxtCodBarraOtico->setInteiro  (true);
    $obTxtCodBarraOtico->setMaxLength(44);
    $obTxtCodBarraOtico->setStyle    ('width:317px');
    $obTxtCodBarraOtico->setNull     (true);
    $obTxtCodBarraOtico->obEvento->setOnChange("montaParametrosGET('montaTipo','stCodBarraOtico');");

    // Define objeto TextBox para a digitacao do codigo de barras
    $obTxtCodBarraManual = new TextBox();
    $obTxtCodBarraManual->setName   ('stCodBarraManual');
    $obTxtCodBarraManual->setId     ('stCodBarraManual');
    $obTxtCodBarraManual->setRotulo ('Digitação do Código de Barras');
    $obTxtCodBarraManual->setTitle  ('Informe o código de barras.');
    $obTxtCodBarraManual->setMascara('99999999999 9 99999999999 9 99999999999 9 99999999999 9');
    $obTxtCodBarraManual->setStyle  ('width:370px');
    $obTxtCodBarraManual->setNull   (true);
    $obTxtCodBarraManual->obEvento->setOnChange("montaParametrosGET('montaTipo','stCodBarraManual');");

    // Define Objeto BuscaInner para conta
    $obBscConta = new BuscaInner;
    $obBscConta->setRotulo('Conta');

    if ($stAcao == 'devolucao') {
        $obBscConta->setTitle('Informe o Código da Conta.');
    } else {
        $obBscConta->setTitle('Informe a Conta Banco que Receberá o Valor Arrecadado.');
    }

    $obBscConta->setId     ('stNomConta');

    $obBscConta->setNull   (false);
    $obBscConta->obCampoCod->setName     ('inCodPlano');
    $obBscConta->obCampoCod->setId       ('inCodPlano');
    $obBscConta->obCampoCod->setSize     (10);
    $obBscConta->obCampoCod->setNull     (false);
    $obBscConta->obCampoCod->setMaxLength(8);
    $obBscConta->obCampoCod->setAlign    ('left');
    $obBscConta->setValoresBusca(CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(),$obForm->getName(),'tes_pagamento_arrecadacao');
    $obBscConta->obCampoCod->setValue    ($_REQUEST['inCodPlano']);
    $obBscConta->setValue                ($_REQUEST['stNomConta']);
    $obBscConta->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodPlano','stNomConta','tes_pagamento&inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");

    // Define Obeto Numerico para valor da arrecadacao
    $obTxtValor = new Moeda();
    $obTxtValor->setRotulo('Valor');

    if ($stAcao == 'devolucao') {
        $obTxtValor->setTitle('Informe o Valor.');
    } else {
        $obTxtValor->setTitle('Informe o Valor a Arrecadar.');
    }

    $obTxtValor->setName     ('nuValor');
    $obTxtValor->setId       ('nuValor');
    $obTxtValor->setNull     (false);
    $obTxtValor->setDecimais (2 );
    $obTxtValor->setNegativo (false);
    $obTxtValor->setSize     (17);
    $obTxtValor->setMaxLength(17);
    $obTxtValor->setValue    ('0,00');

    if ($stAcao == 'incluir') {
        $obTxtValor->setLabel(true);
    }

    $obSpnModalidade = new Span();
    $obSpnModalidade->setId('spnModalidade');
    
    $obSpnBemAlienacao = new Span();
    $obSpnBemAlienacao->setId('spnBemAlienacao');

    // Define Objeto TextArea para observações
    $obTxtObs = new TextArea;
    $obTxtObs->setName  ('stObservacoes');
    $obTxtObs->setId    ('stObservacoes');
    $obTxtObs->setValue ($stObservacoes);
    $obTxtObs->setRotulo('Observações');

    if ($stAcao == 'devolucao') {
        $obTxtObs->setTitle('Informe a Observação ref. ao Estorno.');
    } else {
        $obTxtObs->setTitle('Informe a Observação ref. a este Recebimento.');
    }

    $obTxtObs->setNull(true);
    $obTxtObs->setRows(2);
    $obTxtObs->setCols(100);

    $obOk = new Ok;
    $obOk->obEvento->setOnClick("if (Valida()) { Salvar(); BloqueiaFrames(true,false); }");

    $obLimpar = new Limpar();
    $obLimpar->obEvento->setOnClick('Limpar();');

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addForm      ($obForm);
    $obFormulario->addHidden    ($obHdnAcao);
    $obFormulario->addHidden    ($obHdnCtrl);
    $obFormulario->addHidden    ($obHdnCodBoletim);
    $obFormulario->addHidden    ($obHdnDtBoletim);
    $obFormulario->addHidden    ($obHdnVlTotal);
    $obFormulario->addHidden    ($obHdnVlTotalLista);
    $obFormulario->addHidden    ($obApplet);
    $obFormulario->addHidden    ($obHdnCodEntidade);
    $obFormulario->addTitulo    ('Dados da Entidade.');
    $obFormulario->addComponente($obCmbEntidade);
    $obFormulario->addTitulo    ('Dados do Boletim');
    $obFormulario->addSpan      ($obSpanBoletim);

    if ($stAcao == 'devolucao') {
        $obFormulario->addTitulo('Dados da Devolução de Receita');
    } else {
        $obFormulario->addTitulo('Dados da Arrecadação');
    }

    if ($stAcao != 'devolucao') {
        $obFormulario->addComponente($obTxtCodBarraOtico);
        $obFormulario->addComponente($obTxtCodBarraManual);
    }

    $obFormulario->addComponente($obBscReceita);

    if ($stAcao != 'devolucao') {
        $obFormulario->addSpan($obSpnModalidade);
    }
    
    $obFormulario->addSpan($obSpnBemAlienacao);
    
    $obFormulario->addComponente($obBscConta);
    $obFormulario->addComponente($obTxtValor);
    $obFormulario->addComponente($obTxtObs);
    $obFormulario->defineBarra  (array($obOk, $obLimpar));
    $obFormulario->show();

        $jsOnload .= "jq('#stNomReceita').parent().parent().closest('tr').hide() ;";
        $jsOnload .= "jq('#stNomConta').parent().parent().closest('tr').hide();";

    if ( ($_REQUEST['inCodBoletim'] != '') && ($_REQUEST['inCodigoEntidade'] != "") ) {
        $jsOnload .= "ajaxJavaScript('".CAM_GF_TES_INSTANCIAS."arrecadacao/".$pgOcul."?inCodigoEntidade=".$_REQUEST['inCodigoEntidade']."&inCodBoletim=".$_REQUEST['inCodBoletim']."','buscaBoletim');";
    }
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>