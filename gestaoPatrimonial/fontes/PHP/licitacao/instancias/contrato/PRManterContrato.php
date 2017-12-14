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
    * Página Oculto para publicação do contrato
    * Data de Criação   : 10/11/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Rodrigo

    * $Id: PRManterContrato.php 66545 2016-09-16 19:07:08Z lisiane $

    * Casos de uso : uc-03.05.23
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
include_once "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoContrato.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoArquivo.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoAnulado.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoDocumento.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoAditivos.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoLicitacao.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoAditivos.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoPublicacaoContrato.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";
include_once TLIC."TLicitacaoDocumento.class.php";

$stAcao = $request->get('stAcao');

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgGera = "OCGeraContrato.php";

$obTContrato = new TLicitacaoContrato();
$obTLicitacaoContratoLicitacao = new TLicitacaoContratoLicitacao();
$obTContratoAditivos = new TLicitacaoContratoAditivos;
$obTContratoAditivos->obTLicitacaoContrato = &$obTContrato;
$obTContratoDocumento = new TLicitacaoContratoDocumento;
$obTContratoDocumento->obTLicitacaoContrato = &$obTContrato;
$obTContratoAnulado = new TLicitacaoContratoAnulado;
$obTPublicacaoContrato = new TLicitacaoPublicacaoContrato;
$obTLicitacaoContratoArquivo = new TLicitacaoContratoArquivo;

$obErro = new Erro();

if ($request->get('inNumeroContrato') != '') {
    $obTLicitacaoContrato = new TLicitacaoContrato();
    
    if ($stAcao == 'incluir') {
        $obTLicitacaoContrato->recuperaTodos( $rsLicitacaoContrato, " WHERE numero_contrato = '".$request->get('inNumeroContrato')."' AND exercicio = '".$request->get('stExercicioContrato')."' AND cod_entidade = ".$request->get('inCodEntidade'));
        
        if($rsLicitacaoContrato->getNumLinhas() > 0) {
            SistemaLegado::exibeAviso(urlencode('O Número de Contrato '.$request->get('inNumeroContrato').' já está vinculado a outro contrato!'),'n_incluir','erro');
            die;
        }
    } else {
        if($request->get('inNumeroContrato') != $request->get('inHdnNumeroContrato')) {
            $obTLicitacaoContrato->recuperaTodos( $rsLicitacaoContrato, " WHERE numero_contrato = '".$request->get('inNumeroContrato')."' AND exercicio = '".$request->get('stExercicioContrato')."' AND cod_entidade = ".$request->get('inCodEntidade'));
            
            if($rsLicitacaoContrato->getNumLinhas() > 0) {
                SistemaLegado::exibeAviso(urlencode('O Número de Contrato '.$request->get('inNumeroContrato').' já está vinculado a outro contrato!'),'n_incluir','erro');
                die;
            }
        }
    }
}


if($request->get('stExercicioContrato') < $request->get('stExercicioLicitacao')) {
    SistemaLegado::exibeAviso(urlencode('Exercício do Contrato deve ser maior ou igual ao Exercício da Licitação!'),'n_incluir','erro');
    die;
}

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTContrato );
Sessao::getTransacao()->setMapeamento( $obTContratoDocumento );
Sessao::getTransacao()->setMapeamento( $obTContratoAditivos );
Sessao::getTransacao()->setMapeamento( $obTContratoAnulado );
Sessao::getTransacao()->setMapeamento( $obTPublicacaoContrato );
Sessao::getTransacao()->setMapeamento( $obTLicitacaoContratoArquivo );

$arDocumentos = Sessao::read('arDocumentos');
$arValores = Sessao::read('arValores');

switch ($stAcao) {
    case "incluir":

        if (strlen($request->get('nmValorGarantiaExecucao')) < 19) {

            $obTLicitacaoLicitacao = new TLicitacaoLicitacao();
            $obTLicitacaoLicitacao->setDado( 'cod_licitacao'	,$request->get('inCodLicitacao'	) 	);
            $obTLicitacaoLicitacao->setDado( 'cod_modalidade'	,$request->get('inCodModalidade') 	);
            $obTLicitacaoLicitacao->setDado( 'cod_entidade'		,$request->get('inCodEntidade') 	);
            $obTLicitacaoLicitacao->recuperaLicitacao( $rsLicitacao );

            unset($stMensagem);

            $obLicitacaoDocumento   = new TLicitacaoDocumento();
            $obLicitacaoDocumento->recuperaTodos( $rsDocumentosLicitacao,$stFiltro,' ORDER BY nom_documento ' );

            $numDocumentosLicitacao  = count($rsDocumentosLicitacao->arElementos);
            $numDocumentosParticipante = count($arDocumentos);

            //    Exige ao menos um documento, seja lançado manual ou automático pela licitação.
            if ($numDocumentosParticipante == 0 && $numDocumentosLicitacao == 0) {
                $stMensagem = 'É necessário informar todos os documentos do participante!';
            }
            
            // exige que seja ao menos lançado um documento para o participante
            if ($numDocumentosParticipante == 0 && $numDocumentosLicitacao > 0) {
                $stMensagem = 'É necessário informar ao menos um documento do participante!';
            }

            if ( implode(array_reverse(explode('/',$request->get('dtVencimento')))) < implode(array_reverse(explode('/',$request->get('dtAssinatura')))) ) {
                $stMensagem = 'A data de vencimento deve ser igual ou superior a data de assinatura! ('.$request->get('dtAssinatura').')';
            } elseif ( implode(array_reverse(explode('/',$request->get('dtAssinatura')))) <= implode(array_reverse(explode('/',$rsLicitacao->getCampo('dt_licitacao')))) ) {
                $stMensagem = 'A data de assinatura deve ser superior a data de inclusão da licitação! ('.$rsLicitacao->getCampo('dt_licitacao').')';
            } elseif ( implode(array_reverse(explode('/',$request->get('dtInicioExecucao')))) < implode(array_reverse(explode('/',$request->get('dtAssinatura')))) ) {
                $stMensagem = 'A data de Início da execução deve ser igual ou maior a data de assinatura do contrato! ('.$request->get('dtAssinatura').')';
            } elseif ( implode(array_reverse(explode('/',$request->get('dtFimExecucao')))) < implode(array_reverse(explode('/',$request->get('dtInicioExecucao')))) ) {
                $stMensagem = 'A data de Fim de execução deve ser igual ou maior a data de Início da execução! ('.$request->get('dtInicioExecucao').')';
            } elseif ( !strstr($request->get('nmValorGarantiaExecucao'),',')) {
                $stMensagem = 'Valor da Garantia de Execução inválido!';
            }

           if ( count( $arValores ) <= 0 ) {
                $stMensagem = 'É necessário pelo menos um veículo de publicação!';
            } else {
                foreach ($arValores as $arTemp) {
                    if ( implode('',array_reverse(explode('/',$arTemp['dtDataPublicacao']))) < implode(array_reverse(explode('/',$request->get('dtAssinatura')))) ) {
                        $stMensagem = 'A data de publicação do veículo '.$arTemp['inVeiculo'].' deve ser maior ou igual a data de assinatura do contrato!';
                        break;
                    }

                }
            }
            if ($stMensagem == '') {

                $obTContrato->setDado('cod_entidade',$request->get('inCodEntidade'));
                $obTContrato->setDado('exercicio', $request->get('stExercicioContrato'));
                $obTContrato->setDado('cgm_responsavel_juridico',$request->get('inCGM'));
                $obTContrato->setDado('cgm_contratado',$request->get('inCGMContratado'));
                $obTContrato->setDado('cod_documento', 0 );
                $obTContrato->setDado('cod_tipo_documento', 0 );
                $obTContrato->setDado('dt_assinatura', $request->get('dtAssinatura'));
                $obTContrato->setDado('vencimento', $request->get('dtVencimento'));
                $obTContrato->setDado('valor_garantia', str_replace(',','.',str_replace('.','',$request->get('nmValorGarantiaExecucao'))) );
                $obTContrato->setDado('valor_contratado', $request->get('vlContrato') );
                $obTContrato->setDado('inicio_execucao', $request->get('dtInicioExecucao') );
                $obTContrato->setDado('fim_execucao', $request->get('dtFimExecucao') );
                $obTContrato->setDado('cod_tipo_contrato', $request->get('inTipoContrato') );
                $obTContrato->setDado('cod_tipo_instrumento', $request->get('inTipoInstrumento') );
                
                $obTContrato->setDado('num_orgao', $request->get('inNumOrgao') );
                $obTContrato->setDado('num_unidade', $request->get('inNumUnidade') );
                $obTContrato->setDado('numero_contrato', $request->get('inNumeroContrato') );
                $obTContrato->setDado('tipo_objeto', $request->get('inCodTipoObjeto') );
                $obTContrato->setDado('objeto', $request->get('stObjeto') );
                $obTContrato->setDado('forma_fornecimento', $request->get('stFormaFornecimento') );
                $obTContrato->setDado('forma_pagamento', $request->get('stFormaPagamento') );
                $obTContrato->setDado('cgm_signatario', $request->get('inCGMSignatario') );
                $obTContrato->setDado('prazo_execucao', $request->get('stPrazoExecucao') );
                $obTContrato->setDado('multa_rescisoria', $request->get('stMultaRescisoria') );
                $obTContrato->setDado('justificativa', $request->get('stJustificativa') );
                $obTContrato->setDado('razao', $request->get('stRazao') );
                $obTContrato->setDado('fundamentacao_legal', $request->get('stFundamentacaoLegal') );
                $obTContrato->setDado('cgm_representante_legal', $request->get('inCGMRepresentanteLegal') );
                $obTContrato->setDado('multa_inadimplemento', $request->get('stMultaInadimplemento') );
                $obTContrato->setDado('cod_garantia', $request->get('inTipoGarantia') );
                
                
                $obTContrato->inclusao();

                $inCountDocumentos = count($arDocumentos);
                for ($inPosTransf = 0; $inPosTransf < $inCountDocumentos; $inPosTransf++) {
                    $obTContratoDocumento->setDado('cod_documento',$arDocumentos[$inPosTransf]["inCodDocumento"]);
                    $obTContratoDocumento->setDado('num_documento',$arDocumentos[$inPosTransf]["stNumDocumento"]);
                    $obTContratoDocumento->setDado('dt_validade',$arDocumentos[$inPosTransf]["dtValidade"]);
                    $obTContratoDocumento->setDado('dt_emissao', $arDocumentos[$inPosTransf]["dtEmissao"]);
                    $obTContratoDocumento->inclusao();
                }

                //inclui os dados da publicacao do contrato
                foreach ($arValores as $arTemp) {
                    $obTPublicacaoContrato->setDado( 'num_contrato', $obTContrato->getDado('num_contrato') );
                    $obTPublicacaoContrato->setDado( 'numcgm', $arTemp['inVeiculo'] );
                    $obTPublicacaoContrato->setDado( 'dt_publicacao', $arTemp['dtDataPublicacao'] );
                    $obTPublicacaoContrato->setDado( 'num_publicacao',$arTemp['inNumPublicacao'] );
                    $obTPublicacaoContrato->setDado( 'exercicio', $request->get('stExercicioContrato') );
                    $obTPublicacaoContrato->setDado( 'cod_entidade', $request->get('inCodEntidade') );
                    $obTPublicacaoContrato->setDado( 'cod_modalidade', $request->get('inCodModalidade') );
                    $obTPublicacaoContrato->setDado( 'cod_licitacao', $request->get('inCodLicitacao') );
                    $obTPublicacaoContrato->setDado( 'observacao', $arTemp['stObservacao'] );
                    $obTPublicacaoContrato->inclusao();
                }

                $obTLicitacaoContratoLicitacao->setDado('num_contrato', $obTContrato->getDado('num_contrato'));
                $obTLicitacaoContratoLicitacao->setDado('cod_entidade', $obTContrato->getDado('cod_entidade'));
                $obTLicitacaoContratoLicitacao->setDado('exercicio', $request->get('stExercicioContrato') );
                $obTLicitacaoContratoLicitacao->setDado('exercicio_licitacao', $request->get('stExercicioLicitacao'));
                $obTLicitacaoContratoLicitacao->setDado('cod_licitacao',$request->get('inCodLicitacao'));
                $obTLicitacaoContratoLicitacao->setDado('cod_modalidade',$request->get('inCodModalidade'));

                $obTLicitacaoContratoLicitacao->inclusao();

                $obTOrcamentoEntidade = new TOrcamentoEntidade();
                $stFiltro = " AND 	E.cod_entidade = ".$obTContrato->getDado('cod_entidade')." ";
                $obTOrcamentoEntidade->recuperaRelacionamento( $rsEntidade, $stFiltro );

                /***************************************************************************************************************/
                $arArquivos = Sessao::read('arArquivos');

                $arArquivosDigitaisIncluir = array();
                $arArquivosDigitaisExcluir = array();

                $stDirTMP = CAM_GP_LICITACAO."tmp/";
                $stDirANEXO = CAM_GP_LIC_ANEXOS."contrato/";

                foreach($arArquivos AS $chave => $arquivo){
                    if($arquivo['boExcluido'] == 'FALSE'){
                        //Salva Arquivo Digital
                        $arArquivosDigitaisIncluir[] = $arquivo;
                    }elseif($arquivo['boCopiado'] == 'TRUE' && $arquivo['boExcluido'] == 'TRUE'){
                        //Salva Arquivo Digital para Exclusão
                        $arArquivosDigitaisExcluir[] = $arquivo;
                    }
                }

                foreach($arArquivosDigitaisIncluir AS $chave => $arquivo){
                    $obTLicitacaoContratoArquivo = new TLicitacaoContratoArquivo;
                    $obTLicitacaoContratoArquivo->setDado('num_contrato', $obTContrato->getDado('num_contrato'));
                    $obTLicitacaoContratoArquivo->setDado('cod_entidade', $obTContrato->getDado('cod_entidade'));
                    $obTLicitacaoContratoArquivo->setDado('exercicio'   , $obTContrato->getDado('exercicio'));
                    $obTLicitacaoContratoArquivo->setDado('nom_arquivo' , $arquivo['nom_arquivo']);
                    $obTLicitacaoContratoArquivo->setDado('arquivo'     , $arquivo['arquivo']);

                    if( $arquivo['boCopiado'] == 'TRUE' )
                        $obTLicitacaoContratoArquivo->alteracao();
                    else{
                        $obTLicitacaoContratoArquivo->inclusao();

                        $stArquivoTMP   = $stDirTMP.$arquivo['arquivo'];
                        $stArquivoANEXO = $stDirANEXO.$arquivo['arquivo'];

                        if(!copy($stArquivoTMP, $stArquivoANEXO))
                            $obErro->setDescricao("Erro no upload do arquivo(".$arquivo['arquivo'].")!");
                    }

                    if ( $obErro->ocorreu() )
                        break;
                }

                if ( !$obErro->ocorreu() ) {
                    foreach($arArquivosDigitaisExcluir AS $chave => $arquivo){
                        $obTLicitacaoContratoArquivo = new TLicitacaoContratoArquivo;
                        $obTLicitacaoContratoArquivo->setDado('num_contrato', $obTContrato->getDado('num_contrato'));
                        $obTLicitacaoContratoArquivo->setDado('cod_entidade', $obTContrato->getDado('cod_entidade'));
                        $obTLicitacaoContratoArquivo->setDado('exercicio'   , $obTContrato->getDado('exercicio'));
                        $obTLicitacaoContratoArquivo->setDado('nom_arquivo' , $arquivo['nom_arquivo']);
                        $obTLicitacaoContratoArquivo->setDado('arquivo'     , $arquivo['arquivo']);
                        $obTLicitacaoContratoArquivo->exclusao();

                        $stArquivo = $stDirANEXO.$arquivo['arquivo'];
                        if (file_exists($stArquivo)) {
                            if(!unlink($stArquivo))
                                $obErro->setDescricao("Erro ao excluir o arquivo(".$arquivo['nom_arquivo'].")!");
                        }

                        if ( $obErro->ocorreu() )
                            break;
                    }
                }

                if ( !$obErro->ocorreu() ) {
                    $obIterator = new DirectoryIterator($stDirTMP);
                    foreach ( $obIterator as $file ) {
                        $stFile = $file->getFilename();
                        if ($stFile!="index.php" && $stFile!="." && $stFile!="..") {
                            if (file_exists($stDirTMP.$stFile)) {
                                unlink($stDirTMP.$stFile);
                            }
                        }
                    }
                }
                /***************************************************************************************************************/
                SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=$stAcao","Contrato: ".$obTContrato->getDado('numero_contrato')."/".$obTContrato->getDado('exercicio')." da entidade ".$obTContrato->getDado('cod_entidade')." - ".$rsEntidade->getCampo('nom_cgm'),"incluir", "aviso", Sessao::getId(),"");
                Sessao::remove('arDocumentos');

                if ($request->get('boImprimirContrato')) {
                $arRelatorio = array();
                include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";
                $obTEntidade = new TOrcamentoEntidade;
                $obTEntidade->setDado('cod_entidade', $request->get('inCodEntidade'));
                $obTEntidade->setDado('exercicio', Sessao::getExercicio());
                $obTEntidade->recuperaRelacionamentoNomes($rsRelatorio);
                $arRelatorio['nomEntidade'] = $rsRelatorio->getCampo('entidade');
                $arRelatorio['nomPrefeito'] = $rsRelatorio->getCampo('responsavel');
                $obTEntidade = new TCGM;
                $stFiltro = " and CGM.numcgm = ".$rsRelatorio->getCampo('numcgm');;
                $obTEntidade->recuperaRelacionamentoSintetico($rsRelatorio, $stFiltro);
                $arRelatorio['cgcEntidade'] = $rsRelatorio->getCampo('documento');
                include_once CAM_GA_CGM_MAPEAMENTO."TCGM.class.php";
                $obTFornecedor = new TCGM;
                $obTFornecedor->setDado('numcgm',$request->get('inCGMContratado') );
                $obTFornecedor->recuperaRelacionamentoFornecedor($rsFornecedor);

                $arRelatorio['cgcFornecedor'] = $rsFornecedor->getCampo('documento');
                $arRelatorio['cgmFornecedor'] = $rsFornecedor->getCampo('numcgm');
                $arRelatorio['nomFornecedor'] = $rsFornecedor->getCampo('nom_cgm');
                $arRelatorio['nom_logradouro'] = $rsFornecedor->getCampo('tipo_logradouro').' '.$rsFornecedor->getCampo('logradouro').' '.$rsFornecedor->getCampo('numero').' '.$rsFornecedor->getCampo('complemento').', '.$rsFornecedor->getCampo('bairro').', '.$rsFornecedor->getCampo('cidade').'/'.$rsFornecedor->getCampo('uf');
                $arRelatorio['nomRepresentante'] = $request->get('stNomCGM');
                $arRelatorio['cgmRepresentante'] = $request->get('inCGM');
                $arRelatorio['dataInicio'] = $request->get('dtAssinatura');
                $arRelatorio['dataVigencia'] = $request->get('dtVencimento');

                $arRelatorio['numContrato'] = $obTContrato->getDado('num_contrato');
                $arRelatorio['codModalidade'] = $request->get('inCodModalidade');
                $arRelatorio['codLicitacao'] = $request->get('inCodLicitacao');
                $arRelatorio['codEntidade'] = $request->get('inCodEntidade');
                $arRelatorio['descObjeto'] = $request->get('hdnDescObjeto');

                //CONSULTANDO ARQUIVO TEMPLATE
                include_once TADM.'TAdministracaoModeloArquivosDocumento.class.php';
                $obTAdministracaoArquivosDocumento = new TAdministracaoModeloArquivosDocumento();
                $obTAdministracaoArquivosDocumento->setDado( 'cod_acao', Sessao::read('acao') );
                $obTAdministracaoArquivosDocumento->setDado( 'cod_documento', 0);
                $obTAdministracaoArquivosDocumento->recuperaDocumentos( $rsTemplate );
                $arRelatorio['nomDocumentoSxw'] = $rsTemplate->getCampo('nome_arquivo_template');
                Sessao::write('arRelatorio', $arRelatorio);
                SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());
                 }
            } else {
                sistemaLegado::exibeAviso(urlencode($stMensagem),'n_incluir','erro');
            }
        } else {
            sistemaLegado::exibeAviso(urlencode('O valor de garantia informado é maior que o permitido!'),'n_incluir','erro');
        }
    break;

    case "alterar":
        if (strlen($request->get('nmValorGarantiaExecucao')) < 19) {
            $obTLicitacaoLicitacao = new TLicitacaoLicitacao();
            $obTLicitacaoLicitacao->setDado( 'cod_licitacao', $request->get('inCodLicitacao'));
            $obTLicitacaoLicitacao->setDado( 'cod_modalidade', $request->get('inCodModalidade'));
            $obTLicitacaoLicitacao->setDado( 'cod_entidade', $request->get('inCodModalidade'));
            $obTLicitacaoLicitacao->setDado( 'cod_exercicio', Sessao::getExercicio());
            $obTLicitacaoLicitacao->recuperaLicitacao( $rsLicitacao );

            unset($stMensagem);

            if ( implode(array_reverse(explode('/',$request->get('dtVencimento')))) < implode(array_reverse(explode('/',$request->get('dtAssinatura')))) ) {
                $stMensagem = 'A data de vencimento deve ser igual ou superior a data de assinatura! ('.$request->get('dtAssinatura').')';
            } elseif ( SistemaLegado::comparaDatas($rsLicitacao->getCampo('dt_licitacao'),$request->get('dtAssinatura'))) {
                $stMensagem = 'A data de assinatura deve ser superior a data de inclusão da licitação! ('.$rsLicitacao->getCampo('dt_licitacao').')';
            } elseif ( implode(array_reverse(explode('/',$request->get('dtInicioExecucao')))) < implode(array_reverse(explode('/',$request->get('dtAssinatura')))) ) {
                $stMensagem = 'A data de Início da execução deve ser igual ou maior a data de assinatura do contrato! ('.$request->get('dtAssinatura').')';
            } elseif ( implode(array_reverse(explode('/',$request->get('dtFimExecucao')))) < implode(array_reverse(explode('/',$request->get('dtInicioExecucao')))) ) {
                $stMensagem = 'A data de Fim de execução deve ser igual ou maior a data de Início da execução! ('.$request->get('dtInicioExecucao').')';
            } elseif ( !strstr($request->get('nmValorGarantiaExecucao'),',')) {
                $stMensagem = 'Valor da Garantia de Execução inválido!';
            }
            if ( count( $arValores ) <= 0 ) {
                $stMensagem = 'É necessário pelo menos um veículo de publicação!';
            }
            if ( count( $arDocumentos ) <= 0 ) {
                $stMensagem = 'É necessário informar ao menos um documento do participante!';
            } else {
                foreach ($arValores as $arTemp) {
                    if ( implode('',array_reverse(explode('/',$arTemp['dtDataPublicacao']))) < implode(array_reverse(explode('/',$request->get('dtAssinatura')))) ) {
                        $stMensagem = 'A data de publicação do veículo '.$arTemp['inVeiculo'].' deve ser maior ou igual a data de assinatura do contrato!';
                        break;
                    }
                }
            }

            if ($stMensagem == '') {
                $obTContrato->setDado('cod_licitacao',$request->get('inCodLicitacao'));
                $obTContrato->setDado('cod_entidade',$request->get('inCodEntidade'));
                $obTContrato->setDado('cod_modalidade',$request->get('inCodModalidade'));
                $obTContrato->setDado('exercicio', $request->get('stExercicioContrato'));
                $obTContrato->setDado('num_contrato',$request->get('inNumContrato'));
                $obTContrato->setDado('cgm_responsavel_juridico',$request->get('inCGM'));
                $obTContrato->setDado('cgm_contratado',$request->get('inCGMContratado'));
                $obTContrato->setDado('inicio_execucao' , $request->get('dtInicioExecucao') );
                $obTContrato->setDado('fim_execucao'    , $request->get('dtFimExecucao') );
                $obTContrato->setDado('cod_documento', 0 );
                $obTContrato->setDado('cod_tipo_documento', 0);
                $obTContrato->setDado('dt_assinatura', $request->get('dtAssinatura'));
                $obTContrato->setDado('vencimento', $request->get('dtVencimento'));
                $obTContrato->setDado('valor_garantia', str_replace(',','.',str_replace('.','',$request->get('nmValorGarantiaExecucao'))) );
                $obTContrato->setDado('valor_contratado', $request->get('vlContrato'));
                $obTContrato->setDado('cod_tipo_contrato', $request->get('inTipoContrato') );
                $obTContrato->setDado('cod_tipo_instrumento', $request->get('inTipoInstrumento') );

                $obTContrato->setDado('num_orgao', $request->get('inNumOrgao') );
                $obTContrato->setDado('num_unidade', $request->get('inNumUnidade') );
                $obTContrato->setDado('numero_contrato', $request->get('inNumeroContrato') );
                $obTContrato->setDado('tipo_objeto', $request->get('inCodTipoObjeto') );
                $obTContrato->setDado('objeto', $request->get('stObjeto') );
                $obTContrato->setDado('forma_fornecimento', $request->get('stFormaFornecimento') );
                $obTContrato->setDado('forma_pagamento', $request->get('stFormaPagamento') );
                $obTContrato->setDado('cgm_signatario', $request->get('inCGMSignatario') );
                $obTContrato->setDado('prazo_execucao', $request->get('stPrazoExecucao') );
                $obTContrato->setDado('multa_rescisoria', $request->get('stMultaRescisoria') );
                $obTContrato->setDado('justificativa', $request->get('stJustificativa') );
                $obTContrato->setDado('razao', $request->get('stRazao') );
                $obTContrato->setDado('fundamentacao_legal', $request->get('stFundamentacaoLegal') );
                $obTContrato->setDado('cgm_representante_legal', $request->get('inCGMRepresentanteLegal') );
                $obTContrato->setDado('multa_inadimplemento', $request->get('stMultaInadimplemento') );
                $obTContrato->setDado('cod_garantia', $request->get('inTipoGarantia') );
                $obTContrato->alteracao();

                $inCountDocumentos = count($arDocumentos);

                $obTContratoDocumento->exclusao();

                for ($inPosTransf = 0; $inPosTransf < $inCountDocumentos; $inPosTransf++) {
                    $obTContratoDocumento->setDado('cod_documento', $arDocumentos[$inPosTransf]["inCodDocumento"]);
                    $obTContratoDocumento->setDado('num_documento', $arDocumentos[$inPosTransf]["stNumDocumento"]);
                    $obTContratoDocumento->setDado('dt_validade',   $arDocumentos[$inPosTransf]["dtValidade"]);
                    $obTContratoDocumento->setDado('dt_emissao',    $arDocumentos[$inPosTransf]["dtEmissao"]);
                    $obTContratoDocumento->inclusao();
                }

                for ($inPosTransf = 0; $inPosTransf < $inCountDocumentos; $inPosTransf++) {
                    if ($arDocumentos[$inPosTransf]['boAlterado']) {
                        $obTContratoDocumento->setDado('cod_documento', $arDocumentos[$inPosTransf]["inCodDocumento"]);
                        $obTContratoDocumento->setDado('num_documento', $arDocumentos[$inPosTransf]["stNumDocumento"]);
                        $obTContratoDocumento->setDado('dt_validade',   $arDocumentos[$inPosTransf]["dtValidade"]);
                        $obTContratoDocumento->setDado('dt_emissao',    $arDocumentos[$inPosTransf]["dtEmissao"]);
                        $obTContratoDocumento->alteracao();
                    }
                }

                $obTContratoAditivos->exclusao();
                $obTContratoAditivos->setCampoCod('num_contrato');
                $arAditivos = Sessao::read('arAditivos');
                $inCountAditivos = count($arAditivos);

                for ($inPosTransf = 0; $inPosTransf < $inCountAditivos; $inPosTransf++) {
                    $obTContratoAditivos->setDado('cod_norma',$arAditivos[$inPosTransf]["inCodNorma"]);
                    $obTContratoAditivos->setDado('dt_vencimento',$arAditivos[$inPosTransf]["dtVencimento"]);
                    $obTContratoAditivos->inclusao();
                }

                //exclui os veiculos de publicidade existentes
                $obTPublicacaoContrato->setDado( 'num_contrato', $request->get('inNumContrato') );
                $obTPublicacaoContrato->setDado( 'exercicio', $request->get('stExercicioContrato') );
                $obTPublicacaoContrato->setDado( 'cod_entidade', $request->get('inCodEntidade') );
                $obTPublicacaoContrato->exclusao();

                //inclui os veiculos que estao na sessao
                foreach ($arValores as $arTemp) {
                    $obTPublicacaoContrato->setDado( 'numcgm', $arTemp['inVeiculo']);
                    $obTPublicacaoContrato->setDado( 'dt_publicacao', $arTemp['dtDataPublicacao']);
                    $obTPublicacaoContrato->setDado( 'num_publicacao', $arTemp['inNumPublicacao']);
                    $obTPublicacaoContrato->setDado( 'num_licitacao', $request->get('inNumPublicacao'));
                    $obTPublicacaoContrato->setDado( 'cod_modalidade', $request->get('inCodModalidade'));
                    $obTPublicacaoContrato->setDado( 'cod_licitacao', $request->get('inCodLicitacao'));
                    $obTPublicacaoContrato->setDado( 'observacao', $arTemp['stObservacao']);
                    $obTPublicacaoContrato->inclusao();
                }

                include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";
                $obTEntidade = new TOrcamentoEntidade;
                $obTEntidade->setDado('cod_entidade', $request->get('inCodEntidade'));
                $obTEntidade->setDado('exercicio', Sessao::getExercicio());
                $obTEntidade->recuperaRelacionamentoNomes($rsRelatorio);

                /***************************************************************************************************************/
                $arArquivos = Sessao::read('arArquivos');

                $arArquivosDigitaisIncluir = array();
                $arArquivosDigitaisExcluir = array();

                $stDirTMP = CAM_GP_LICITACAO."tmp/";
                $stDirANEXO = CAM_GP_LIC_ANEXOS."contrato/";

                foreach($arArquivos AS $chave => $arquivo){
                    if($arquivo['boExcluido'] == 'FALSE'){
                        //Salva Arquivo Digital
                        $arArquivosDigitaisIncluir[] = $arquivo;
                    }elseif($arquivo['boCopiado'] == 'TRUE' && $arquivo['boExcluido'] == 'TRUE'){
                        //Salva Arquivo Digital para Exclusão
                        $arArquivosDigitaisExcluir[] = $arquivo;
                    }
                }

                foreach($arArquivosDigitaisIncluir AS $chave => $arquivo){
                    $obTLicitacaoContratoArquivo = new TLicitacaoContratoArquivo;
                    $obTLicitacaoContratoArquivo->setDado('num_contrato', $obTContrato->getDado('num_contrato'));
                    $obTLicitacaoContratoArquivo->setDado('cod_entidade', $obTContrato->getDado('cod_entidade'));
                    $obTLicitacaoContratoArquivo->setDado('exercicio'   , $obTContrato->getDado('exercicio'));
                    $obTLicitacaoContratoArquivo->setDado('nom_arquivo' , $arquivo['nom_arquivo']);
                    $obTLicitacaoContratoArquivo->setDado('arquivo'     , $arquivo['arquivo']);

                    if( $arquivo['boCopiado'] == 'TRUE' )
                        $obTLicitacaoContratoArquivo->alteracao();
                    else{
                        $obTLicitacaoContratoArquivo->inclusao();

                        $stArquivoTMP   = $stDirTMP.$arquivo['arquivo'];
                        $stArquivoANEXO = $stDirANEXO.$arquivo['arquivo'];

                        if(!copy($stArquivoTMP, $stArquivoANEXO))
                            $obErro->setDescricao("Erro no upload do arquivo(".$arquivo['arquivo'].")!");
                    }

                    if ( $obErro->ocorreu() )
                        break;
                }

                if ( !$obErro->ocorreu() ) {
                    foreach($arArquivosDigitaisExcluir AS $chave => $arquivo){
                        $obTLicitacaoContratoArquivo = new TLicitacaoContratoArquivo;
                        $obTLicitacaoContratoArquivo->setDado('num_contrato', $obTContrato->getDado('num_contrato'));
                        $obTLicitacaoContratoArquivo->setDado('cod_entidade', $obTContrato->getDado('cod_entidade'));
                        $obTLicitacaoContratoArquivo->setDado('exercicio'   , $obTContrato->getDado('exercicio'));
                        $obTLicitacaoContratoArquivo->setDado('nom_arquivo' , $arquivo['nom_arquivo']);
                        $obTLicitacaoContratoArquivo->setDado('arquivo'     , $arquivo['arquivo']);
                        $obTLicitacaoContratoArquivo->exclusao();

                        $stArquivo = $stDirANEXO.$arquivo['arquivo'];
                        if (file_exists($stArquivo)) {
                            if(!unlink($stArquivo))
                                $obErro->setDescricao("Erro ao excluir o arquivo(".$arquivo['nom_arquivo'].")!");
                        }

                        if ( $obErro->ocorreu() )
                            break;
                    }
                }

                if ( !$obErro->ocorreu() ) {
                    $obIterator = new DirectoryIterator($stDirTMP);
                    foreach ( $obIterator as $file ) {
                        $stFile = $file->getFilename();
                        if ($stFile!="index.php" && $stFile!="." && $stFile!="..") {
                            if (file_exists($stDirTMP.$stFile)) {
                                unlink($stDirTMP.$stFile);
                            }
                        }
                    }
                }
                /***************************************************************************************************************/

                SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Contrato: ".$obTContrato->getDado('numero_contrato')."/".$obTContrato->getDado('exercicio')." da entidade ".$obTContrato->getDado('cod_entidade')." - ". $rsRelatorio->getCampo('entidade'),"alterar", "aviso", Sessao::getId(),"");

                if ($request->get('boImprimirContrato')) {
                    $arRelatorio = array();
                    $arRelatorio['nomEntidade'] = $rsRelatorio->getCampo('entidade');
                    $arRelatorio['nomPrefeito'] = $rsRelatorio->getCampo('responsavel');
                    $obTEntidade = new TCGM;
                    $stFiltro = " and CGM.numcgm = ".$rsRelatorio->getCampo('numcgm');;
                    $obTEntidade->recuperaRelacionamentoSintetico($rsRelatorio, $stFiltro);
                    $arRelatorio['cgcEntidade'] = $rsRelatorio->getCampo('documento');
                    include_once CAM_GA_CGM_MAPEAMENTO."TCGM.class.php";
                    $obTFornecedor = new TCGM;
                    $obTFornecedor->setDado('numcgm',$request->get('inCGMContratado') );
                    $obTFornecedor->recuperaRelacionamentoFornecedor($rsFornecedor, $stFiltro);
                    $arRelatorio['cgcFornecedor'] = $rsFornecedor->getCampo('documento');
                    $arRelatorio['nomFornecedor'] = $request->get('stNomContratado');
                    $arRelatorio['nom_logradouro'] = $rsFornecedor->getCampo('tipo_logradouro').' '.$rsFornecedor->getCampo('logradouro').' '.$rsFornecedor->getCampo('numero').' '.$rsFornecedor->getCampo('complemento').', '.$rsFornecedor->getCampo('bairro').', '.$rsFornecedor->getCampo('cidade').'/'.$rsFornecedor->getCampo('uf');
                    $arRelatorio['nomOrgao'] = '';
                    $arRelatorio['nomUnidade'] = '';
                    $arRelatorio['numContrato'] = $obTContrato->getDado('num_contrato');
                    $arRelatorio['codModalidade'] = $request->get('inCodModalidade');
                    $arRelatorio['codLicitacao'] = $request->get('inCodLicitacao');
                    $arRelatorio['codEntidade'] = $request->get('inCodEntidade');
                    $arRelatorio['descObjeto'] = $request->get('hdnDescObjeto');
                    $arRelatorio['dataInicio'] = $request->get('dtAssinatura');
                    $arRelatorio['dataVigencia'] = $request->get('dtVencimento');
                    $arRelatorio['cgmFornecedor'] = $rsFornecedor->getCampo( 'numcgm' );

                    $obTRepresentante = new TCGM();
                    $obTRepresentante->setDado('numcgm',$request->get('inCGM'));
                    $obTRepresentante->recuperaRelacionamentoSintetico( $rsRepresentante );
                    $arRelatorio['nomRepresentante'] = $rsRepresentante->getCampo('nom_cgm');
                    $arRelatorio['cgmRepresentante'] = $request->get('inCGM');

                    //CONSULTANDO ARQUIVO TEMPLATE
                    include_once TADM.'TAdministracaoModeloArquivosDocumento.class.php';
                    $obTAdministracaoArquivosDocumento = new TAdministracaoModeloArquivosDocumento();
                    $obTAdministracaoArquivosDocumento->setDado( 'cod_acao', Sessao::read('acao') );
                    $obTAdministracaoArquivosDocumento->setDado( 'cod_documento', 0 );
                    $obTAdministracaoArquivosDocumento->recuperaDocumentos( $rsTemplate );
                    $arRelatorio['nomDocumentoSxw'] =$rsTemplate->getCampo('nome_arquivo_template');
                    Sessao::write('arRelatorio', $arRelatorio);
                    SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());
                }
            }

            if ($stMensagem != '') {
                sistemaLegado::exibeAviso(urlencode($stMensagem),'n_incluir','erro');
            }
        } else {
            sistemaLegado::exibeAviso(urlencode('O valor de garantia informado é maior que o permitido!'),'n_incluir','erro');
        }
    break;

    case "anular";
       
            if ( ( sistemaLegado::comparaDatas( $request->get('stDataAnulacao'), $request->get('dtAssinatura') ) ) || ( $request->get('stDataAnulacao') == $request->get('dtAssinatura') ) ) {
                $obTContratoAnulado->setDado('num_contrato',$request->get('inNumContrato'));
                $obTContratoAnulado->setDado('cod_entidade',$request->get('inCodEntidade'));
                $obTContratoAnulado->setDado('exercicio',$request->get('stExercicioContrato'));
                $obTContratoAnulado->setDado('dt_anulacao', $request->get('stDataAnulacao'));
                $obTContratoAnulado->setDado('motivo', $request->get('stMotivo'));
                
                $vlAnulacao = number_format(str_replace(".", "", $request->get('vlAnulacao')), 2, ".", "");
                $obTContratoAnulado->setDado('valor_anulacao', $vlAnulacao);
                
                $obTContrato = new TLicitacaoContrato;
                $obTContrato->setDado('num_contrato',$request->get('inNumContrato'));
                $obTContrato->setDado('cod_entidade',$request->get('inCodEntidade'));
                $obTContrato->setDado('exercicio',$request->get('stExercicioContrato'));
                $obTContrato->recuperaPorChave($rsContrato);
                
                if($vlAnulacao > $rsContrato->getCampo('valor_contratado')) {
                    SistemaLegado::exibeAviso('O valor da anulação não pode ser maior que o valor do contrato.', "n_incluir", "erro" );
                    break;
                }
                
                $obTContratoAnulado->inclusao();
                
            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Contrato: ".$obTContratoAnulado->getDado('exercicio')." - ".$obTContratoAnulado->getDado('cod_entidade')." - ".$obTContratoAnulado->getDado('numero_contrato'),"incluir", "aviso", Sessao::getId(),"");
            } else {
                sistemaLegado::exibeAviso(urlencode('<i><b>Data de Anulação</b></i> deve ser maior ou igual a <b><i>Data da Assinatura</i></b>.'),'n_anular','erro');
            }
        break;
    }

Sessao::encerraExcecao();
