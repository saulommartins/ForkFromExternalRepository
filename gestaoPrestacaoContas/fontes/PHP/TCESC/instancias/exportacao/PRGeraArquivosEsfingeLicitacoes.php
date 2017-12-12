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
    * Página de Formulario de Manter Adjudicacao
    * Data de Criação: 23/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Id: $

    * Casos de uso: uc-03.05.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function geraArquivosLicitacoes(&$obExportador, $stDataInicial, $stDataFinal)
{
    include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php"  );
    include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoParticipante.class.php"  );
    include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoParticipanteDocumentos.class.php"  );
    include_once( CAM_GP_COM_MAPEAMENTO."TComprasCotacaoFornecedorItem.class.php"  );
    include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoComissaoLicitacao.class.php"  );
    include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoComissaoMembros.class.php"  );
    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldos.class.php"  );

    $arFiltroRelatorio = Sessao::read('filtroRelatorio');

    //======== PROCESSO LICITATORIO ========
    $obTLicitacaoLicitacao = new TLicitacaoLicitacao;
    $obTLicitacaoLicitacao->setDado( "exercicio", $arFiltroRelatorio['stExercicio'] );
    $obTLicitacaoLicitacao->setDado( "cod_entidade", implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTLicitacaoLicitacao->setDado( "dt_inicial", $stDataInicial );
    $obTLicitacaoLicitacao->setDado( "dt_final", $stDataFinal );
    $obTLicitacaoLicitacao->recuperaProcessoLicitatorioEsfinge( $rsProcessoLicitatorio );
    unset( $obTLicitacaoLicitacao );

    $obExportador->addArquivo("ProcessoLicitatorio.txt");
    $obExportador->roUltimoArquivo->addBloco( $rsProcessoLicitatorio );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_licitacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_edital");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo_licitacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_comissao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo_objeto");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_modalidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_criterio");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_abertura_propostas");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("contrato_dt_assinatura");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(255);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_cotado");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_abertura_propostas");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("timestamp_homologacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_garantia");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

//======== CONVIDADO LICITACÃO ========
    $obTLicitacaoParticipante = new TLicitacaoParticipante;
    $obTLicitacaoParticipante->setDado( "exercicio", $arFiltroRelatorio['stExercicio'] );
    $obTLicitacaoParticipante->setDado( "cod_entidade", implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTLicitacaoParticipante->setDado( "dt_inicial", $stDataInicial );
    $obTLicitacaoParticipante->setDado( "dt_final", $stDataFinal );
    $obTLicitacaoParticipante->recuperaConvidadoLicitacaoEsfinge( $rsConvidadosLicitacao );
    unset($obTLicitacaoParticipante);

    $obExportador->addArquivo("ConvidadoLicitacao.txt");
    $obExportador->roUltimoArquivo->addBloco($rsConvidadosLicitacao);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_pessoa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cic_do_participante");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_licitacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_cgm");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inclusao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

//======== ITENS DA LICITACÃO ========
    $obTLicitacaoLicitacao = new TLicitacaoLicitacao;
    $obTLicitacaoLicitacao->setDado( "exercicio", $arFiltroRelatorio['stExercicio'] );
    $obTLicitacaoLicitacao->setDado( "cod_entidade", implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTLicitacaoLicitacao->setDado( "dt_inicial", $stDataInicial );
    $obTLicitacaoLicitacao->setDado( "dt_final", $stDataFinal );
    $obTLicitacaoLicitacao->recuperaItensLicitadosEsfinge( $rsItensLicitacao );
    unset($obTLicitacaoLicitacao);

    $obExportador->addArquivo("ItemLicitacao.txt");
    $obExportador->roUltimoArquivo->addBloco( $rsItensLicitacao );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_licitacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_resumida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_homologacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("lote");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_unidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

//======== PARTICIPANTE LICITACÃO ========
    $obTLicitacaoParticipante = new TLicitacaoParticipante;
    $obTLicitacaoParticipante->setDado( "exercicio", $arFiltroRelatorio['stExercicio'] );
    $obTLicitacaoParticipante->setDado( "cod_entidade", implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTLicitacaoParticipante->setDado( "dt_inicial", $stDataInicial );
    $obTLicitacaoParticipante->setDado( "dt_final", $stDataFinal );
    $obTLicitacaoParticipante->recuperaParticipanteLicitacaoEsfinge( $rsParticipanteLicitacao );
    unset($obTLicitacaoParticipante);

    $obExportador->addArquivo("ParticipanteLicitacao.txt");
    $obExportador->roUltimoArquivo->addBloco( $rsParticipanteLicitacao );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_licitacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_pessoa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cic_participante");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_participacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_cgm");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

//======== CERTIDÃO PARTICIPANTE ========
    $obTLicitacaoParticipanteDocumentos = new TLicitacaoParticipanteDocumentos;
    $obTLicitacaoParticipanteDocumentos->setDado( "exercicio", $arFiltroRelatorio['stExercicio'] );
    $obTLicitacaoParticipanteDocumentos->setDado( "cod_entidade", implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTLicitacaoParticipanteDocumentos->setDado( "dt_inicial", $stDataInicial );
    $obTLicitacaoParticipanteDocumentos->setDado( "dt_final", $stDataFinal );
    $obTLicitacaoParticipanteDocumentos->recuperaCertidaoParticipanteEsfinge( $rsParticipanteDocumentos );
    unset($obTLicitacaoParticipanteDocumentos);

    $obExportador->addArquivo("CertidaoParticipanteLicitacao.txt");
    $obExportador->roUltimoArquivo->addBloco( $rsParticipanteDocumentos );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_licitacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_pessoa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cic_participante");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo_certidao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(25);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(25);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(25);

//======== CADASTRO PARTICIPANTE ========
    $obTLicitacaoParticipante = new TLicitacaoParticipante;
    $obTLicitacaoParticipante->setDado( "exercicio", $arFiltroRelatorio['stExercicio'] );
    $obTLicitacaoParticipante->setDado( "cod_entidade", implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTLicitacaoParticipante->setDado( "dt_inicial", $stDataInicial );
    $obTLicitacaoParticipante->setDado( "dt_final", $stDataFinal );
    $obTLicitacaoParticipante->recuperaCadastroParticipanteLicitacaoEsfinge( $rsCadastroParticipantes );
    unset($obTLicitacaoParticipante);

    $obExportador->addArquivo("CadastroParticipanteLicitacao.txt");
    $obExportador->roUltimoArquivo->addBloco( $rsCadastroParticipantes );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_licitacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_pessoa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cic_participante");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_cgm");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certificacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("final_vigencia");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

//======== COTACAO ========
    $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;
    $obTComprasCotacaoFornecedorItem->setDado( "exercicio", $arFiltroRelatorio['stExercicio'] );
    $obTComprasCotacaoFornecedorItem->setDado( "cod_entidade", implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTComprasCotacaoFornecedorItem->setDado( "dt_inicial", $stDataInicial );
    $obTComprasCotacaoFornecedorItem->setDado( "dt_final", $stDataFinal );
    $obTComprasCotacaoFornecedorItem->recuperaCotacaoEsfinge( $rsCotacao );
    unset($obTComprasCotacaoFornecedorItem);

    $obExportador->addArquivo("CadastroParticipanteLicitacao.txt");
    $obExportador->roUltimoArquivo->addBloco( $rsCotacao );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_pessoa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cic_participante");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_licitacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_cotacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ordem");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ordem");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

//======== COMISSÃO LICITACÃO ========
    $obTLicitacaoComissaoLicitacao = new TLicitacaoComissaoLicitacao;
    $obTLicitacaoComissaoLicitacao->setDado( "exercicio", $arFiltroRelatorio['stExercicio'] );
    $obTLicitacaoComissaoLicitacao->setDado( "cod_entidade", implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTLicitacaoComissaoLicitacao->setDado( "dt_inicial", $stDataInicial );
    $obTLicitacaoComissaoLicitacao->setDado( "dt_final", $stDataFinal );
    $obTLicitacaoComissaoLicitacao->recuperaComissaoLicitacaoEsfinge( $rsComissaoLicitacao );
    unset($obTLicitacaoComissaoLicitacao);

    $obExportador->addArquivo("ComissaoLicitacao.txt");
    $obExportador->roUltimoArquivo->addBloco( $rsComissaoLicitacao );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_comissao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_termino");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(255);

//======== MEMBRO COMISSAO LICITACAO ========
    $obTLicitacaoComissaoMembros = new TLicitacaoComissaoMembros;
    $obTLicitacaoComissaoMembros->setDado( "exercicio", $arFiltroRelatorio['stExercicio'] );
    $obTLicitacaoComissaoMembros->setDado( "cod_entidade", implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTLicitacaoComissaoMembros->setDado( "dt_inicial", $stDataInicial );
    $obTLicitacaoComissaoMembros->setDado( "dt_final", $stDataFinal );
    $obTLicitacaoComissaoMembros->recuperaMembrosComissaoEsfinge( $rsMembrosComissaoLicitacao );
    unset($obTLicitacaoComissaoMembros);

    $obExportador->addArquivo("MembroComissaoLicitacao.txt");
    $obExportador->roUltimoArquivo->addBloco( $rsMembrosComissaoLicitacao );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_comissao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura_membro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_cgm");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_norma");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_termino");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicativo_presidencia");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

//======== BLOQUEIO ORÇAMENTÁRIO ========
    $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
    $obTOrcamentoReservaSaldos->setDado( "exercicio", $arFiltroRelatorio['stExercicio'] );
    $obTOrcamentoReservaSaldos->setDado( "cod_entidade", implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTOrcamentoReservaSaldos->setDado( "dt_inicial", $stDataInicial );
    $obTOrcamentoReservaSaldos->setDado( "dt_final", $stDataFinal );
    $obTOrcamentoReservaSaldos->recuperaBloqueioOrcamentarioEsfinge( $rsBloqueioOrcamentario );
    unset($obTOrcamentoReservaSaldos);

    $obExportador->addArquivo("BloqueioOrcamentario.txt");
    $obExportador->roUltimoArquivo->addBloco( $rsBloqueioOrcamentario );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reserva");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_licitacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_acao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_pao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna(""); //iduso
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna(""); //especificação das fontes
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna(""); //reservado ao tc
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("categoria_economica");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("grupo_natureza_despesa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("modalidade_aplicacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("modalidade_aplicacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elemento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inclusao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_reserva");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
