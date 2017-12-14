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
    * Pagina de oculto para Incluir Cadastro/Certificação
    * Data de Criação   : 29/09/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * Casos de uso: uc-03.05.13

    $Id: OCManterCertificacao.php 64452 2016-02-23 21:03:45Z arthur $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_HTML."MontaAtributos.class.php";
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";
include_once TLIC."TLicitacaoDocumentoAtributoValor.class.php";
include_once TLIC."TLicitacaoDocumentosAtributos.class.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once TLIC.'TLicitacaoLicitacao.class.php';

$obRCadastroDinamico = new RCadastroDinamico();
$obRCadastroDinamico->setCodCadastro( 1 );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCertificacao";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";
$stJs = '';

function montaListaDocumento($arRecordSet, $stAcao = '')
{
    $pgOcul  = "OCManterCertificacao.php";
    $rsItens = new RecordSet;
    $rsItens->preenche( $arRecordSet );

    if ($stAcao == 'consultar') {
        $obTable = new TableTree();
        $obTable->setArquivo( $pgOcul );
        $obTable->setParametros( array( 'cod_documento' ) );
        $obTable->setComplementoParametros( 'stCtrl=montaLabelAtributos' );
    } else {
        $obTable = new Table();
    }
    $obTable->setRecordSet( $rsItens );

    $obTable->Head->addCabecalho( 'Documento'       , 20 );
    $obTable->Head->addCabecalho( 'Data de Emissão' , 15 );
    $obTable->Head->addCabecalho( 'Data de Validade', 20 );

    $obTable->Body->addCampo( 'nom_documento'   , 'E' );
    $obTable->Body->addCampo( 'data_emissao'    , 'C' );
    $obTable->Body->addCampo( 'data_validade'   , 'C' );

    if ($stAcao != 'consultar') {
        $obTable->Body->addAcao( 'alterar' , 'JavaScript:executaFuncaoAjax(\'%s\' , \'&id=%s\' )', array( 'alterarItem', 'id' ) );
        $obTable->Body->addAcao( 'excluir' , 'JavaScript:executaFuncaoAjax(\'%s\' , \'&id=%s\' )', array( 'excluirItem', 'id' ) );
    }

    $obTable->montaHTML();

    $stHTML = str_replace( "\n" ,"" ,$obTable->getHTML() );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "d.getElementById('spnDocumentos').innerHTML = '".$stHTML."';";

    return $stJs;
}

function abilitaCampos($acao)
{
    if ($acao != 'alterar') {
        $stJs  = "f.inCodFornecedor.readonly = false;                           \n";
        $stJs .= "d.getElementById('imgFornecedor').style.display = 'inline';   \n";
        $stJs .= "f.dtDataRegistro.readonly  = false;                           \n";
        $stJs .= "f.dtDataVigencia.readonly  = false;                           \n";
    }
    $stJs .= "f.stObservacao.disabled    = false;                               \n";

    return $stJs;
}

function desabilitaCampos($acao)
{
    if ($acao != 'alterar') {
        $stJs  = "f.inCodFornecedor.readonly = true;                            \n";
        $stJs .= "d.getElementById('imgFornecedor').style.display = 'none';     \n";
        $stJs .= "f.dtDataRegistro.readonly  = true;                            \n";
        $stJs .= "f.dtDataVigencia.readonly  = true;                            \n";
    }
    $stJs .= "f.stDataValidade.disabled = 'disabled';                           \n";
    $stJs .= "f.inNumDiasValido.disabled = 'disabled';                          \n";
    $stJs .= "f.hdnObservacao.value = f.stObservacao.value;                     \n";
    $stJs .= "f.stObservacao.disabled    = true;                                \n";

    return $stJs;
}

function montaAtributos($documento)
{
    $obRCadastroDinamico = new RCadastroDinamico();
    $obRCadastroDinamico->setCodCadastro( 1 );

    if ($documento) {
        $obRCadastroDinamico->setPersistenteAtributos  (  new TLicitacaoDocumentosAtributos() );

        $obRCadastroDinamico->setChavePersistenteValores( array("cod_documento" => $documento ) );
        $obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributo_"  );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );

        $obFormulario = new Formulario;
        $obMontaAtributos->geraFormulario($obFormulario);
        $obFormulario->montaInnerHtml();
        $stHTML = $obFormulario->getHTML();

        $stJs  = "d.getElementById('spnAtributos').innerHTML = '".$stHTML."';   \n";
    } else {
        $stJs  = "d.getElementById('spnAtributos').innerHTML = '';              \n";
    }

    return $stJs;
}

function montaLabelAtributos($documento)
{
    include_once TLIC."TLicitacaoDocumentosAtributos.class.php";
    $obTLicitacaoDocumentosAtributos = new TLicitacaoDocumentosAtributos();
    $obTLicitacaoDocumentosAtributos->setDado( 'cod_documento', $documento );
    $obTLicitacaoDocumentosAtributos->recuperaAtributo( $rsAtributos );
    if ( $rsAtributos->getNumLinhas() > 0 ) {
        $obFormulario = new Formulario();
        $obFormulario->addTitulo( 'Atributos' );
        $obLabel = new Label();
        while ( !$rsAtributos->eof() ) {
            $obLabel->setId( 'labelAtributo' );
            $obLabel->setRotulo( $rsAtributos->getCampo('nom_atributo') );
            $obLabel->setValue( $rsAtributos->getCampo('valor') );
            $obFormulario->addComponente( $obLabel );
            $rsAtributos->proximo();
        }

        $obFormulario->montaHTML();
        $stHTML = $obFormulario->getHTML();
    } else {
        $stHTML = "Este documento não possui atributos!";
    }

    return $stHTML;
}

function sincronizarDataValidaDocumento($inDiasValidos, $inDataEmissao)
{
    if ($inDataEmissao != "") {
        if ($inDiasValidos != "") {
            $diasValidos = $inDiasValidos;
        } else {
            $diasValidos = 0;
        }

        $arDataEmissao = explode('/',$inDataEmissao);
        //defino data de emissao
        $ano = $arDataEmissao[2];
        $mes = $arDataEmissao[1];
        $dia = $arDataEmissao[0];

        $dataEmissao = mktime(0,0,0,$mes,$dia,$ano);

        $dataValidade = strftime("%d/%m/%Y" , strtotime("+".$diasValidos." days",$dataEmissao));

        $stJs .= "jQuery('#stDataValidade').val('".$dataValidade."');   \n";
        $stJs .= "jQuery('#inNumDiasValido').val('".$diasValidos."');   \n";
        echo $stJs;
    }
}

function sincronizaDiasValidosDocumento($inDataValidade, $inDataEmissao)
{
    $stJs = "";

    if (strlen($inDataValidade) == 10) {

        if ($inDataValidade != "") {
            $arDataValidade = explode('/',$inDataValidade);
            $dataValidade = $inDataValidade;
        } else {
            $arDataValidade = explode('/',date('d/m/Y'));
            $dataValidade = date('d/m/Y');
        }

         //defino data de validade
        $ano1 = $arDataValidade[2];
        $mes1 = $arDataValidade[1];
        $dia1 = $arDataValidade[0];

        //defino data de emissão
        $arDtEmissao = explode('/',$inDataEmissao);
        $ano2 = $arDtEmissao[2];
        $mes2 = $arDtEmissao[1];
        $dia2 = $arDtEmissao[0];

        //calculo timestam das duas datas
        $timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
        $timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2);

        //diminuo a uma data a outra
        $segundos_diferenca = $timestamp1 - $timestamp2;

        //converto segundos em dias
        $diasValido = $segundos_diferenca / (60 * 60 * 24);

        //obtenho o valor absoluto dos dias (tiro o possível sinal negativo)
        $diasValido = abs($diasValido);

        //tiro os decimais aos dias de diferenca
        $diasValido = floor($diasValido);

        $stJs .= "jQuery('#stDataValidade').val('');                    \n";
        $stJs .= "jQuery('#stDataValidade').val('".$dataValidade."');   \n";
        $stJs .= "jQuery('#inNumDiasValido').val('".$diasValido."');    \n";
    } else {
        $stJs .= "jQuery('#stDataValidade').val('');                    \n";
        $stJs .= "jQuery('#inNumDiasValido').val('');                   \n";
    }

    echo $stJs;
}

function preencheLicitacao($rsRecordSet, $stComponente, $inCodLicitacao)
{
    $stJs = "limpaSelect(f.".$stComponente.",1);                                                    \n";
    
    while ( !$rsRecordSet->eof() ) {
        $stJs .= "f.".$stComponente."[".$rsRecordSet->getCorrente()."] = new Option('".$rsRecordSet->getCampo('cod_licitacao')."','".$rsRecordSet->getCampo('cod_licitacao')."'); \n";
        
        if ( $rsRecordSet->getCampo('cod_licitacao') == $inCodLicitacao ){
            $stJs .= "f.".$stComponente."[".$rsRecordSet->getCorrente()."].selected = 'selected';   \n";
        }
        
        $rsRecordSet->proximo();
    }

    return $stJs;
}

function validaFornecedorLicitacao () {
    global $request;
    
    $stJs = "";
    
    $stExercicioLicitacao = $request->get('stExercicioLicitacao');
    $inCodModalidade      = $request->get('inCodModalidade');
    $inCodEntidade        = $request->get('inCodEntidade');
    $inCodLicitacao       = $request->get('inCodLicitacao');
    $inCodFornecedor      = $request->get('inCodFornecedor');
    
    if (!empty($stExercicioLicitacao) && !empty($inCodModalidade) && !empty($inCodEntidade) && !empty($inCodLicitacao) && !empty($inCodFornecedor) ) {
    
        $obTLicitacaoLicitacao = new TLicitacaoLicitacao();
        $obTLicitacaoLicitacao->setDado( 'exercicio'     , $stExercicioLicitacao );
        $obTLicitacaoLicitacao->setDado( 'cod_entidade'  , $inCodEntidade );
        $obTLicitacaoLicitacao->setDado( 'cod_modalidade', $inCodModalidade );
        
        $stFiltro .= " AND ll.cod_licitacao IN ( SELECT cod_licitacao
                                                   FROM licitacao.participante_certificacao_licitacao
                                                  WHERE cod_licitacao  = ".$inCodLicitacao."
                                                    AND cod_modalidade = ".$inCodModalidade."
                                                    AND cod_entidade   = ".$inCodEntidade."
                                                    AND cgm_fornecedor = ".$inCodFornecedor."
                                                ) ";
        
        $obTLicitacaoLicitacao->recuperaLicitacao( $rsLicitacao, $stFiltro );

        if ( $rsLicitacao->getNumLinhas() > 0 ) {
            $stJs .= "alertaAviso('Fornecedor (".$inCodFornecedor.") já foi certificado para a licitação (".$inCodLicitacao.").','form','erro','".Sessao::getId()."');";
        }
    }

    return $stJs;
}

switch ($request->get('stCtrl')) {
    case "incluirDocumento":
        $arDocs = Sessao::read('arDocs');

        if ( is_array($arDocs) ) {
            $stCampoFoco = "inDocumento";

            include_once TLIC."TLicitacaoDocumento.class.php";
            $obTLicitaoDocumento = new TLicitacaoDocumento();
            $obTLicitaoDocumento->setDado('cod_documento', $request->get('inDocumento'));
            $obTLicitaoDocumento->recuperaPorChave( $rsDocumento );

            include_once TCOM."TComprasFornecedor.class.php";
            $obTComprasFornecedor = new TComprasFornecedor();
            $obTComprasFornecedor->setDado('cod_fornecedor', $request->get('inCodFornecedor'));
            $obTComprasFornecedor->recuperaPorChave( $rsFornecedor );

            foreach ($arDocs as $key => $value) {
                if (( $value['cod_documento'] == $request->get('inDocumento') ) && ( $value['cgm_fornecedor'] == $rsFornecedor->getCampo('cgm_fornecedor') ) && ( $request->get('stAcaoSessao') != 'alterar' ) ) {
                    $stMensagem = "Já existe o mesmo documento para este fornecedor.";
                    break;
                }
            }

            if ($request->get('stAcaoSessao') == 'alterar') {
                $inCount = $request->get('id')-1;
            } else {
                $inCount = count($arDocs);
            }

            if ( ( sistemaLegado::comparaDatas($request->get('stDataEmissao'), $request->get('stDataValidade') ) ) ) {
                $stCampoFoco = 'stDataValidade';
                $stMensagem = 'A data de validade do documento deve ser maior ou igual que a data de emissão.';
            } elseif ( sistemaLegado::comparaDatas( $request->get('stDataEmissao'),date('d/m/Y')) ) {
                $stCampoFoco = 'stDataEmissao       ';
                $stMensagem = 'A data de emissão deve ser menor ou igual a data de hoje.';
            } elseif (sistemaLegado::comparaDatas( $request->get('dtHdnDataRegistro'),$request->get('stDataValidade') )) {
                $stCampoFoco = 'stDataValidade';
                $stMensagem = 'A data de validade deve ser maior ou igual a data de registro.';
            }

            if (($request->get('stDataValidade') == '') && ($request->get('inNumDiasValido') == '')) {
                $stMensagem = 'A data de validade ou o número de dias para o vencimento do documento devem ser informados';
            }

            $dataValidade = $request->get('stDataValidade');

            if (!$stMensagem) {
                $arDocs[$inCount]['id']             = $inCount+1;
                $arDocs[$inCount]['cod_documento']  = $request->get('inDocumento');
                $arDocs[$inCount]['nom_documento']  = $rsDocumento->getCampo('nom_documento');
                $arDocs[$inCount]['num_documento']  = $request->get('inNumDocumento');
                $arDocs[$inCount]['data_emissao']   = $request->get('stDataEmissao');
                $arDocs[$inCount]['data_validade']  = $dataValidade;
                $arDocs[$inCount]['cod_fornecedor'] = $request->get('inCodFornecedor');
                $arDocs[$inCount]['cgm_fornecedor'] = $rsFornecedor->getCampo('cgm_fornecedor');

                $obRCadastroDinamico->setPersistenteAtributos ( new TLicitacaoDocumentosAtributos() );

                $obRCadastroDinamico->setChavePersistenteValores( array("cod_documento" => $request->get('inDocumento') ) );
                $obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

                if ( $rsAtributos->getNumLinhas() > 0 ) {
                    $arAtributos = array();
                    while ( !$rsAtributos->eof() ) {
                            $stChave = 'Atributo_'.$rsAtributos->getCampo('cod_atributo').'_'.$rsAtributos->getCampo('cod_cadastro');
                            $arAtributos[$stChave] = $request->get($stChave);
                            $rsAtributos->proximo();
                    }
                    $arDocs[$inCount]['atributos'] = $arAtributos;
                }

                Sessao::write('arDocs' , $arDocs);

                $stJs .= montaListaDocumento( $arDocs );
                $stJs .= "f.btIncluirDocumento.value='Incluir';                                 \n";
                $stJs .= "executaFuncaoAjax('limpaDocumento');                                  \n";
                $stJs .= "f.stAcaoSessao.value = '';                                            \n";
                $stJs .= desabilitaCampos( $request->get('stAcao') );
            } else {
                $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');   \n";
                $stJs .= "f.".$stCampoFoco.".focus();                                           \n";
            }
        }
    break;

    case "sincronizaDataValida":
        sincronizarDataValidaDocumento($request->get('inNumDiasValido'), $request->get('stDataEmissao'));
    break;

    case "sincronizaDiasValidos":
        sincronizaDiasValidosDocumento($request->get('stDataValidade'), $request->get('stDataEmissao'));
    break;

    case "limpaDocumento":
        $stJs  = "f.stDocumento.selectedIndex = 0;                                  \n";
        $stJs .= "f.inDocumento.value = '';                                         \n";
        $stJs .= "f.inNumDocumento.value = '';                                      \n";
        $stJs .= "f.stDataEmissao.value = '';                                       \n";
        $stJs .= "f.stDataValidade.value = '';                                      \n";
        $stJs .= "f.inNumDiasValido.value = '';                                     \n";
        $stJs .= "document.getElementById('btIncluirDocumento').value='Incluir';    \n";
        $stJs .= "f.stAcaoSessao.value = 'incluir';                                 \n";
        $stJs .= "executaFuncaoAjax('montaAtributos');                              \n";
    break;

    case "montaAtributos":
        $stJs = montaAtributos( $request->get('inDocumento') );
    break;

    case "montaLabelAtributos":
        echo montaLabelAtributos( $request->get('cod_documento') );
    break;

    case "alterarItem":
        $arDocs = Sessao::read('arDocs');

        foreach ($arDocs as $key => $value) {
            if ($request->get('id') == $value['id']) {
                $inDataValidade = $value['data_validade'];
                $inDataEmissao = $value['data_emissao'];

                $stJs  = montaAtributos( $value['cod_documento'] );
                $arEmissao = explode('/', $value['data_emissao']);
                $arValidade = explode('/', $value['data_validade']);
                $stJs .= "f.inDocumento.value = ".$value['cod_documento'].";                            \n";
                $stJs .= "f.stDocumento.value = ".$value['cod_documento'].";                            \n";
                $stJs .= "f.inNumDocumento.value = '".$value['num_documento']."';                       \n";
                $stJs .= "f.stDataEmissao.value = '".$arEmissao[0].$arEmissao[1].$arEmissao[2]."';      \n";
                $stJs .= "mascaraData(f.stDataEmissao,'');                                              \n";
                $stJs .= "f.stDataValidade.value = '".$arValidade[0].$arValidade[1].$arValidade[2]."';  \n";
                $stJs .= "mascaraData(f.stDataValidade,'');                                             \n";
                $stJs .= "f.btIncluirDocumento.value='Alterar';                                         \n";
                $stJs .= "f.stDataValidade.disabled = '';                                               \n";
                $stJs .= "f.inNumDiasValido.disabled = '';                                              \n";
                $stJs .= sincronizaDiasValidosDocumento($inDataValidade,$inDataEmissao);

                if ( is_array($value['atributos']) ) {
                    foreach ($value['atributos'] as $key => $value) {
                        $stJs .= "f.".$key.".value = '".$value."';                                      \n";
                    }
                }
                break;
            }
        }

        $stJs .= "f.stAcaoSessao.value = 'alterar';                                                     \n";
        $stJs .= "f.id.value = ".$request->get('id').";                                                 \n";
    break;

    case "excluirItem":
        $arDocs = Sessao::read('arDocs');
        $arTemp = array();
        $inCount = 0;
        foreach ($arDocs as $key => $value) {
            if ($request->get('id') != $value['id']) {
                $arTemp[$inCount]['id'] = $inCount+1;
                $arTemp[$inCount]['cod_documento' ] = $value['cod_documento'];
                $arTemp[$inCount]['nom_documento' ] = $value['nom_documento'];
                $arTemp[$inCount]['num_documento' ] = $value['num_documento'];
                $arTemp[$inCount]['data_emissao'  ] = $value['data_emissao'];
                $arTemp[$inCount]['data_validade' ] = $value['data_validade'];
                $arTemp[$inCount]['cod_fornecedor'] = $value['cod_fornecedor'];
                $arTemp[$inCount]['cgm_fornecedor'] = $value['cgm_fornecedor'];
                $arTemp[$inCount]['atributos']      = $value['atributos'];
                $inCount++;
            }
        }
        Sessao::write('arDocs' , $arTemp);
        $stJs = montaListaDocumento( Sessao::read('arDocs') );
    break;

    case "desabilitaCampos":
    break;

    case "abilitaCampos":
    break;

    case "montaAlteracao":
        $arAtributos = array();
        Sessao::write('arDocs' , array());
        $inCount = 0;

        include_once TLIC."TLicitacaoDocumentoAtributoValor.class.php";
        $obTLicitacaoDocumentoAtributoValor = new TLicitacaoDocumentoAtributoValor();

        include_once TLIC."TLicitacaoCertificacaoDocumentos.class.php";
        $obTLicitacaoCertificacaoDocumentos = new TLicitacaoCertificacaoDocumentos();

        $obTLicitacaoCertificacaoDocumentos->setDado( 'num_certificacao', $request->get('inNumCertificacao') );
        $obTLicitacaoCertificacaoDocumentos->setDado( 'exercicio', $request->get('stExercicio') );
        $obTLicitacaoCertificacaoDocumentos->setDado( 'cgm_fornecedor', $request->get('inCodFornecedor') );
        $stFiltro = $obTLicitacaoCertificacaoDocumentos->recuperaFiltroDocumentos();
        $obTLicitacaoCertificacaoDocumentos->recuperaDocumentos( $rsDocumento,$stFiltro );
        $obTLicitacaoDocumentoAtributoValor->TLicitacaoCertificacaoDocumentos = & $obTLicitacaoCertificacaoDocumentos;
        while ( !$rsDocumento->eof() ) {
            $arAtributos = array();
            $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_documento', $rsDocumento->getCampo( 'cod_documento' ) );
            $obTLicitacaoDocumentoAtributoValor->recuperaPorChave( $rsAtributos );
            while ( !$rsAtributos->eof() ) {
                $stChave = 'Atributo_'.$rsAtributos->getCampo('cod_atributo').'_'.$rsAtributos->getCampo('cod_cadastro');
                $arAtributos[$stChave] = $rsAtributos->getCampo('valor');
                $rsAtributos->proximo();
            }

            $arDocs[$inCount]['id'] = $inCount+1;
            $arDocs[$inCount]['cod_documento']  = $rsDocumento->getCampo('cod_documento');
            $arDocs[$inCount]['nom_documento']  = $rsDocumento->getCampo('nom_documento');
            $arDocs[$inCount]['num_documento']  = $rsDocumento->getCampo('num_documento');
            $arDocs[$inCount]['data_emissao']   = $rsDocumento->getCampo('dt_emissao');
            $arDocs[$inCount]['data_validade']  = $rsDocumento->getCampo('dt_validade');
            $arDocs[$inCount]['cgm_fornecedor'] = $rsDocumento->getCampo('cgm_fornecedor');
            $arDocs[$inCount]['atributos']      = $arAtributos;

            $rsDocumento->proximo();
            $inCount++;
        }
        Sessao::write('arDocs' , $arDocs);

        $stJs = montaListaDocumento( $arDocs, $request->get('stAcao') );
    break;

    case 'montaDetalheDocumento':
    break;

    case 'carregaModalidade':
        if ( !$request->get('stExercicioLicitacao') && !$request->get('inCodEntidade') ) {
            $stJs.= "f.inCodModalidade.value = '';      \n";
            $stJs.= "f.inCodLicitacao.value = '';       \n";
            $stJs.= "limpaSelect(f.inCodLicitacao,1);   \n";
        }
    break;

    case 'carregaLicitacao':
        $stExercicioLicitacao = $request->get('stExercicioLicitacao', Sessao::getExercicio() );
        
        if ($stExercicioLicitacao && $request->get('inCodEntidade') && $request->get('inCodModalidade')) {
            $obTLicitacaoLicitacao = new TLicitacaoLicitacao();
            $obTLicitacaoLicitacao->setDado( 'exercicio'     , $stExercicioLicitacao );
            $obTLicitacaoLicitacao->setDado( 'cod_entidade'  , $request->get('inCodEntidade') );
            $obTLicitacaoLicitacao->setDado( 'cod_modalidade', $request->get('inCodModalidade') );

            $stFiltro = "";

            if ($request->get('stCtrl') == 'carregaLicitacaoContrato') {
                $stFiltro.="
                      AND EXISTS (
                           SELECT 1
                                 FROM licitacao.homologacao
                            LEFT JOIN licitacao.homologacao_anulada
                                   ON  homologacao_anulada.num_homologacao = homologacao.num_homologacao
                                  AND  homologacao_anulada.num_adjudicacao = homologacao.num_adjudicacao
                                  AND  homologacao_anulada.cod_entidade = homologacao.cod_entidade
                                  AND  homologacao_anulada.cod_modalidade = homologacao.cod_modalidade
                                  AND  homologacao_anulada.cod_licitacao = homologacao.cod_licitacao
                                  AND  homologacao_anulada.exercicio_licitacao = homologacao.exercicio_licitacao
                                  AND  homologacao_anulada.cod_item = homologacao.cod_item
                                  AND  homologacao_anulada.cod_cotacao = homologacao.cod_cotacao
                                  AND  homologacao_anulada.lote = homologacao.lote
                                  AND  homologacao_anulada.exercicio_cotacao = homologacao.exercicio_cotacao
                                  AND  homologacao_anulada.cgm_fornecedor = homologacao.cgm_fornecedor
                                WHERE  homologacao.cod_entidade = ll.cod_entidade
                                  AND  homologacao.cod_modalidade = ll.cod_modalidade
                                  AND  homologacao.cod_licitacao = ll.cod_licitacao
                                  AND  homologacao.exercicio_licitacao = ll.exercicio
                                  AND  homologacao_anulada.num_homologacao is null
                                )";
            }
            
            if ($request->get('stFiltro') == 'adjudicacao' || $request->get('stFiltro') == 'julgamento') {
                
                $stFiltro.= "
                    -- Para as modalidades 1,2,3,4,5,6,7,10,11 é obrigatório exister um edital
                    AND CASE WHEN ll.cod_modalidade in (1,2,3,4,5,6,7,10,11) THEN
                            
                              EXISTS (
                                    SELECT 1
                                      FROM licitacao.edital
                                     WHERE edital.cod_licitacao = ll.cod_licitacao
                                       AND edital.cod_modalidade = ll.cod_modalidade
                                       AND edital.cod_entidade = ll.cod_entidade
                                       AND edital.exercicio = ll.exercicio
                            )
        
                      -- Para as modalidades 8,9 é facultativo possuir um edital
                      WHEN ll.cod_modalidade in (8,9) THEN
                            
                               EXISTS (
                                    SELECT 1
                                      FROM licitacao.edital
                                     WHERE edital.cod_licitacao = ll.cod_licitacao
                                       AND edital.cod_modalidade = ll.cod_modalidade
                                       AND edital.cod_entidade = ll.cod_entidade
                                       AND edital.exercicio = ll.exercicio
                                     )
        
                        OR NOT EXISTS (
                               SELECT 1
                                 FROM licitacao.edital
                                WHERE edital.cod_licitacao = ll.cod_licitacao
                                  AND edital.cod_modalidade = ll.cod_modalidade
                                  AND edital.cod_entidade = ll.cod_entidade
                                  AND edital.exercicio = ll.exercicio
                                )
                     END
                     
                     AND EXISTS ( SELECT 1
                                    FROM compras.mapa_cotacao
                              INNER JOIN compras.julgamento
                                      ON julgamento.exercicio   = mapa_cotacao.exercicio_cotacao
                                     AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                                   WHERE mapa_cotacao.cod_mapa       = ll.cod_mapa
                                     AND mapa_cotacao.exercicio_mapa = ll.exercicio_mapa
                                     AND NOT EXISTS ( SELECT 1
                                                        FROM compras.cotacao_anulada
                                                       WHERE cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                                                         AND cotacao_anulada.exercicio   = mapa_cotacao.exercicio_cotacao
                                                    )
                                 )";
    
                if ($request->get('stFiltro') == 'adjudicacao') {
                    $stFiltro.= "
                     AND EXISTS (
                              SELECT 1
                                FROM compras.mapa_cotacao
                          INNER JOIN licitacao.adjudicacao
                                  ON adjudicacao.exercicio_cotacao   = mapa_cotacao.exercicio_cotacao
                                 AND adjudicacao.cod_cotacao         = mapa_cotacao.cod_cotacao
                                 AND adjudicacao.cod_licitacao       = ll.cod_licitacao
                                 AND adjudicacao.cod_modalidade      = ll.cod_modalidade
                                 AND adjudicacao.cod_entidade        = ll.cod_entidade
                                 AND adjudicacao.exercicio_licitacao = ll.exercicio
                     )
                     AND (SELECT count(1)
                            FROM compras.mapa_cotacao
                      INNER JOIN licitacao.adjudicacao
                              ON adjudicacao.exercicio_cotacao   = mapa_cotacao.exercicio_cotacao
                             AND adjudicacao.cod_cotacao         = mapa_cotacao.cod_cotacao
                             AND adjudicacao.cod_licitacao       = ll.cod_licitacao
                             AND adjudicacao.cod_modalidade      = ll.cod_modalidade
                             AND adjudicacao.cod_entidade        = ll.cod_entidade
                             AND adjudicacao.exercicio_licitacao = ll.exercicio
                           WHERE adjudicacao.adjudicado
                         ) = (SELECT COUNT(1)
                                FROM compras.mapa_cotacao
                          INNER JOIN licitacao.adjudicacao
                                  ON adjudicacao.exercicio_cotacao   = mapa_cotacao.exercicio_cotacao
                                 AND adjudicacao.cod_cotacao         = mapa_cotacao.cod_cotacao
                                 AND adjudicacao.cod_licitacao       = ll.cod_licitacao
                                 AND adjudicacao.cod_modalidade      = ll.cod_modalidade
                                 AND adjudicacao.cod_entidade        = ll.cod_entidade
                                 AND adjudicacao.exercicio_licitacao = ll.exercicio
                             ) ";
                }
            }
    
            $obTLicitacaoLicitacao->recuperaLicitacao( $rsLicitacao, $stFiltro );
    
            if ( $rsLicitacao->getNumLinhas() > 0 ) {
                $stJs.= preencheLicitacao( $rsLicitacao, 'inCodLicitacao', $request->get('inCodLicitacao') );
            } else {
                $stJs.= "f.inCodLicitacao.selectedIndex =  0;   \n";
                $stJs.= "limpaSelect(f.inCodLicitacao,1);       \n";
            }
            
        } else {
            $stJs = "f.inCodLicitacao.value = '';           \n";
            $stJs.= "limpaSelect(f.inCodLicitacao,1);       \n";
        }
        
    break;

    case "validaFornecedorLicitacao":
        $stJs = validaFornecedorLicitacao();
    break;
    
}

echo $stJs;

?>