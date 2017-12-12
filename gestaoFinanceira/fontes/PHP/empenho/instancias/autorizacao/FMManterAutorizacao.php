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
    * Página de Processamento de Autorização
    * Data de Criação   : 01/12/2004

    * @author Analista Jorge B. Ribarr
    * @author Desenvolvedor Anderson R. M. Buzo
    * @author Desenvolvedor Eduardo Martins

    * @ignore

    $Id: FMManterAutorizacao.php 65615 2016-06-02 13:23:31Z franver $

    * Casos de uso: uc-02.03.02
                    uc-02.01.08
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_INCLUDE.'validaGF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoAutorizacaoEmpenho.class.php';
include_once CAM_FW_HTML.'MontaAtributos.class.php';
include_once CAM_GA_ADM_COMPONENTES.'IMontaAssinaturas.class.php';
include_once TEMP.'TEmpenhoCategoriaEmpenho.class.php';
include_once CAM_GP_ALM_COMPONENTES.'IPopUpCentroCustoUsuario.class.php';
require_once CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php";

//Define o nome dos arquivos PHP
$stPrograma = 'ManterAutorizacao';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao','incluir');

$stFiltro = '';
if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
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
    include_once ($pgJS);

    $obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;

    $rsUnidade = new RecordSet ;

    $rsClassificacao = new RecordSet;
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio(Sessao::getExercicio());
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->setExercicio(Sessao::getExercicio());
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM(Sessao::read('numCgm'));
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade($rsEntidade);
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->listar($rsTipo);
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->listar($rsHistorico);

    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->setExercicio(Sessao::getExercicio());
    $obREmpenhoAutorizacaoEmpenho->obRUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario($rsOrgao, $stOrder);
    $obREmpenhoAutorizacaoEmpenho->listarUnidadeMedida($rsUnidade);

    while (!$rsUnidade->eof()) {
        if ($rsUnidade->getCampo('nom_unidade' ) == 'Unidade') {
            $inCodUnidade       = $rsUnidade->getCampo('cod_unidade' ).'-'.$rsUnidade->getCampo('cod_grandeza').'-'.$rsUnidade->getCampo('nom_unidade');
            $inCodUnidadePadrao = $rsUnidade->getCampo('cod_unidade' ).'-'.$rsUnidade->getCampo('cod_grandeza').'-'.$rsUnidade->getCampo('nom_unidade');
        }
        $rsUnidade->proximo();
    }
    $rsUnidade->setPrimeiroElemento();

    $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento($stFormaExecucao);
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio(Sessao::getExercicio());

    $stMascaraRubrica = $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

    $stDtValidadeInicial = date('d/m').'/'.Sessao::getExercicio();
    $stDtValidadeFinal   = '31/12/'.Sessao::getExercicio();

    Sessao::remove('arItens');

    $inCodHistorico = 0;

    if ($stAcao == 'alterar') {
        Sessao::remove('inCodUnidadeOrcamentaria');
        Sessao::remove('arItens');

        $obREmpenhoAutorizacaoEmpenho->setExercicio     (Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->setCodAutorizacao($request->get('inCodAutorizacao'));
        $obREmpenhoAutorizacaoEmpenho->setCodPreEmpenho ($request->get('inCodPreEmpenho'));
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade($request->get('inCodEntidade'));
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setCodReserva($request->get('inCodReserva'));
        $obREmpenhoAutorizacaoEmpenho->consultar();

        $boItemMaterial = $obREmpenhoAutorizacaoEmpenho->consultarItemMaterial();

        $stNomEmpenho          = $obREmpenhoAutorizacaoEmpenho->getDescricao();
        $inCodEntidade         = $request->get('inCodEntidade');
        $stNomEntidade         = $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->getNomCGM();
        $inCodOrgao            = $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
        $inCodUnidadeOrcamento = $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
        $inCodTipo             = $obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->getCodTipo();
        $inCodPreEmpenho       = $request->get('inCodPreEmpenho');
        $inCodAutorizacao      = $request->get('inCodAutorizacao');
        $inCodReserva          = $request->get('inCodReserva');
        $inCodDespesa          = $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa();
        $stNomDespesa          = $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getDescricao();
        $stCodClassificacao    = $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao();
        $stNomClassificacao    = $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->getDescricao();
        $inCodFornecedor       = $obREmpenhoAutorizacaoEmpenho->obRCGM->getNumCGM();
        $stNomFornecedor       = $obREmpenhoAutorizacaoEmpenho->obRCGM->getnomCGM();
        $stDescricao           = $obREmpenhoAutorizacaoEmpenho->getDescricao();
        $inCodHistorico        = $obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->getCodHistorico();
        $inCodCategoria        = $obREmpenhoAutorizacaoEmpenho->getCodCategoria();

        if ($inCodCategoria == 2 || $inCodCategoria == 3) {
            include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoContrapartidaAutorizacao.class.php';
            $obTEmpenhoContrapartidaAutorizacao = new TEmpenhoContrapartidaAutorizacao();
            $obTEmpenhoContrapartidaAutorizacao->setDado('cod_autorizacao', $obREmpenhoAutorizacaoEmpenho->getCodAutorizacao());
            $obTEmpenhoContrapartidaAutorizacao->setDado('cod_entidade'   , $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade());
            $obTEmpenhoContrapartidaAutorizacao->setDado('exercicio'      , $obREmpenhoAutorizacaoEmpenho->getExercicio());
            $obTEmpenhoContrapartidaAutorizacao->recuperaContrapartidaLancamento($rsContrapartida);
            $inCodContrapartida = $rsContrapartida->getCampo('conta_contrapartida');
        }

        if ($obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getDtValidadeInicial()) {
            $stDtValidadeInicial = $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getDtValidadeInicial();
        } else {
            $stDtValidadeInicial = $obREmpenhoAutorizacaoEmpenho->getDtAutorizacao();
        }

        $stDtValidadeFinal = $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getDtValidadeFinal();

        if ($obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getDtInclusao()) {
            $stDtInclusao = $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getDtInclusao();
        } else {
            $stDtInclusao = $obREmpenhoAutorizacaoEmpenho->getDtAutorizacao();
        }
        if($obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getVlReserva()!='')
            $nuVlReserva = number_format($obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getVlReserva(),2,',','.');
        $arItemPreEmpenho = $obREmpenhoAutorizacaoEmpenho->getItemPreEmpenho();

        foreach ($arItemPreEmpenho as $inCount => $obItemPreEmpenho) {
            $nuVlUnitario = ($obItemPreEmpenho->getValorTotal()/$obItemPreEmpenho->getQuantidade());
            $nuVlTotalItens = bcadd( $nuVlTotalItens, $obItemPreEmpenho->getValorTotal(),4);

            $arItens = Sessao::read('arItens');

            $arItens[$inCount]['num_item']     = $inCount+1;
            if($obItemPreEmpenho->getCodItemPreEmp()!=''){
                $arItens[$inCount]['cod_item']     = $obItemPreEmpenho->getCodItemPreEmp();
                $booCodItem = true;
            }else{
                $booCodItem = false;
            }

            $arItens[$inCount]['nom_item']     = $obItemPreEmpenho->getNomItem();
            $arItens[$inCount]['cod_centro']   = $obItemPreEmpenho->getCodCentroCusto();
            $arItens[$inCount]['complemento']  = $obItemPreEmpenho->getComplemento();
            $arItens[$inCount]['quantidade']   = $obItemPreEmpenho->getQuantidade();
            $arItens[$inCount]['vl_unitario']  = $nuVlUnitario;
            $arItens[$inCount]['cod_unidade']  = $obItemPreEmpenho->obRUnidadeMedida->getCodUnidade();
            $arItens[$inCount]['cod_grandeza'] = $obItemPreEmpenho->obRUnidadeMedida->obRGrandeza->getCodGrandeza();
            $arItens[$inCount]['nom_unidade']  = $obItemPreEmpenho->getNomUnidade();
            $arItens[$inCount]['cod_material'] = $obItemPreEmpenho->getCodMaterial();
            $arItens[$inCount]['vl_total']     = $obItemPreEmpenho->getValorTotal();
            $arItens[$inCount]['cod_marca']    = $obItemPreEmpenho->getCodigoMarca();

            // Teve que ser feito a consulta da marca separadamente e manualmente
            if ($arItens[$inCount]['cod_marca'] != "") {
                $stNomeMarca = SistemaLegado::pegaDado('descricao', 'almoxarifado.marca', ' WHERE cod_marca = '.$arItens[$inCount]['cod_marca']);
                $arItens[$inCount]['nome_marca'] = $stNomeMarca;
            }

            Sessao::write('arItens', $arItens);
        }
        $nuVlUnitario = '';

        $arChaveAtributo =  array('cod_pre_empenho' => $request->get('inCodPreEmpenho'),
                                  'exercicio'       => Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->setChavePersistenteValores          ($arChaveAtributo);
        $obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionadosValores($rsAtributos);

        if ($obREmpenhoAutorizacaoEmpenho->getBoModuloEmpenho() == true) {
            // Se foi emitido pela gestão patrimonial, define o atributo Modalidade como label, pois não pode ser alterado
            // Atributos 101 = Modalidade; 120 = Número Processo Administrativo; e 121 = Exercicio Processo Administrativo
            $arAtributosLabelAL = array(120,121);
            while (!$rsAtributos->EOF()) {
                if ( $rsAtributos->getCampo('cod_atributo') == 101 ) {
                    $rsAtributos->setCampo('label', true);
                } else if( in_array($rsAtributos->getCampo('cod_atributo'), $arAtributosLabelAL) AND SistemaLegado::pegaConfiguracao('cod_uf',2,Sessao::getExercicio(), $boTransacao) == 2 ) {
                    $rsAtributos->setCampo('label', true);
                } else {
                    $rsAtributos->setCampo('label', false);
                }
                $rsAtributos->proximo();
            }
            $rsAtributos->setPrimeiroElemento();
        }

        $jsOnLoad = "montaParametrosGET('alterar','');";

        if($booCodItem){
            $jsOnLoad .= "bloqueiaTipoItem('Catalogo');";
        }else{
            $jsOnLoad .= "bloqueiaTipoItem('Descricao');";
        }
    } else {
        $obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionados($rsAtributos);
    }

    //*****************************************************//
    // Define COMPONENTES DO FORMULARIO
    //*****************************************************//
    //Instancia o formulário
    $obForm = new Form;
    $obForm->setAction($pgProc);
    $obForm->setTarget('oculto');

    //Define o objeto da ação stAcao
    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ('stAcao');
    $obHdnAcao->setValue($stAcao);

    //Define o Hidden para valor padrao da unidade
    $obHdnUnidadePadrao = new Hidden;
    $obHdnUnidadePadrao->setName ('inCodUnidadePadrao');
    $obHdnUnidadePadrao->setValue($inCodUnidadePadrao);

    //Define o objeto de controle
    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ('stCtrl');
    $obHdnCtrl->setValue('');

    $obHdnNumItem = new Hidden;
    $obHdnNumItem->setName ('hdnNumItem');
    $obHdnNumItem->setId   ('hdnNumItem');
    $obHdnNumItem->setValue($hdnNumItem);

    //Define o objeto da Data de Inclusão de Reserva
    $obHdnDtInclusao = new Hidden;
    $obHdnDtInclusao->setName ('stDtInclusao');
    $obHdnDtInclusao->setValue($stDtInclusao);

    //Define o objeto da Data de Validade Inicial da Reserva
    $obHdnDtValidadeInicial = new Hidden;
    $obHdnDtValidadeInicial->setName ('stDtValidadeInicial');
    $obHdnDtValidadeInicial->setValue($stDtValidadeInicial);

    //Define o Hidden de Valor de Reserva
    $obHdnVlReserva = new Hidden;
    $obHdnVlReserva->setId   ('hdnVlReserva');
    $obHdnVlReserva->setName ('hdnVlReserva');
    $obHdnVlReserva->setValue(0);

    if ($stAcao == 'alterar') {
        // Define o objeto Hidden para Codigo da Autorizacao
        $obHdnCodAutorizacao = new Hidden;
        $obHdnCodAutorizacao->setName ('inCodAutorizacao');
        $obHdnCodAutorizacao->setValue($inCodAutorizacao);

        // Define o objeto Hidden para Codigo da Pre Empenho
        $obHdnCodPreEmpenho = new Hidden;
        $obHdnCodPreEmpenho->setName ('inCodPreEmpenho');
        $obHdnCodPreEmpenho->setValue($inCodPreEmpenho);

        // Define o objeto Hidden para Codigo da Entidade
        $obHdnCodEntidade = new Hidden;
        $obHdnCodEntidade->setName ('inCodEntidade');
        $obHdnCodEntidade->setId   ('inCodEntidade'); // Necessário para o componete IMontaAssinaturas
        $obHdnCodEntidade->setValue($inCodEntidade);

        // Define o objeto Hidden para Codigo da Reserva  $js = "";
        $obHdnCodReserva = new Hidden;
        $obHdnCodReserva->setName ('inCodReserva');
        $obHdnCodReserva->setValue($inCodReserva);

        // Define o objeto Hidden para Codigo Estrutural
        $obHdnCodEstrutural = new Hidden;
        $obHdnCodEstrutural->setName ('stCodEstrutural');
        $obHdnCodEstrutural->setValue($stCodClassificacao);

        // Define o objeto Hidden para Codigo Órgão
        $obHdnCodOrgao = new Hidden;
        $obHdnCodOrgao->setName ('hdnCodOrgao');
        $obHdnCodOrgao->setValue($inCodOrgao);

        // Define o objeto Hidden para Codigo Unidade Orcamentaria
        $obHdnCodUnidade = new Hidden;
        $obHdnCodUnidade->setName ('hdnCodUnidade');
        $obHdnCodUnidade->setValue($inCodUnidadeOrcamento);

        // Define o objeto Hidden para Conta Contrapartida
        $obHdnCodContrapartida = new Hidden;
        $obHdnCodContrapartida->setName ('hdnCodContrapartida');
        $obHdnCodContrapartida->setValue($inCodContrapartida);
    }

    // Define o objeto Hidden para Módulo empenho
    $obHdnBoModuloEmpenho = new Hidden;
    $obHdnBoModuloEmpenho->setName ('hdnBoModuloEmpenho');
    $obHdnBoModuloEmpenho->setValue(false);

    $obHdnVlTotalAutorizacao = new Hidden;
    $obHdnVlTotalAutorizacao->setName ('nuVlTotalAutorizacao');
    $obHdnVlTotalAutorizacao->setId   ('nuVlTotalAutorizacao');
    $obHdnVlTotalAutorizacao->setValue('');

    if ($stAcao != 'alterar') {
        // Define Objeto TextBox para Codigo da Entidade
        $obTxtCodEntidade = new TextBox;
        $obTxtCodEntidade->setName('inCodEntidade');
        $obTxtCodEntidade->setId  ('inCodEntidade');

        $obTxtCodEntidade->obEvento->setOnChange('getIMontaAssinaturas()');

        if ($rsEntidade->getNumLinhas()==1) {
            $obTxtCodEntidade->setValue($rsEntidade->getCampo('cod_entidade'));
            $jsOnLoad .= "BloqueiaFrames(true,false);montaParametrosGET('buscaDtAutorizacao','inCodEntidade');";
        } else {
            $obTxtCodEntidade->setValue($inCodEntidade);
            $obTxtCodEntidade->obEvento->setOnBlur("montaParametrosGET('buscaDtAutorizacao');");
        }
        $obTxtCodEntidade->setRotulo ('Entidade');
        $obTxtCodEntidade->setTitle  ('Selecione a entidade.');
        $obTxtCodEntidade->setInteiro(true);
        $obTxtCodEntidade->setNull   (false);

        // Define Objeto Select para Nome da Entidade
        $obCmbNomEntidade = new Select;
        $obCmbNomEntidade->setName ('stNomEntidade');
        $obCmbNomEntidade->setId   ('stNomEntidade');
        $obCmbNomEntidade->setValue($inCodEntidade);

        $stOnChange  = "jq('#inCodEntidade').val(this.value);";
        $stOnChange .= " montaParametrosGET('buscaDtAutorizacao');";
        $stOnChange .= " getIMontaAssinaturas();";
        $obCmbNomEntidade->obEvento->setOnChange($stOnChange);

        if ($rsEntidade->getNumLinhas()>1) {
            $obCmbNomEntidade->addOption('', 'Selecione');
        }

        $obCmbNomEntidade->setCampoId   ('cod_entidade');
        $obCmbNomEntidade->setCampoDesc ('nom_cgm');
        $obCmbNomEntidade->setStyle     ('width: 520');
        $obCmbNomEntidade->preencheCombo($rsEntidade);
        $obCmbNomEntidade->setNull      (false);
    } else {
        $obLblEntidade = new Label;
        $obLblEntidade->setRotulo('*Entidade');
        $obLblEntidade->setValue ($inCodEntidade.' - '.$stNomEntidade);

        // Define Objeto Hidden para despesa
        $obHdnDespesa = new Hidden();
        $obHdnDespesa->setName ('inCodDespesaAux');
        $obHdnDespesa->setValue($inCodDespesa);

        // Define Objeto Hidden para Valor dos itens exlcuidos
        $obHdnVlItensExcluidos = new Hidden();
        $obHdnVlItensExcluidos->setName ('nuVlItemExcluidos');
        $obHdnVlItensExcluidos->setValue($nuVlTotalItens);
    }

    // Define objeto Data para armazenar a data da autorização
    $obDtAutorizacao = new Data;
    $obDtAutorizacao->setName  ('stDtAutorizacao');
    $obDtAutorizacao->setId    ('stDtAutorizacao');
    $obDtAutorizacao->setValue ('');
    $obDtAutorizacao->setRotulo('Data da Autorização');
    $obDtAutorizacao->setTitle ('Informe a data da autorização.');
    $obDtAutorizacao->setNull  (false);
    $obDtAutorizacao->obEvento->setOnChange("buscaDado('buscaDespesa');");
    if ($stAcao == 'incluir'){
        $obDtAutorizacao->setLabel ( TRUE );
        $jsOnLoad .= "montaParametrosGET('LiberaDataAutorizacao');";
    }

    // Define Objeto BuscaInner para Despesa
    $obBscDespesa = new BuscaInner;
    $obBscDespesa->setRotulo ('Dotação Orçamentária');
    $obBscDespesa->setTitle  ('Informe a dotação orcamentária.');
    $obBscDespesa->setNulL   (true);
    $obBscDespesa->setId     ('stNomDespesa');
    $obBscDespesa->setValue  ($stNomDespesa );
    $obBscDespesa->obCampoCod->setName     ('inCodDespesa');
    $obBscDespesa->obCampoCod->setSize     (10);
    $obBscDespesa->obCampoCod->setMaxLength(5);
    $obBscDespesa->obCampoCod->setValue    ($inCodDespesa);
    $obBscDespesa->obCampoCod->setAlign    ('left');
    $obBscDespesa->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaDespesa');");
    $obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDespesa','stNomDespesa','autorizacaoEmpenho&inCodEntidade='+document.frm.inCodEntidade.value+'&inNumOrgao='+document.frm.inCodOrgao.value+'&inNumUnidade='+document.frm.inCodUnidadeOrcamento.value,'".Sessao::getId()."','800','550');");

    // Define Objeto Select para Classificacao da Despesa
    $obCmbClassificacao = new Select;
    $obCmbClassificacao->setRotulo('Desdobramento');
    $obCmbClassificacao->setTitle ('Selecione a rubrica de despesa.');
    $obCmbClassificacao->setName  ('stCodClassificacao');
    $obCmbClassificacao->setId    ('stCodClassificacao');
    $obCmbClassificacao->setValue ($stCodClassificacao);
    $obCmbClassificacao->setStyle ('width: 600');
    $obCmbClassificacao->setNull  (true);
    if (!$stFormaExecucao) {
        $obCmbClassificacao->setDisabled(true);
    }
    $obCmbClassificacao->addOption    ('', 'Selecione');
    $obCmbClassificacao->setCampoId   ('cod_estrutural');
    $obCmbClassificacao->setCampoDesc ('cod_estrutural');
    $obCmbClassificacao->preencheCombo($rsClassificacao);

    // Define Objeto Span Para lista de itens
    $obSpanSaldo = new Span;
    $obSpanSaldo->setId('spnSaldoDotacao');

    // Define Objeto Select para Orgao Orcamentario
    $obCmbOrgaoOrcamento = new Select;
    $obCmbOrgaoOrcamento->setName     ('inCodOrgao');
    $obCmbOrgaoOrcamento->setRotulo   ('Órgão Orçamentário');
    $obCmbOrgaoOrcamento->setTitle    ('Selecione o órgão orçamentário.');
    $obCmbOrgaoOrcamento->setId       ('inCodOrgao');
    $obCmbOrgaoOrcamento->setValue    ($inCodOrgao);
    $obCmbOrgaoOrcamento->addOption   ('', 'Selecione');
    $obCmbOrgaoOrcamento->setCampoId  ('num_orgao');
    $obCmbOrgaoOrcamento->setCampoDesc('[num_orgao] - [nom_orgao]');
    if ($stAcao == 'alterar') {
        $obCmbOrgaoOrcamento->preencheCombo($rsOrgao);
    }
    $obCmbOrgaoOrcamento->setNull(false);
    $obCmbOrgaoOrcamento->obEvento->setOnChange("montaParametrosGET('buscaOrgaoUnidade');");

    // Define Objeto Select para Unidade Orcamentaria
    $obCmbUnidadeOrcamento = new Select;
    $obCmbUnidadeOrcamento->setName     ('inCodUnidadeOrcamento');
    $obCmbUnidadeOrcamento->setRotulo   ('Unidade Orçamentária');
    $obCmbUnidadeOrcamento->setTitle    ('Selecione a unidade orçamentária.');
    $obCmbUnidadeOrcamento->setId       ('inCodUnidadeOrcamento');
    $obCmbUnidadeOrcamento->setValue    ($inCodUnidadeOrcamento);
    $obCmbUnidadeOrcamento->addOption   ('', 'Selecione');
    $obCmbUnidadeOrcamento->setCampoId  ('num_unidade');
    $obCmbUnidadeOrcamento->setCampoDesc('nom_unidade');
    $obCmbUnidadeOrcamento->setNull     (false);

    // Define Objeto BuscaInner para Fornecedor
    $obBscFornecedor = new BuscaInner;
    $obBscFornecedor->setRotulo ('Fornecedor');
    $obBscFornecedor->setTitle  ('Informe o fornecedor.');
    $obBscFornecedor->setId     ('stNomFornecedor');
    $obBscFornecedor->setValue  ($stNomFornecedor);
    $obBscFornecedor->setNull   (false );
    $obBscFornecedor->obCampoCod->setName     ('inCodFornecedor');
    $obBscFornecedor->obCampoCod->setSize     (10);
    $obBscFornecedor->obCampoCod->setNull     (false);
    $obBscFornecedor->obCampoCod->setMaxLength(8);
    $obBscFornecedor->obCampoCod->setValue    ($inCodFornecedor);
    $obBscFornecedor->obCampoCod->setAlign    ('left');
    $obBscFornecedor->obCampoCod->obEvento->setOnBlur("montaParametrosGET('buscaFornecedor'); montaParametrosGET('buscaContrapartida'); montaParametrosGET('verificaFornecedor');");
    $obBscFornecedor->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodFornecedor','stNomFornecedor','','".Sessao::getId()."','800','550');");

    $rsCategoriaEmpenho = new RecordSet();
    $obCategoriaEmpenho = new TEmpenhoCategoriaEmpenho();

    $obCategoriaEmpenho->recuperaTodos($rsCategoriaEmpenho);

    // Define Objeto Select para Categoria do Empenho
    $obCmbCategoriaEmpenho = new Select;
    $obCmbCategoriaEmpenho->setRotulo    ('Categoria do Empenho');
    $obCmbCategoriaEmpenho->setTitle     ('Informe a categoria do empenho.');
    $obCmbCategoriaEmpenho->setName      ('inCodCategoria');
    $obCmbCategoriaEmpenho->setId        ('inCodCategoria');
    $obCmbCategoriaEmpenho->setNull      (false);
    $obCmbCategoriaEmpenho->setValue     ($inCodCategoria);
    $obCmbCategoriaEmpenho->setStyle     ('width: 250');
    $obCmbCategoriaEmpenho->setCampoId   ('cod_categoria');
    $obCmbCategoriaEmpenho->setCampoDesc ('descricao');
    $obCmbCategoriaEmpenho->preencheCombo($rsCategoriaEmpenho);
    $obCmbCategoriaEmpenho->obEvento->setOnChange("montaParametrosGET('buscaContrapartida'); ");

    // Define Objeto Span Para Contrapartida
    $obSpanContrapartida = new Span;
    $obSpanContrapartida->setId('spnContrapartida');

    if (($stAcao == 'material') || ($boItemMaterial)) {
       // Define Objeto BuscaInner para Material
       $obBscMaterial = new BuscaInner;
       $obBscMaterial->setRotulo  ('Material');
       $obBscMaterial->setTitle   ('Este campo será utilizado quando a autorização for por material.');
       $obBscMaterial->setNulL    (true);
       $obBscMaterial->setId      ('stNomMaterial');
       $obBscMaterial->setValue   ($stNomMaterial);
       $obBscMaterial->obCampoCod->setName     ('inCodMaterial');
       $obBscMaterial->obCampoCod->setSize     (10);
       $obBscMaterial->obCampoCod->setReadOnly (true);
       $obBscMaterial->obCampoCod->setMaxLength(7);
       $obBscMaterial->obCampoCod->setValue    ($inCodMaterial);
       $obBscMaterial->obCampoCod->setAlign    ('left');
       $obBscMaterial->obCampoCod->obEvento->setOnBlur("montaParametrosGET('buscaMaterial');");
       $obBscMaterial->setFuncaoBusca("abrePopUp('".CAM_FRAMEWORK."popupsLegado/materialSiam/FLMaterialSiam.php','frm','inCodMaterial','stNomMaterial','','".Sessao::getId()."','800','550');");
    }

    // Define Objeto TextArea para Descricao
    $obTxtDescricao = new TextArea;
    $obTxtDescricao->setName         ('stDescricao');
    $obTxtDescricao->setId           ('stDescricao');
    $obTxtDescricao->setValue        ($stDescricao);
    $obTxtDescricao->setRotulo       ('Descrição da Autorização');
    $obTxtDescricao->setTitle        ('Informe a descrição.');
    $obTxtDescricao->setNull         (true);
    $obTxtDescricao->setRows         (2);
    $obTxtDescricao->setCols         (100);
    $obTxtDescricao->setMaxCaracteres(640);

    // Define Objeto Select para Histórico
    $obCmbHistorico = new Select;
    $obCmbHistorico->setName      ('inCodHistorico');
    $obCmbHistorico->setRotulo    ('Histórico Padrão');
    $obCmbHistorico->setTitle     ('Informe o histórico padrão.');
    $obCmbHistorico->setId        ('inCodHistorico');
    $obCmbHistorico->setValue     ($inCodHistorico);
    $obCmbHistorico->addOption    ('', 'Selecione');
    $obCmbHistorico->setCampoId   ('cod_historico');
    $obCmbHistorico->setCampoDesc ('nom_historico');
    $obCmbHistorico->preencheCombo($rsHistorico);
    $obCmbHistorico->setNull      (true);

    if ( $obREmpenhoAutorizacaoEmpenho->getBoModuloEmpenho() == true AND $stAcao == 'alterar' ) {
       $obLblItem = new Label;
       $obLblItem->setName  ('stNomItem');
       $obLblItem->setId    ('stNomItem');
       $obLblItem->setRotulo('Item');
    }else {
       // Define Objeto TextArea para Descricao do Item
       $obTxtNomItem = new TextArea;
       $obTxtNomItem->setName         ('stNomItem');
       $obTxtNomItem->setId           ('stNomItem');
       $obTxtNomItem->setValue        ($stNomItem);
       $obTxtNomItem->setRotulo       ('*Descrição do Item');
       $obTxtNomItem->setTitle        ('Informe a descrição do item.');
       $obTxtNomItem->setNull         (true);
       $obTxtNomItem->setRows         (1);
       $obTxtNomItem->setCols         (100);
       $obTxtNomItem->setMaxCaracteres(160);
       $obTxtNomItem->obEvento->setOnBlur('proximoFoco(this.value);');
    }

    // Define Objeto TextArea para Complemento
    $obTxtComplemento = new TextArea;
    $obTxtComplemento->setName  ('stComplemento');
    $obTxtComplemento->setId    ('stComplemento');
    $obTxtComplemento->setValue ($stComplemento);
    $obTxtComplemento->setRotulo('Complemento');
    $obTxtComplemento->setTitle ('Informe o complemento.');
    $obTxtComplemento->setRows  (2);
    $obTxtComplemento->setCols  (100);

    $obMarca = new IPopUpMarca($obForm);
    $obMarca->setNull               ( true );
    if ( $obREmpenhoAutorizacaoEmpenho->getBoModuloEmpenho() == true AND $stAcao == 'alterar') {
        $obMarca->setRotulo             ( 'Marca do Item' );
    }else{
        $obMarca->setRotulo             ( 'Marca' );
    }
    $obMarca->setId                 ( 'stNomeMarca' );
    $obMarca->setName               ( 'stNomeMarca' );
    $obMarca->obCampoCod->setName   ( 'inMarca' );
    $obMarca->obCampoCod->setId     ( 'inMarca' );
    $obMarca->obCampoCod->setValue  ( $inMarca );

    // Define Objeto Numeric para Quantidade
    $obTxtQuantidade = new Numerico;
    $obTxtQuantidade->setName     ('nuQuantidade');
    $obTxtQuantidade->setId       ('nuQuantidade');
    $obTxtQuantidade->setValue    ($nuQuantidade);
    $obTxtQuantidade->setRotulo   ('*Quantidade');
    $obTxtQuantidade->setTitle    ('Informe a quantidade.');
    $obTxtQuantidade->setNegativo (false);
    $obTxtQuantidade->setDefinicao('NUMERIC');
    $obTxtQuantidade->setSize     (14);    
    $obTxtQuantidade->setMaxLength(9);
    $obTxtQuantidade->setDecimais (4);
    $obTxtQuantidade->setFormatarNumeroBR  (true);
    $obTxtQuantidade->obEvento->setOnChange('gerarValorTotal(this);');

    // Define Objeto Select para Unidade
    $obCmbUnidade = new Select;
    $obCmbUnidade->setName      ('inCodUnidade');
    $obCmbUnidade->setId        ('inCodUnidade');
    $obCmbUnidade->setRotulo    ('*Unidade');
    $obCmbUnidade->setTitle     ('Selecione a unidade.');
    $obCmbUnidade->setValue     ($inCodUnidade);
    $obCmbUnidade->addOption    ('', 'Selecione');
    $obCmbUnidade->setCampoId   ('[cod_unidade]-[cod_grandeza]-[nom_unidade]');
    $obCmbUnidade->setCampoDesc ('nom_unidade');
    $obCmbUnidade->preencheCombo($rsUnidade);
    $obCmbUnidade->setNull      (true);

    // Define Objeto Moeda para Valor Unitário
    $obTxtVlUnitario = new ValorUnitario;
    $obTxtVlUnitario->setName     ('nuVlUnitario');
    $obTxtVlUnitario->setId       ('nuVlUnitario');
    $obTxtVlUnitario->setValue    ($nuVlUnitario);
    $obTxtVlUnitario->setRotulo   ('*Valor Unitário');
    $obTxtVlUnitario->setTitle    ('Informe o valor unitário.');
    $obTxtVlUnitario->setNull     (true);
    $obTxtVlUnitario->setDefinicao('NUMERIC');
    $obTxtVlUnitario->setDecimais (4);
    $obTxtVlUnitario->setSize     (21);
    $obTxtVlUnitario->setMaxLength(10);
    $obTxtVlUnitario->setFormatarNumeroBR  (true);
    $obTxtVlUnitario->obEvento->setOnChange('gerarValorTotal(this);');

    // Define Objeto Moeda para Valor Unitário
    $obTxtVlTotal = new ValorTotal;
    $obTxtVlTotal->setName     ('nuVlTotal');
    $obTxtVlTotal->setId       ('nuVlTotal');
    $obTxtVlTotal->setValue    ($nuVlTotal);
    $obTxtVlTotal->setRotulo   ('*Valor Total');
    $obTxtVlTotal->setTitle    ('Informe o valor total.');
    $obTxtVlTotal->setNull     (true);
    $obTxtVlTotal->setReadOnly (true);
    $obTxtVlTotal->setDefinicao('NUMERIC');
    $obTxtVlTotal->setSize     (21);
    $obTxtVlTotal->setMaxLength(12);
    $obTxtVlTotal->setFormatarNumeroBR  (true);
    $obTxtVlTotal->obEvento->setOnChange('gerarValorTotal(this);');

    // Define Objeto Button para  Incluir Item
    $obBtnIncluir = new Button;
    $obBtnIncluir->setValue('Incluir Item');
    $obBtnIncluir->setName ('btnIncluir');
    $obBtnIncluir->setId   ('btnIncluir');
    $obBtnIncluir->obEvento->setOnClick("jq('#stNomItem').focus();if(incluirItem()){montaParametrosGET('incluiItemPreEmpenho');}");
    
    // Define Objeto Button para  Alterar Item
    $obBtnAlterar = new Button;
    $obBtnAlterar->setValue('Alterar Item');
    $obBtnAlterar->setName ('btnAlterar');
    $obBtnAlterar->setId   ('btnAlterar');
    $obBtnAlterar->obEvento->setOnClick("montaParametrosGET('alteraMarcaItemPreEmpenho');");

    // Define Objeto Button para Limpar
    $obBtnLimpar = new Button;
    $obBtnLimpar->setValue('Limpar');
    $obBtnLimpar->obEvento->setOnClick("limparItem();jq('#stNomItem').focus();");

    // Define Objeto Span Para lista de itens
    $obSpan = new Span;
    $obSpan->setId('spnLista');

    // Define Objeto Label para Valor Total dos Itens
    $obLblVlTotal = new Label;
    $obLblVlTotal->setId    ('nuValorTotal');
    $obLblVlTotal->setName  ('nuValorTotal');
    $obLblVlTotal->setRotulo('TOTAL: ');

    // Objeto Span para valor da reserva e data de validade
    $obSpanReserva = new Span;
    $obSpanReserva->setId('spnReserva');

    $obMontaAtributos = new MontaAtributos;
    $obMontaAtributos->setTitulo   ('Atributos');
    $obMontaAtributos->setName     ('Atributo_');
    $obMontaAtributos->setRecordSet($rsAtributos);

    $obMontaAssinaturas = new IMontaAssinaturas(null, 'autorizacao_empenho');
    $obMontaAssinaturas->definePapeisDisponiveis('autorizacao_empenho');

    //Radio para Item Almoxarifado - Sim
    $obRdTipoItemC = new Radio;
    $obRdTipoItemC->setTitle      ( "Selecione o tipo de Item" );
    $obRdTipoItemC->setRotulo     ( "**Item do Almoxarifado" );
    $obRdTipoItemC->setName       ( "stTipoItemRadio" );
    $obRdTipoItemC->setId         ( "stTipoItemRadio1" );
    $obRdTipoItemC->setValue      ( "Catalogo" );
    $obRdTipoItemC->setLabel      ( "Sim" );
    $obRdTipoItemC->obEvento->setOnClick( "habilitaCampos('Catalogo');" );
    $obRdTipoItemC->setChecked( false );

    //Radio para Item Almoxarifado - Não
    $obRdTipoItemD = new Radio;
    $obRdTipoItemD->setRotulo   ( "**Item de Almoxarifado" );
    $obRdTipoItemD->setName     ( "stTipoItemRadio" );
    $obRdTipoItemD->setId       ( "stTipoItemRadio2" );
    $obRdTipoItemD->setValue    ( "Descricao" );
    $obRdTipoItemD->setLabel    ( "Não" );
    $obRdTipoItemD->obEvento->setOnClick( "habilitaCampos('Descricao');" );
    $obRdTipoItemD->setChecked( true );

    //Hidden para armazenar o tipo de Item Almoxarifado
    $obHdnTipoItem = new Hidden;
    $obHdnTipoItem->setName ('stTipoItem');
    $obHdnTipoItem->setId   ('stTipoItem');
    $obHdnTipoItem->setValue('Descricao');

    $arRadios = array( $obRdTipoItemC, $obRdTipoItemD );

    //Item de Almoxarifado - Catalogo
    include_once CAM_GP_ALM_COMPONENTES."IMontaItemUnidade.class.php";
    $obMontaItemUnidade = new IMontaItemUnidade($obForm);
    $obMontaItemUnidade->obIPopUpCatalogoItem->setRotulo("*Item");
    $obMontaItemUnidade->obIPopUpCatalogoItem->setNull(true);
    $obMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->setId("inCodItem");
    $obMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->obEvento->setOnBlur("montaParametrosGET('unidadeItem','inCodItem');");
    $obMontaItemUnidade->obIPopUpCatalogoItem->setId( 'stNomItemCatalogo' );
    $obMontaItemUnidade->obIPopUpCatalogoItem->setName( 'stNomItemCatalogo' );
    $obMontaItemUnidade->obSpnInformacoesItem->setStyle('visibility:hidden; display:none');

    //****************************************//
    // Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm  ($obForm);
    $obFormulario->addTitulo('Insira os Dados da Autorização');

    $obFormulario->addHidden($obHdnCtrl);
    $obFormulario->addHidden($obHdnAcao);
    $obFormulario->addHidden($obHdnUnidadePadrao);
    $obFormulario->addHidden($obHdnVlReserva);
    $obFormulario->addHidden($obHdnNumItem);

    if ($stAcao == 'alterar') {
        $obFormulario->addHidden    ($obHdnCodAutorizacao);
        $obFormulario->addHidden    ($obHdnCodPreEmpenho);
        $obFormulario->addHidden    ($obHdnCodEntidade);
        $obFormulario->addHidden    ($obHdnCodReserva);
        $obFormulario->addHidden    ($obHdnCodEstrutural);
        $obFormulario->addHidden    ($obHdnCodOrgao);
        $obFormulario->addHidden    ($obHdnCodUnidade);
        $obFormulario->addHidden    ($obHdnDespesa);
        $obFormulario->addHidden    ($obHdnVlItensExcluidos);
        $obFormulario->addHidden    ($obHdnDtInclusao);
        $obFormulario->addHidden    ($obHdnDtValidadeInicial);
        $obFormulario->addHidden    ($obHdnCodContrapartida);
        $obFormulario->addComponente($obLblEntidade);

        include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoAutorizacaoEmpenhoAssinatura.class.php';
        /* Montar um RecordSet com todas as assinaturas vinculadas ao documento na tabela correspondente */
        $obTAutorizacaoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
        $obTAutorizacaoAssinatura->setDado('exercicio'      , $obREmpenhoAutorizacaoEmpenho->stExercicio);
        $obTAutorizacaoAssinatura->setDado('cod_entidade'   , $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade());
        $obTAutorizacaoAssinatura->setDado('cod_autorizacao', $obREmpenhoAutorizacaoEmpenho->getCodAutorizacao());
        $obTAutorizacaoAssinatura->recuperaAssinaturasAutorizacao($rsAssinatura, '', '', '');
        if ($rsAssinatura->inNumLinhas > 0) {
            $obMontaAssinaturas->setOpcaoAssinaturas(true);
            $arAssinaturas = Sessao::read('assinaturas');
            $arAssinaturas['existentes'] = $rsAssinatura->arElementos;
            Sessao::write('assinaturas', $arAssinaturas);
        } else {
            $obMontaAssinaturas->setOpcaoAssinaturas(false);
        }

        if ($obREmpenhoAutorizacaoEmpenho->getBoModuloEmpenho() == true) {
            $obHdnBoModuloEmpenho->setValue(true);
            $obBscDespesa->setLabel(true);
            $obCmbOrgaoOrcamento->setLabel(true);
            $obCmbOrgaoOrcamento->setNull(true);
            $obCmbUnidadeOrcamento->setLabel(true);
            $obCmbUnidadeOrcamento->setNull(true);
            Sessao::write('inCodUnidadeOrcamentaria', $inCodUnidadeOrcamento);
            $obCmbClassificacao->setLabel(true);
            $obCmbClassificacao->setNull(true);
            Sessao::write('inCodClassificacao', $stCodClassificacao);
            $obBscFornecedor->setLabel(true);
            $obTxtDescricao->setLabel(true);
        } else if ($inCodDespesa != '') {
            Sessao::write('inCodUnidadeOrcamentaria', $inCodUnidadeOrcamento);
            $jsOnLoad .= "montaParametrosGET('montaOrgaoUnidade', 'hdnCodOrgao,hdnCodUnidade,inCodDespesa');";
        }
    } else {
        $obFormulario->addComponenteComposto($obTxtCodEntidade, $obCmbNomEntidade);
        $obFormulario->addComponente($obDtAutorizacao);
        $obMontaAssinaturas->setOpcaoAssinaturas(false);
    }

    $obCentroCustoUsuario = new IPopUpCentroCustoUsuario($obForm);
    $obCentroCustoUsuario->setNull  (true);
    $obCentroCustoUsuario->setRotulo('Centro de Custo');
    $obCentroCustoUsuario->obCampoCod->setId ('inCodCentroCusto');

    $obFormulario->addHidden    ($obHdnVlTotalAutorizacao);
    $obFormulario->addHidden    ($obHdnBoModuloEmpenho);
    $obFormulario->addComponente($obBscDespesa);
    $obFormulario->addComponente($obCmbClassificacao);
    $obFormulario->addSpan      ($obSpanSaldo);
    $obFormulario->addComponente($obCmbOrgaoOrcamento);
    $obFormulario->addComponente($obCmbUnidadeOrcamento);
    $obFormulario->addComponente($obBscFornecedor);
    $obFormulario->addComponente($obCmbCategoriaEmpenho);
    $obFormulario->addSpan      ($obSpanContrapartida);
    $obFormulario->addComponente($obTxtDescricao);
    $obFormulario->addComponente($obCmbHistorico);

    $obMontaAtributos->geraFormulario($obFormulario);

    if ($obREmpenhoAutorizacaoEmpenho->getBoModuloEmpenho() != true || $stAcao != 'alterar') {
        $obFormulario->addTitulo('Insira os Itens da Autorização');
        if (($stAcao == 'material') || ($boItemMaterial)) {
            $obFormulario->addComponente($obBscMaterial);
        }
        $obFormulario->addHidden($obHdnTipoItem);
        $obFormulario->agrupaComponentes($arRadios);
        $obMontaItemUnidade->geraFormulario($obFormulario);
        $obFormulario->addComponente($obTxtNomItem);
        $obFormulario->addComponente($obTxtComplemento);
        $obFormulario->addComponente($obMarca);        
        $obFormulario->addComponente($obCentroCustoUsuario);
        $obFormulario->addComponente($obTxtQuantidade);
        $obFormulario->addComponente($obCmbUnidade);
        $obFormulario->addComponente($obTxtVlUnitario);
        $obFormulario->addComponente($obTxtVlTotal);
        $obFormulario->agrupaComponentes(array($obBtnIncluir, $obBtnLimpar));
    }elseif ( $obREmpenhoAutorizacaoEmpenho->getBoModuloEmpenho() == true AND $stAcao == 'alterar' ) {
        $obFormulario->addTitulo('Itens da Autorização');
        $obFormulario->addComponente($obLblItem); 
        $obFormulario->addComponente($obMarca);
        $obFormulario->agrupaComponentes(array($obBtnAlterar, $obBtnLimpar));
    }

    $obFormulario->addSpan      ($obSpan);
    $obFormulario->addComponente($obLblVlTotal);
    $obFormulario->addSpan      ($obSpanReserva);

    $obMontaAssinaturas->geraFormulario($obFormulario);

    if ($stAcao == 'alterar') {
        if ($inCodCategoria == 2 || $inCodCategoria == 3) {
            $jsOnLoad .= "montaParametrosGET('buscaContrapartida');";
        }

        $stFiltro.= "&pg=".Sessao::read('pg');
        $stFiltro.= "&pos=".Sessao::read('pos');
        $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
        $obFormulario->Cancelar($stLocation);
    } else {
        $obOk = new Ok();
        $obOk->setId('Ok');
        $obOk->obEvento->setOnClick("if (Valida()) { document.frm.submit(); this.disabled = true; }");
        $obLimpar = new Limpar();
        $obLimpar->obEvento->setOnClick('limparTodos();');
        $obFormulario->defineBarra(array($obOk, $obLimpar));
    }
    $obFormulario->show();

    if ($obMontaAssinaturas->getOpcaoAssinaturas()) {
        $jsOnLoad .= "getIMontaAssinaturas();\n";
    }
}
if ($stAcao != 'alterar') {
    $jsOnLoad .= "habilitaCampos('Descricao');\n";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
