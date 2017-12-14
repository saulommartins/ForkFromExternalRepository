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
    * Pagina de processamento para Incluir Cadastro/Certificação
    * Data de Criação   : 03/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * Casos de uso: uc-03.05.13

    $Id: PRManterCertificacao.php 64487 2016-03-02 20:40:30Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once  CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" ;
include_once TLIC."TLicitacaoParticipanteCertificacao.class.php";
include_once TLIC."TLicitacaoCertificacaoDocumentos.class.php";
include_once TLIC."TLicitacaoDocumentoAtributoValor.class.php";
include_once TLIC."TLicitacaoDocumentosAtributos.class.php";
include_once TLIC."TLicitacaoParticipanteCertificacaoLicitacao.class.php";
include_once TLIC.'TLicitacaoLicitacao.class.php';

$stPrograma = "ManterCertificacao";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";
$pgGera       = "OCGeraCertificadoFornecedor.php";

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');
$stAcao = $request->get('stAcao');

$obRCadastroDinamico = new RCadastroDinamico();
$obRCadastroDinamico->setPersistenteValores  (  new TLicitacaoDocumentoAtributoValor );
$obRCadastroDinamico->setCodCadastro( 1 );
$obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

$obErro = new Erro();
$boFlagTransacao = false;

$obTransacao = new Transacao();
$obTransacao->begin();
$boTransacao = $obTransacao->getTransacao();

$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

$obTLicitacaoParticipanteCertificacao = new TLicitacaoParticipanteCertificacao();
$obTLicitacaoCertificacaoDocumentos   = new TLicitacaoCertificacaoDocumentos();
$obTLicitacaoDocumentoAtributoValor   = new TLicitacaoDocumentoAtributoValor();
$obTLicitacaoDocumentosAtributos      = new TLicitacaoDocumentosAtributos();
$obTLicitacaoLicitacao                = new TLicitacaoLicitacao();
$obTLicitacaoParticipanteCertificacaoLicitacao = new TLicitacaoParticipanteCertificacaoLicitacao();

switch ($stAcao) {
    case "incluir":
        
        if ( implode(array_reverse(explode('/',$request->get('dtDataRegistro')))) > implode(array_reverse(explode('/',$request->get('dtDataVigencia')))) ) {
                $obErro->setDescricao("A data de registro deve ser menor que a data de vigência.");
        } elseif ( count( Sessao::read('arDocs') ) == 0 ) {
                $obErro->setDescricao("Ao menos um documento deve ser incluído.");
        } else {
            $obTLicitacaoLicitacao->setDado( 'exercicio'     , $request->get('stExercicioLicitacao') );
            $obTLicitacaoLicitacao->setDado( 'cod_entidade'  , $request->get('inCodEntidade') );
            $obTLicitacaoLicitacao->setDado( 'cod_modalidade', $request->get('inCodModalidade') );
            
            $stFiltro .= " AND ll.cod_licitacao IN ( SELECT cod_licitacao
                                                       FROM licitacao.participante_certificacao_licitacao
                                                      WHERE cod_licitacao  = ".$request->get('inCodLicitacao')."
                                                        AND cod_modalidade = ".$request->get('inCodModalidade')."
                                                        AND cod_entidade   = ".$request->get('inCodEntidade')."
                                                        AND cgm_fornecedor = ".$request->get('inCodFornecedor')."
                                                    ) ";
            
            $obTLicitacaoLicitacao->recuperaLicitacao( $rsLicitacao, $stFiltro );

            if ( $rsLicitacao->getNumLinhas() > 0 ) {
                $obErro->setDescricao("Fornecedor (".$request->get('inCodFornecedor').") já foi certificado para a licitação (".$request->get('inCodLicitacao').")");
            }
        }
        
        if (!$obErro->ocorreu()) {

            $obTLicitacaoParticipanteCertificacao->proximocod($inNumCertificacao);

            $obTLicitacaoParticipanteCertificacao->setDado( 'num_certificacao', $inNumCertificacao );
            $obTLicitacaoParticipanteCertificacao->setDado( 'exercicio', Sessao::getExercicio() );
            $obTLicitacaoParticipanteCertificacao->setDado( 'cgm_fornecedor', $request->get('inCodFornecedor') );
            $obTLicitacaoParticipanteCertificacao->setDado( 'cod_tipo_documento', 0 );
            $obTLicitacaoParticipanteCertificacao->setDado( 'cod_documento', 0 );
            $obTLicitacaoParticipanteCertificacao->setDado( 'dt_registro', $request->get('dtDataRegistro') );
            $obTLicitacaoParticipanteCertificacao->setDado( 'final_vigencia', $request->get('dtDataVigencia') );
            $obTLicitacaoParticipanteCertificacao->setDado( 'observacao', $request->get('hdnObservacao') );
            $obErro = $obTLicitacaoParticipanteCertificacao->inclusao($boTransacao);

            if (!$obErro->ocorreu()) {
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'num_certificacao'      , $inNumCertificacao );
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'exercicio_certificacao', Sessao::getExercicio() );
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'cgm_fornecedor'        , $request->get('inCodFornecedor') );
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'cod_licitacao'         , $request->get('inCodLicitacao') );
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'cod_modalidade'        , $request->get('inCodModalidade') );
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'cod_entidade'          , $request->get('inCodEntidade') );
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'exercicio_licitacao'   , $request->get('stExercicioLicitacao') );
                $obErro = $obTLicitacaoParticipanteCertificacaoLicitacao->inclusao($boTransacao);
            }

            foreach ( Sessao::read('arDocs') as $key => $value ) {
                $obTLicitacaoCertificacaoDocumentos->obTLicitacaoParticipanteCertificacao = & $obTLicitacaoParticipanteCertificacao;
                $obTLicitacaoCertificacaoDocumentos->setDado( 'cod_documento', $value['cod_documento'] );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'cgm_fornecedor', $request->get('inCodFornecedor') );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'num_documento', $value['num_documento'] );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'dt_emissao', $value['data_emissao'] );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'dt_validade', $value['data_validade'] );
                $obTLicitacaoCertificacaoDocumentos->inclusao($boTransacao);

                if ( is_array($value['atributos']) ) {
                    foreach ($value['atributos'] as $key => $value2) {
                        $arKey = explode('_',$key);
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_documento', $value['cod_documento'] );
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_cadastro', $arKey[2] );
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_modulo', 37 );
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_atributo', $arKey[1] );

                        $obTLicitacaoDocumentoAtributoValor->obTLicitacaoCertificacaoDocumentos = & $obTLicitacaoCertificacaoDocumentos;
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'valor', $value2 );
                        $obTLicitacaoDocumentoAtributoValor->inclusao($boTransacao);
                    }
                }
            }
            
            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTLicitacaoParticipanteCertificacao);
            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTLicitacaoParticipanteCertificacaoLicitacao);
            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTLicitacaoCertificacaoDocumentos);
            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTLicitacaoDocumentoAtributoValor);
            
            SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=$stAcao",'Incluir Cadastro / Certificação concluído com sucesso ('.str_pad($obTLicitacaoCertificacaoDocumentos->getDado("num_certificacao"),6,"0",STR_PAD_LEFT).'/'.$obTLicitacaoCertificacaoDocumentos->getDado("exercicio").')! ', "", "aviso", Sessao::getId(), "../");
            $requestTMP = $_REQUEST;
            $requestTMP['request']['inNumCertificacao'] = str_pad($obTLicitacaoCertificacaoDocumentos->getDado("num_certificacao"),6,"0",STR_PAD_LEFT);
            $requestTMP['stExercicio'] = $obTLicitacaoCertificacaoDocumentos->getDado("exercicio");

            Sessao::write('request' , $requestTMP);

            $stLink = '&inCodFornecedor='.$request->get('inCodFornecedor');
            $stLink.= '&stExercicio='.$obTLicitacaoCertificacaoDocumentos->getDado("exercicio"       );
            $stLink.= '&inNumCertificacao='.str_pad($obTLicitacaoCertificacaoDocumentos->getDado("num_certificacao"),6,"0",STR_PAD_LEFT);
            $stLink.= '&dtDataRegistro='.$request->get('dtDataRegistro');
            $stLink.= '&dtDataVigencia='.$request->get('dtDataVigencia');
            SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId().$stLink);
        } else {
            SistemaLegado::exibeAviso( urlencode( $obErro->getDescricao() ) ,"n_alterar", "erro");
        }
    break;

    case 'alterar':
            
        if ( count( Sessao::read('arDocs') ) == 0 ) {
            $stMensagem = 'Ao menos um documento deve ser incluído.';
        }
        if (!$stMensagem && !$obErro->ocorreu()) {
            
            $obTLicitacaoParticipanteCertificacao->setDado( 'exercicio', $request->get('stHdnExercicio') );
            $obTLicitacaoParticipanteCertificacao->setDado( 'cgm_fornecedor', $request->get('inHdnCodFornecedor') );
            $obTLicitacaoParticipanteCertificacao->setDado( 'cod_tipo_documento', 0 );
            $obTLicitacaoParticipanteCertificacao->setDado( 'cod_documento', 0 );
            $obTLicitacaoParticipanteCertificacao->setDado( 'dt_registro', $request->get('dtHdnDataRegistro') );
            $obTLicitacaoParticipanteCertificacao->setDado( 'final_vigencia', $request->get('dtHdnDataVigencia') );
            $obTLicitacaoParticipanteCertificacao->setDado( 'observacao', $request->get('hdnObservacao') );
            $obTLicitacaoParticipanteCertificacao->setDado( 'num_certificacao', intval($request->get('inNumCertificacao')) );
            $obTLicitacaoParticipanteCertificacao->alteracao($boTransacao);
            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTLicitacaoParticipanteCertificacao);
            
            if (!$obErro->ocorreu()) {
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'num_certificacao'      , $request->get('inNumCertificacao'));
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'exercicio_certificacao', $request->get('stHdnExercicio') );
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'cgm_fornecedor'        , $request->get('inHdnCodFornecedor') );
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'cod_entidade'          , $request->get('inCodEntidade') );
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'exercicio_licitacao'   , $request->get('stExercicioLicitacao') );
                
                $obTLicitacaoParticipanteCertificacaoLicitacao->recuperaPorChave($rsParticipanteLicitacao);
                
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'cod_modalidade'        , $request->get('inCodModalidade') );
                $obTLicitacaoParticipanteCertificacaoLicitacao->setDado( 'cod_licitacao'         , $request->get('inCodLicitacao') );
                                
                if ($rsParticipanteLicitacao->getNumLinhas() <= 0) {
                    $obErro = $obTLicitacaoParticipanteCertificacaoLicitacao->inclusao($boTransacao);  
                } else {
                    $obErro = $obTLicitacaoParticipanteCertificacaoLicitacao->alteracao($boTransacao);
                }

            }
            
            // verificação da chave do array de itens
            $obTLicitacaoCertificacaoDocumentos->obTLicitacaoParticipanteCertificacao = & $obTLicitacaoParticipanteCertificacao;
            $obTLicitacaoCertificacaoDocumentos->recuperaPorChave( $rsItens );

            while ( !$rsItens->eof() ) {
                $obTLicitacaoDocumentoAtributoValor->obTLicitacaoCertificacaoDocumentos = & $obTLicitacaoCertificacaoDocumentos;
                $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_documento', $rsItens->getCampo('cod_documento') );
                $obTLicitacaoDocumentoAtributoValor->exclusao($boTransacao);
                $rsItens->proximo();
            }
            $obTLicitacaoCertificacaoDocumentos->exclusao($boTransacao);

            $arDocs = Sessao::read('arDocs');

            foreach ($arDocs as $key => $value) {
                $obTLicitacaoCertificacaoDocumentos->obTLicitacaoParticipanteCertificacao = & $obTLicitacaoParticipanteCertificacao;
                $obTLicitacaoCertificacaoDocumentos->setDado( 'cod_documento', $value['cod_documento'] );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'num_documento', $value['num_documento'] );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'dt_emissao', $value['data_emissao'] );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'dt_validade', $value['data_validade'] );
                $obTLicitacaoCertificacaoDocumentos->inclusao($boTransacao);

                if ( is_array($value['atributos']) && ( count($value['atributos']) > 0 )) {
                    foreach ($value['atributos'] as $key => $value2) {
                        $arKey = explode('_',$key);
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_documento', $value['cod_documento'] );
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_cadastro', $arKey[2] );
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_modulo', 37 );
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_atributo', $arKey[1] );
                        $obTLicitacaoDocumentoAtributoValor->obTLicitacaoCertificacaoDocumentos = & $obTLicitacaoCertificacaoDocumentos;
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'valor', $value2 );
                        $obTLicitacaoDocumentoAtributoValor->inclusao($boTransacao);
                    }
                }
            }
            
            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTLicitacaoParticipanteCertificacaoLicitacao);
            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTLicitacaoCertificacaoDocumentos);
            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTLicitacaoDocumentoAtributoValor);
            
            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId().$stLink, "Número da certificação: ".$request->get('inNumCertificacao')."/".$request->get('stHdnExercicio'), "alterar", "aviso", Sessao::getId(), "../");
            
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem),"n_alterar","erro");
        }
    break;
}

?>