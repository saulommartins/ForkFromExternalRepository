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
    * Monta os combos de Assinaturas Configuráveis
    * Data de Criação: 06/11/2007

    * @author Analista: Anderson Konze
    * @author Desenvolvedor: Leopoldo Braga Barreiro

    $Id: IMontaAssinaturas.class.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-xx.xx.xx

*/

class IMontaAssinaturas extends Objeto
{
/**
    * @var Object
    * @access Private
*/
var $obRadioAssinaturasSim;

/**
    * @var Object
    * @access Private
*/
var $obRadioAssinaturasNao;

/**
    * @var Object
    * @access Private
*/
var $obSpnListaAssinaturas;

/**
    * @var Object
    * @access Private
*/
var $obListaAssinaturas;

/**
    * @var String
    * @access Private
*/
var $stCampoEntidades;

/**
    * @var String
    * @access Private
*/
var $stFuncaoJS;

/**
    * @var Boolean
    * @access Private
*/
var $boOpcaoAssinaturas;

/**
    * @var Array
    * @access Private
*/
var $arPapeisDisponiveis;

/**
    * @var String
    * @access Private
*/
var $stFuncaoJSAux;

/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;

/**
    * @var String
    * @access Private
*/
var $stTipoDocumento;

/**
    * @var Integer
    * @access Private
*/
var $inCodDocumento;

/**
    * @var String
    * @access Private
*/
var $stExercicio;

/**
    * @access Public
    * @ param Integer
    * @return void
*/
function setCodEntidade($valor) { $this->inCodEntidade = $valor; }

/**
    * @access Public
    * @ param Boolean
    * @return void
*/
function setOpcaoAssinaturas($valor=false)
{
    $this->boOpcaoAssinaturas = $valor;
    if ($valor == false) {
        $this->obRadioAssinaturasNao->setChecked( true );
        $this->obRadioAssinaturasSim->setChecked( false );
    } else {
        $this->obRadioAssinaturasNao->setChecked( false );
        $this->obRadioAssinaturasSim->setChecked( true );
    }
}

/**
    * @access Public
    * @ param Array
    * @return void
*/
function setPapeisDisponiveis($arValor)
{
    $this->arPapeisDisponiveis = $arValor;
    #sessao->assinaturas['papeis'] = $arValor;
    #Sessao::write('papeis',$arValor);
    $assinaturas['papeis'] = $arValor;
    Sessao::write('assinaturas', $assinaturas);
}

/**
    * @access Public
    * @ param String
    * @return void
*/
function setFuncaoJS($valor='')
{
    if ($valor == '') {
        $this->stFuncaoJS = "
    function getIMontaAssinaturas(obj, papel)
    {
        try {
            if (document.getElementById('stIncluirAssinaturasSim').checked) {
                var stValor = '';
                var idCampo = document.getElementById('" . $this->stCampoEntidades . "');
                var tpCampo = idCampo.type;
                arValor = new Array();
                if (tpCampo == 'select-multiple') {
                    for (var i=0; i<idCampo.options.length; i++) {
                        arValor[i] = idCampo.options[i].value;
                    }
                    stValor = arValor.join(',');
                } else {
                    stValor = idCampo.value;
                }
                if (stValor.length > 0) {
                    if (obj) {
                        if (obj.checked) {
                            if (papel) {
                                ajaxJavaScript('" . CAM_GA_ADM_INSTANCIAS . "processamento/OCIMontaAssinaturas.php?" . Sessao::getId() . "&stCodEntidade=' + stValor + '&stCtrlAs=selecionaAssinatura&assina=' + obj.name + '&papel=' + papel + '', '');
                            } else {
                                ajaxJavaScript('" . CAM_GA_ADM_INSTANCIAS . "processamento/OCIMontaAssinaturas.php?" . Sessao::getId() . "&stCodEntidade=' + stValor + '&stCtrlAs=selecionaAssinatura&assina=' + obj.name, '');
                            }
                        } else {
                            // limpa a combo relacionada à checkbox
                            var id = obj.id.split('_');
                            if (document.getElementById('papel_'+id[1]) != null) {
                                $('papel_'+id[1]).value = 0;
                            }
                            ajaxJavaScript('" . CAM_GA_ADM_INSTANCIAS . "processamento/OCIMontaAssinaturas.php?" . Sessao::getId() . "&stCodEntidade=' + stValor + '&stCtrlAs=retiraAssinatura&assina=' + obj.name, '');
                        }
                    } else {
                        ajaxJavaScript('" . CAM_GA_ADM_INSTANCIAS . "processamento/OCIMontaAssinaturas.php?" . Sessao::getId() . "&stCtrlAs=montaLista&stCodEntidade=' + stValor + '', '');
                    }
                } else {
                    try {
                        document.getElementById('stIncluirAssinaturasNao').checked = true;
                        ajaxJavaScript('" . CAM_GA_ADM_INSTANCIAS . "processamento/OCIMontaAssinaturas.php?" . Sessao::getId() . "&stCtrlAs=limpaLista', '');
                    } catch (e) { }
                    var mensagem = '@Selecione ao menos uma Entidade para exibir a Lista de Assinaturas.';
                    alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');
                }
            } else {
                ajaxJavaScript('" . CAM_GA_ADM_INSTANCIAS . "processamento/OCIMontaAssinaturas.php?" . Sessao::getId() . "&stCtrlAs=limpaLista', '');
            }
        } catch (e) { }
    }
    ";
    } else {
        $this->stFuncaoJS = $valor;
    }
}

/**
    * @access Public
    * @ param String
    * @return void
*/
function setCampoEntidades($valor='inCodEntidade') { $this->stCampoEntidades = $valor; }

/**
    * @access Public
    * @ param String
    * @return void
*/
function setTipoDocumento($stTipoDoc) { $this->stTipoDocumento = $stTipoDoc; }

/**
    * @access Public
    * @ param String
    * @return void
*/
function setExercicio($stExercicio) { $this->stExercicio = $stExercicio; }

/**
    * @access Public
    * @return Integer
*/
function getCodEntidade() { return $this->inCodEntidade; }

/**
    * @access Public
    * @return String
*/
function getCampoEntidades() { return $this->stCampoEntidades; }

/**
    * @access Public
    * @return Boolean
*/
function getOpcaoAssinaturas() { return $this->boOpcaoAssinaturas; }

/**
    * @access Public
    * @return Array
*/
function getPapeisDisponiveis() { return $this->arPapeisDisponiveis;	}

/**
    * @access Public
    * @return String
*/
function getFuncaoJS() { return $this->stFuncaoJS; }

/**
    * @access Public
    * @return String
*/
function getHtml() { return $this->html; }

/**
    * @access Public
    * @return String
*/
function getTipoDocumento() { return $this->stTipoDocumento; }

/**
    * @access Public
    * @return Integer
*/
function getCodDocumento() { return $this->inCodDocumento; }

/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio; }

/**
    * Método Construtor
    * @access Public
    * @param Integer
    * @return Object
*/
function IMontaAssinaturas($inCodEntidade=null, $stAssinaturaConfiguravel = null)
{
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAssinatura.class.php"  );
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAssinaturaModulo.class.php"  );

    //Radios de Inclusão de Assinaturas
    $obRadioAssinaturasSim = new Radio;
    $obRadioAssinaturasSim->setRotulo ( "Incluir Assinaturas" );
    $obRadioAssinaturasSim->setTitle  ( "Informe se o relatório emitirá ou não as assinaturas." );
    $obRadioAssinaturasSim->setName ( "stIncluirAssinaturas" );
    $obRadioAssinaturasSim->setId ( "stIncluirAssinaturasSim" );
    $obRadioAssinaturasSim->setChecked ( false );
    $obRadioAssinaturasSim->setValue ( "sim" );
    $obRadioAssinaturasSim->setLabel ( "Sim" );
    $obRadioAssinaturasSim->setNull ( false );
    $this->setCampoEntidades();
    /* Deve pegar todos os valores selecionados na select multipla: inCodEntidade */
    if ($stAssinaturaConfiguravel) {
        $obRadioAssinaturasSim->obEvento->setOnClick("
            if (document.getElementById('stIncluirAssinaturasSim').checked) {
                var stValor = '';
                var idCampo = document.getElementById('" . $this->stCampoEntidades . "');
                var tpCampo = idCampo.type;
                arValor = new Array();
                if (tpCampo == 'select-multiple') {
                    for (var i=0; i<idCampo.options.length; i++) {
                        arValor[i] = idCampo.options[i].value;
                    }
                    stValor = arValor.join(',');
                } else {
                    stValor = idCampo.value;
                }
                if (stValor.length > 0) {
                    ajaxJavaScript('" . CAM_GA_ADM_INSTANCIAS . "processamento/OCIMontaAssinaturas.php?" . Sessao::getId() . "&stCtrlAs=montaListaConfiguravel&stModulo=".$stAssinaturaConfiguravel."&stCodEntidade=' + stValor + '', '');
                } else {
                    var mensagem = '@Selecione ao menos uma Entidade para exibir a Lista de Assinaturas.';
                    document.getElementById('stIncluirAssinaturasNao').checked = true;
                    alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');
                }
            }
    ");
    } else {
        $obRadioAssinaturasSim->obEvento->setOnClick("getIMontaAssinaturas()");
    }
    $this->obRadioAssinaturasSim = $obRadioAssinaturasSim;
    $obRadioAssinaturasNao = new Radio;
    $obRadioAssinaturasNao->setName ( "stIncluirAssinaturas" );
    $obRadioAssinaturasNao->setId ( "stIncluirAssinaturasNao" );
    $obRadioAssinaturasNao->setChecked ( true );
    $obRadioAssinaturasNao->setValue ( "nao" );
    $obRadioAssinaturasNao->setLabel ( "Não" );
    $obRadioAssinaturasNao->setNull ( false );
    $obRadioAssinaturasNao->obEvento->setOnClick("getIMontaAssinaturas()");
    $this->obRadioAssinaturasNao = $obRadioAssinaturasNao;
    $this->stCampoEntidades = 'inCodEntidade';
    $this->setFuncaoJS();
    $this->arPapeisDisponiveis = null;
    $this->stFuncaoJSAux = "
    function getPapelDisponivel(obj)
    {
        var nom = obj.name;
        var valor = obj.value;
        var i = 0;
        var arParts = nom.split('_');
        if (arParts[1]) {
            try {
                var idCheck = 'assinatura_' + arParts[1];
                var obCheck = document.getElementById(idCheck);
                if (obj.value.length > 0 && obj.value != '0') {
                    for (i=0; i<obj.form.elements.length; i++) {
                        if (obj.form.elements[i].type == 'select-one') {
                            if (obj.form.elements[i].name.substring(0, 5) == 'papel') {
                                if (obj.name != obj.form.elements[i].name && obj.value == obj.form.elements[i].value) {
                                    obj.form.elements[i].value = '0';
                                    var nomCampoAux = obj.form.elements[i].name;
                                    var arParts = nomCampoAux.split('_');
                                    var obCheckAux = document.getElementById('assinatura_' + arParts[1]);
                                    obCheckAux.checked = false;
                                    getIMontaAssinaturas(obCheckAux);
                                }
                            }
                        }
                    }
                    obCheck.checked = true;
                    getIMontaAssinaturas(obCheck, obj.value);
                } else {
                    obCheck.checked = false;
                    getIMontaAssinaturas(obCheck);
                }
            } catch (e) {}
        }
    }";
    $this->stTipoDocumento = '';
    $this->arPapeis = array();
    $obSpnListaAssinaturas = new Span;
    $obSpnListaAssinaturas->setId ( "spnListaAssinaturas" );
    $obSpnListaAssinaturas->setValue ( "" );
    $this->obSpnListaAssinaturas = $obSpnListaAssinaturas;
    $rsAssinaturasModulo = new RecordSet;
    $rsAssinaturas = new RecordSet;
    $obTAssinaturaModulo = new TAdministracaoAssinaturaModulo;
    $obTAssinaturaModulo->setDado( 'exercicio', Sessao::getExercicio() ); 			// Recupera o Exercício da sessão
    if ($inCodEntidade != null) {
        $obTAssinaturaModulo->setDado( 'cod_entidade', $inCodEntidade );		// CodEntidade passado
    }
    $obTAssinaturaModulo->setDado( 'cod_modulo',  Sessao::getModulo() );			// Recupera o módulo da sessão
    $obTAssinaturaModulo->recuperaNumCgmPorModulo( $rsAssinaturasModulo );		// Recupera os numcgm do módulo atual
    if ( $rsAssinaturasModulo->getNumLinhas() > 0 ) {
        $arNumCgmModulo = array();
        while ( !$rsAssinaturasModulo->eof() ) {
            $arNumCgmModulo[] = $rsAssinaturasModulo->getCampo('numcgm');
            $rsAssinaturasModulo->proximo();
        }
        $obTAssinatura = new TAdministracaoAssinatura;
        $stCondicao = " and assinatura.numcgm in ( " .  implode(',', $arNumCgmModulo)  . " ) ";

        $obTAssinatura->recuperaRelacionamento( $rsAssinaturas, $stCondicao );
    }
    if ( $rsAssinaturas->getNumLinhas() > 0 ) {
        $inId = 0;
        #sessao->assinaturas['disponiveis'] = array();
        $assinaturas['disponiveis'] = array();
        Sessao::write('assinaturas', $assinaturas);
        $arAssinaturasDisponiveis = array();
        while ( !$rsAssinaturas->eof() ) {
            $arAssinaturasDisponiveis[$inId]['inId'] = $inId;
            $arAssinaturasDisponiveis[$inId]['inCodEntidade'] = $rsAssinaturas->getCampo('cod_entidade');
            $arAssinaturasDisponiveis[$inId]['inCGM'] = $rsAssinaturas->getCampo('numcgm');
            $arAssinaturasDisponiveis[$inId]['stNomCGM'] = $rsAssinaturas->getCampo('nom_cgm');
            $arAssinaturasDisponiveis[$inId]['stCargo'] = $rsAssinaturas->getCampo('cargo');
            $arAssinaturasDisponiveis[$inId]['stCRC'] = $rsAssinaturas->getCampo('insc_crc');
            $rsAssinaturas->proximo();
            $inId++;
        }
        #Sessao::write('assinaturas_disponiveis', $arAssinaturasDisponiveis);
        $assinaturas['disponiveis'] = $arAssinaturasDisponiveis;
        Sessao::write('assinaturas', $assinaturas);
    }
}

/**
    * @access Public
    * @param Object
    * @return void
*/
function setEventosCmbEntidades(&$obCmbEntidades)
{
    $obCmbEntidades->obSelect1->obEvento->setOnDblClick('getIMontaAssinaturas()');
    $obCmbEntidades->obSelect2->obEvento->setOnDblClick('getIMontaAssinaturas()');
    $obCmbEntidades->obGerenciaSelects->obBotao1->obEvento->setOnClick('getIMontaAssinaturas()');
    $obCmbEntidades->obGerenciaSelects->obBotao2->obEvento->setOnClick('getIMontaAssinaturas()');
    $obCmbEntidades->obGerenciaSelects->obBotao3->obEvento->setOnClick('getIMontaAssinaturas()');
    $obCmbEntidades->obGerenciaSelects->obBotao4->obEvento->setOnClick('getIMontaAssinaturas()');
}

/**
    * @access Public
    * @param Integer
    * @return void
*/
function setCodDocumento($inCodDoc) { $this->inCodDocumento = $inCodDoc; }

/**
    * @access Public
    * @param Object
    * @return void
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->agrupaComponentes( array( $this->obRadioAssinaturasSim, $this->obRadioAssinaturasNao ) );
    $obFormulario->addSpan( $this->obSpnListaAssinaturas );
    $obFormulario->obJavaScript->addFuncao( $this->stFuncaoJS );
    if ( strlen($this->stFuncaoJSAux) > 0 ) {
        $obFormulario->obJavaScript->addFuncao( $this->stFuncaoJSAux );
    }
}

/**
    * @access Public
    * @param Object (&$obFormulario), String (stTipoDoc='')
    * @return void
*/
function geraListaLeituraFormulario(&$obFormulario, $stTipoDoc='')
{
    if ($stTipoDoc != '') {
        $this->stTipoDocumento = $stTipoDoc;
    }

    switch ($this->stTipoDocumento) {
        // Autorização de Empenho
        case 'autorizacao_empenho':
            /* Lista com todas as assinaturas vinculadas ao documento */
            include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenhoAssinatura.class.php" );
            $obTAutorizacaoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
            $obTAutorizacaoAssinatura->setDado( 'exercicio', $this->stExercicio );
            $obTAutorizacaoAssinatura->setDado( 'cod_entidade', $this->inCodEntidade );
            $obTAutorizacaoAssinatura->setDado( 'cod_autorizacao', $this->inCodDocumento );
            $obTAutorizacaoAssinatura->recuperaAssinaturasAutorizacao( $rsAssinatura, 'ORDER BY num_assinatura', '', '' );
        break;
        // Nota de Empenho Por Autorização
        case 'nota_empenho_autorizacao':
            /* Lista com todas as assinaturas vinculadas ao documento */
            include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAssinatura.class.php";
            $obTEmpenhoAssinatura = new TEmpenhoEmpenhoAssinatura;
            $obTEmpenhoAssinatura->setDado( 'exercicio', $this->stExercicio );
            $obTEmpenhoAssinatura->setDado( 'cod_entidade', $this->inCodEntidade );
            $obTEmpenhoAssinatura->setDado( 'cod_empenho', $this->inCodDocumento );
            $obTEmpenhoAssinatura->recuperaAssinaturasEmpenho( $rsAssinatura, 'ORDER BY num_assinatura', '', '' );
        break;
        // Nota de Empenho
        case 'nota_empenho':
            /* Lista com todas as assinaturas vinculadas ao documento */
            include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAssinatura.class.php" );
            $obTEmpenhoAssinatura = new TEmpenhoEmpenhoAssinatura;
            $obTEmpenhoAssinatura->setDado( 'exercicio', $this->stExercicio );
            $obTEmpenhoAssinatura->setDado( 'cod_entidade', $this->inCodEntidade );
            $obTEmpenhoAssinatura->setDado( 'cod_empenho', $this->inCodDocumento );
            $obTEmpenhoAssinatura->recuperaAssinaturasEmpenho( $rsAssinatura, 'ORDER BY num_assinatura', '', '' );
        break;
        // Ordem de Pagamento
        case 'ordem_pagamento':
            /* Lista com todas as assinaturas vinculadas ao documento */
            include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoAssinatura.class.php" );
            $obTOrdemAssinatura = new TEmpenhoOrdemPagamentoAssinatura;
            $obTOrdemAssinatura->setDado( 'exercicio', $this->stExercicio );
            $obTOrdemAssinatura->setDado( 'cod_entidade', $this->inCodEntidade );
            $obTOrdemAssinatura->setDado( 'cod_ordem', $this->inCodDocumento );
            $obTOrdemAssinatura->recuperaAssinaturasOrdem( $rsAssinatura, 'ORDER BY num_assinatura', '', '' );
        break;
    }

    if ( isset($rsAssinatura) && $rsAssinatura->inNumLinhas > 0 ) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsAssinatura );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Entidade");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Nome");
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Cargo");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_entidade" );
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nom_cgm" );
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cargo" );
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();
        $obLista->setTitulo("Assinaturas");
        $obLista->montaInnerHtml();
        $obSpnAssinatura = new Span;
        $obSpnAssinatura->setValue($obLista->getHtml());
        $obSpnAssinatura->setId ("spnAssinatura");
        $obFormulario->addSpan($obSpnAssinatura);
    }
}

/**
    * @access Public
    * @param
    * @return void
*/
function disparaLista()
{
    return "<script type='text/javascript'>try {getIMontaAssinaturas();} catch (e) {}</script>\n";
}

/**
    * @access Public
    * @param String (stTipoDoc)
    * @return void
*/
function definePapeisDisponiveis($stTipoDoc)
{
    if ($stTipoDoc != '') {
        $this->stTipoDocumento = $stTipoDoc;
    }
    switch ($this->stTipoDocumento) {
        case 'autorizacao_empenho':
            $this->arPapeisDisponiveis = array( 0=>'Selecione', 'autorizo'=>'Autorizo', 'autorizoempenho'=>'Autorizo o Empenho' );
        break;
        case 'nota_empenho_autorizacao':
            $this->arPapeisDisponiveis = array( 0=>'Selecione', 'ordenador'=>'Ordenador da Despesa', 'conferido'=>'Conferido', 'contador'=>'Contador', 'paguese'=>'Pague-se' );
        break;
        case 'nota_empenho':
            $this->arPapeisDisponiveis = array( 0=>'Selecione', 'ordenador'=>'Ordenador da Despesa', 'conferido'=>'Conferido', 'contador'=>'Contador', 'paguese'=>'Pague-se' );
        break;
        case 'ordem_pagamento':
            $this->arPapeisDisponiveis = array( 0=>'Selecione', 'visto'=>'Visto', 'ordenador'=>'Ordenador da Despesa', 'tesoureiro'=>'Tesoureiro' );
        break;
        case 'recibo_despesa_extra':
            $this->arPapeisDisponiveis = array( 0=>'Selecione', 'conferido'=>'Conferido', 'contador'=>'Contador', 'ordenador'=>'Ordenador da Despesa', 'tesoureiro'=>'Tesoureiro' );
        break;
        case 'recibo_receita_extra':
            $this->arPapeisDisponiveis = array( 0=>'Selecione', 'tesoureiro'=>'Tesoureiro' );
        break;
    }

    #sessao->assinaturas['papeis'] = $this->arPapeisDisponiveis;
    //Sessao::write('papeis',$this->arPapeisDisponiveis);
    $assinaturas['papeis'] = $this->arPapeisDisponiveis;
    Sessao::write('assinaturas', $assinaturas);
}

}
?>
