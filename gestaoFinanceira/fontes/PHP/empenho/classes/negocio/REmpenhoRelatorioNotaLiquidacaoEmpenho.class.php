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
    * Classe de Regra para emissão do relatório
    * Data de Criação   : 20/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Regra

    $Id: REmpenhoRelatorioNotaLiquidacaoEmpenho.class.php 65674 2016-06-08 17:18:14Z evandro $

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-12-07 16:11:31 -0200 (Sex, 07 Dez 2007) $

    * Casos de uso uc-02.03.21
                   uc-02.03.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO);
include_once ( CAM_GA_ADM_NEGOCIO     ."RCadastroDinamico.class.php"      );
include_once ( CAM_GF_EMP_NEGOCIO     ."REmpenhoNotaLiquidacao.class.php" );
include_once ( CAM_GF_EMP_NEGOCIO     ."REmpenhoEmpenho.class.php"        );

/**
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class REmpenhoRelatorioNotaLiquidacaoEmpenho extends PersistenteRelatorio
{
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inCodNota;
/**
    * @var Boolean
    * @access Private
*/
var $boImplantado;
var $stExercicioEmpenho;
/**
     * @access public
     * @param string $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
function setExercicioEmpenho($valor) { $this->stExercicioEmpenho = $valor; }
/**
     * @access public
     * @param Integer $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade= $valor; }
/**
     * @access public
     * @param Integer $valor
*/
function setCodNota($valor) { $this->inCodNota    = $valor; }
/**
     * @access public
     * @param Boolean $valor
*/
function setImplantado($valor) { $this->boImplantado = $valor; }

/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;     }
/**
     * @access Public
     * @return Integer
*/
function getCodEntidade() { return $this->inCodEntidade; }
/**
     * @access Public
     * @return Integer
*/
function getCodNota() { return $this->inCodNota;     }
/**
     * @access Public
     * @return Boolean
*/
function getImplantado() { return $this->boImplantado;  }

/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioNotaLiquidacaoEmpenho()
{
    parent::PersistenteRelatorio();
    $this->obRCadastroDinamico      = new RCadastroDinamico;
    $this->obREmpenhoEmpenho        = new REmpenhoEmpenho;
    $this->obREmpenhoNotaLiquidacao = new REmpenhoNotaLiquidacao($this->obREmpenhoEmpenho);
    $this->obRCadastroDinamico->setPersistenteValores  ( new TEmpenhoAtributoLiquidacaoValor );
//    $this->obRCadastroDinamico->setPersistenteAtributos( new TEmpenhoAtributoEmpenho         );
    $this->obRCadastroDinamico->setCodCadastro(2);
    $this->obRCadastroDinamico->obRModulo->setCodModulo(10);
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arRecordSet , $stOrder = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacao.class.php" );
    $obTEmpenhoNotaLiquidacao = new TEmpenhoNotaLiquidacao;

    $arRecordSet = array();
    $stFiltro    = "";
    $stFiltro   .= " AND   nl.cod_entidade  = " . $this->inCodEntidade;
    $stFiltro   .= " AND   nl.cod_nota      = " . $this->inCodNota;
    $stFiltro   .= " AND   nl.exercicio     = '" . $this->stExercicio . "'";
    $obTEmpenhoNotaLiquidacao->setDado( 'exercicio_empenho' , $this->stExercicioEmpenho );
    $stOrder = " ORDER BY li.num_item ";
    if ($this->boImplantado != "t") {
        $obErro = $obTEmpenhoNotaLiquidacao->recuperaNotaLiquidacaoEmpenho( $rsRecordSet, $stFiltro, $stOrder );
    } else {
        $obTEmpenhoNotaLiquidacao->setDado( 'exercicio' , $this->stExercicio );
        $obErro = $obTEmpenhoNotaLiquidacao->recuperaNotaLiquidacaoEmpenhoRestos( $rsRecordSet, $stFiltro, $stOrder );
    }
    if ( !$obErro->ocorreu() ) {
        $this->obREmpenhoNotaLiquidacao->setExercicio ( $this->stExercicio );
        $this->obREmpenhoNotaLiquidacao->setCodNota   ( $this->inCodNota   );
        $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $this->inCodEntidade );
        $this->obREmpenhoNotaLiquidacao->consultar();
        $arChaveAtributo = array( "cod_entidade" => $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade(),
                                  "cod_nota"     => $this->inCodNota,
                                  "exercicio"    => $this->stExercicio           );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
        $this->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
    }

    //Linha0
    $arLinha0[0]['entidade']   = $rsRecordSet->getCampo('cod_entidade') .' - '. $rsRecordSet->getCampo('nom_entidade');
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha0);
    $arRecordSet[0] = $rsNewRecord;

    if ( !$rsRecordSet->eof() ) {
        //Linha1
        $arLinha1[0]['Credor']  = $rsRecordSet->getCampo('numcgm').' - '.$rsRecordSet->getCampo('nom_cgm');
        $arLinha1[0]['CpfCnpj'] = $rsRecordSet->getCampo('cpf_cnpj');
        $arLinha1[0]['Cgm']     = $rsRecordSet->getCampo('numcgm');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha1);
        $arRecordSet[1] = $rsNewRecord;

        //Linha2
        $arLinha2[0]['Endereco']= $rsRecordSet->getCampo('endereco');
        $arLinha2[0]['Fone']    = $rsRecordSet->getCampo('fone');
        $arLinha2[0]['Cidade']  = $rsRecordSet->getCampo('nom_municipio');
        $arLinha2[0]['Uf']      = $rsRecordSet->getCampo('sigla_uf');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha2);
        $arRecordSet[2] = $rsNewRecord;

        //Linha3
        $arLinha3[0]['Empenho']     = $rsRecordSet->getCampo('cod_empenho').'/'.$rsRecordSet->getCampo('exercicio_empenho');
        $arLinha3[0]['Emissao']     = $rsRecordSet->getCampo('dt_empenho');
        $arLinha3[0]['Vencimento_Liquidacao']  = $rsRecordSet->getCampo('dt_vencimento_liquidacao');
        $arLinha3[0]['Liquidacao']  = $rsRecordSet->getCampo('dt_liquidacao');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha3);
        $arRecordSet[3] = $rsNewRecord;

        //Linha4
        $arDesc = array();
        $stDescricao = str_replace( chr(10) , "", $rsRecordSet->getCampo('descricao') );
        $stDescricao = wordwrap( $stDescricao , 100, chr(13) );
        $arDescricao = explode( chr(13), $stDescricao );
        foreach ($arDescricao as $stDescricao) {
            $arDescricao['descricao'] = $stDescricao;
            $arDesc[] = $arDescricao;
        }

        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arDesc);
        $arRecordSet[4] = $rsNewRecord;

        //Observacao
        $arObs = array();
        $stObservacao = str_replace( chr(10) , "", $rsRecordSet->getCampo('observacao') );
        $stObservacao = wordwrap( $stObservacao , 100, chr(13) );
        $arObservacao = explode( chr(13), $stObservacao );
        foreach ($arObservacao as $stObservacao) {
            $arObservacao[1] = $stObservacao;
            $arObs[] = $arObservacao;
        }

        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arObs);
        $arRecordSet[8] = $rsNewRecord;

        //PROCESSO
        if ($rsRecordSet->getCampo('cod_processo')) {
            $arProc[1] = "PROCESSO ADMINISTRATIVO: ".$rsRecordSet->getCampo('cod_processo');
            $arProc2[1] = "ANO PROCESSO: ".$rsRecordSet->getCampo('ano_processo');            
            $arProcesso[] = $arProc;
            $arProcesso[] = $arProc2;
            
            $rsNewRecord = new RecordSet;
            $rsNewRecord->preenche($arProcesso);
            $arRecordSet[21] = $rsNewRecord;
        }

    }
    $inCount=0;
    while ( !$rsRecordSet->eof() ) {
        $arLinha5[$inCount]['Item']             = $rsRecordSet->getCampo('num_item');
        $stNomItem                              = strtoupper($rsRecordSet->getCampo('nom_item')." ".$rsRecordSet->getCampo('complemento'));
        $stNomItem                              = str_replace( chr(10), "", $stNomItem );
        $stNomItem                              = str_replace( chr(13), " ", $stNomItem );
        $stNomItem                              = wordwrap( $stNomItem, 60, chr(13) );
        $arNomItem                              = explode( chr(13), $stNomItem );
        $arLinha5[$inCount]['ValorEmpenhado']   = $rsRecordSet->getCampo('empenhado');
        $arLinha5[$inCount]['ValorLiquidado']   = $rsRecordSet->getCampo('liquidado');
        $nuTotal                               += $rsRecordSet->getCampo('liquidado');
        foreach ($arNomItem as $stNomItem) {
            $arLinha5[$inCount]['Especificacao']    = $stNomItem;
            $inCount++;
        }

        $rsRecordSet->proximo();
    }

    //Bloco4
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha5);
    $rsNewRecord->addFormatacao("ValorEmpenhado","NUMERIC_BR");
    $rsNewRecord->addFormatacao("ValorLiquidado","NUMERIC_BR");
    $arRecordSet[5] = $rsNewRecord;

    $rsRecordSet->setPrimeiroElemento();

    //Bloco5
    $stRecurso = "Recurso: " . $rsRecordSet->getCampo('cod_recurso') ." - ". $rsRecordSet->getCampo('nom_recurso') . " / PAO: ".$rsRecordSet->getCampo('num_acao')." - " .$rsRecordSet->getCampo('nom_pao');
    $stRecurso = str_replace( chr(10) , "", $stRecurso );
    $stRecurso = wordwrap( $stRecurso , 80, chr(13) );
    $arRecurso = explode( chr(13), $stRecurso );
    $inCount=0;
    $arLinha6[0]['Total']       = ' Total ';
    $arLinha6[0]['ValorTotal']  = number_format($nuTotal,2,',','.');
    foreach ($arRecurso as $stRecurso) {
            $arLinha6[$inCount]['recurso']  = $stRecurso;
            $inCount++;
    }

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha6);
    //$rsNewRecord->addFormatacao("ValorTotal","NUMERIC_BR");
    $arRecordSet[6] = $rsNewRecord;

    $inCount = 0;
    while ( !$rsAtributos->eof() ) {
        $arLinha7[$inCount]['Nome']     = $rsAtributos->getCampo('nom_atributo');
        if ($rsAtributos->getCampo('cod_tipo')==3) {
            $arDescricoes = explode("[][][]",$rsAtributos->getCampo('valor_padrao_desc'));
            $arValores    = explode(",",$rsAtributos->getCampo('valor_padrao'));

            foreach ($arValores as $chave => $valor) {
                if ( $valor == $rsAtributos->getCampo('valor') ) {
                    $arLinha7[$inCount]['Valor']    = $arDescricoes[ $chave ];
                }
            }
        } elseif ($rsAtributos->getCampo('cod_tipo')==4) {
            $arDescricoes   = explode("[][][]",$rsAtributos->getCampo('valor_padrao_desc'));
            $arValores      = explode(",",$rsAtributos->getCampo('valor_desc'));
            $stValor        = "";
            for ($inIndice=0; $inIndice<count($arDescricoes); $inIndice++) {
                $stValor = $arDescricoes[ $arValores[($inIndice-1)] ];
            }
            $arLinha7[$inCount]['Valor']    = $stValor;
        } else {
            $arLinha7[$inCount]['Valor']    = ($rsAtributos->getCampo('valor')?$rsAtributos->getCampo('valor'):$rsAtributos->getCampo('valor_padrao'));
        }
        $inCount++;
        $rsAtributos->proximo();
    }
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha7);
    $arRecordSet[7] = $rsNewRecord;

    //Tipo Documento Amazonas
    if ((strtolower(SistemaLegado::pegaConfiguracao( 'seta_tipo_documento_liq_tceam',30, Sessao::getExercicio()))=='true')) {

        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMDocumento.class.php';

        $obTTCEAMDocumento = new TTCEAMDocumento();

        $stFiltroTD.= " WHERE documento.cod_entidade = " .$this->inCodEntidade;
        $stFiltroTD.= " AND   documento.exercicio    = '".$this->stExercicio."'";
        $stFiltroTD.= " AND   documento.cod_nota     = " .$this->inCodNota;

        $obTTCEAMDocumento->recuperaTodos( $rsTipoDocumento1, $stFiltroTD );

        if ($rsTipoDocumento1->getCampo('cod_documento') <>'') {

            $obTTCEAMDocumento->setDado( 'cod_tipo', $rsTipoDocumento1->getCampo('cod_tipo'));
            $obTTCEAMDocumento->recuperaRelacionamento( $rsTipoDocumento, $stFiltroTD );

            if ($rsTipoDocumento->getCampo('cod_tipo') <>'') {
                //linha9
                $arLinha9[0]['cod_tipo']        = $rsTipoDocumento->getCampo('cod_tipo');
                $arLinha9[0]['descricao_tipo']  = $rsTipoDocumento->getCampo('descricao_tipo');
                $arLinha9[0]['vl_comprometido'] = $rsTipoDocumento->getCampo('vl_comprometido');
                $arLinha9[0]['vl_total']        = $rsTipoDocumento->getCampo('vl_total');

                $rsNewRecord = new RecordSet;
                $rsNewRecord->preenche($arLinha9);
                $rsNewRecord->addFormatacao("vl_comprometido","NUMERIC_BR");
                $rsNewRecord->addFormatacao("vl_total","NUMERIC_BR");
                $arRecordSet[9] = $rsNewRecord;

                if ($rsTipoDocumento->getCampo('cod_tipo') =='1') {
                     //linha10
                     $arLinha10[0]['numero']     = $rsTipoDocumento->getCampo('numero');
                     $arLinha10[0]['dt_emissao'] = $rsTipoDocumento->getCampo('dt_emissao');
                     $arLinha10[0]['dt_saida']   = $rsTipoDocumento->getCampo('dt_saida');
                     $arLinha10[0]['hora_saida'] = $rsTipoDocumento->getCampo('hora_saida');

                     $rsNewRecord = new RecordSet;
                     $rsNewRecord->preenche($arLinha10);
                     $rsNewRecord->addFormatacao("dt_emissao","DATA_BR");
                     $rsNewRecord->addFormatacao("dt_saida","DATA_BR");
                     $arRecordSet[10] = $rsNewRecord;

                     //linha11
                     $arLinha11[0]['destino']      = $rsTipoDocumento->getCampo('destino');
                     $arLinha11[0]['dt_chegada']   = $rsTipoDocumento->getCampo('dt_chegada');
                     $arLinha11[0]['hora_chegada'] = $rsTipoDocumento->getCampo('hora_chegada');

                     $rsNewRecord = new RecordSet;
                     $rsNewRecord->preenche($arLinha11);
                     $rsNewRecord->addFormatacao("dt_chegada","DATA_BR");
                     $arRecordSet[11] = $rsNewRecord;

                     //linha12
                     $arLinha12[0]['motivo'] = $rsTipoDocumento->getCampo('motivo');

                     $rsNewRecord = new RecordSet;
                     $rsNewRecord->preenche($arLinha12);
                     $arRecordSet[12] = $rsNewRecord;
                } elseif ($rsTipoDocumento->getCampo('cod_tipo') =='2') {
                     //linha10
                     $arLinha10[0]['matricula']   = $rsTipoDocumento->getCampo('matricula');
                     $arLinha10[0]['funcionario'] = $rsTipoDocumento->getCampo('funcionario');
                     $arLinha10[0]['dt_saida']    = $rsTipoDocumento->getCampo('dt_saida');
                     $arLinha10[0]['hora_saida']  = $rsTipoDocumento->getCampo('hora_saida');
                     $arLinha10[0]['destino']     = $rsTipoDocumento->getCampo('destino');

                     $rsNewRecord = new RecordSet;
                     $rsNewRecord->preenche($arLinha10);
                     $rsNewRecord->addFormatacao("dt_saida","DATA_BR");
                     $arRecordSet[10] = $rsNewRecord;

                     //linha11
                     $arLinha11[0]['vazio']        = '';
                     $arLinha11[0]['dt_retorno']   = $rsTipoDocumento->getCampo('dt_retorno');
                     $arLinha11[0]['hora_retorno'] = $rsTipoDocumento->getCampo('hora_retorno');
                     $arLinha11[0]['quantidade']   = $rsTipoDocumento->getCampo('quantidade');

                     $rsNewRecord = new RecordSet;
                     $rsNewRecord->preenche($arLinha11);
                     $rsNewRecord->addFormatacao("dt_retorno","DATA_BR");
                     $arRecordSet[11] = $rsNewRecord;

                     //linha12
                     $arLinha12[0]['motivo']       = $rsTipoDocumento->getCampo('motivo');

                     $rsNewRecord = new RecordSet;
                     $rsNewRecord->preenche($arLinha12);
                     $arRecordSet[12] = $rsNewRecord;

                } elseif ($rsTipoDocumento->getCampo('cod_tipo') =='3') {
                     //linha10
                     $arLinha10[0]['numero']         = $rsTipoDocumento->getCampo('numero');
                     $arLinha10[0]['data']           = $rsTipoDocumento->getCampo('data');
                     $arLinha10[0]['descricao']      = $rsTipoDocumento->getCampo('descricao');

                     $rsNewRecord = new RecordSet;
                     $rsNewRecord->preenche($arLinha10);
                     $rsNewRecord->addFormatacao("data","DATA_BR");
                     $arRecordSet[10] = $rsNewRecord;

                     //linha11
                     $arLinha11[0]['descricao'] = $rsTipoDocumento->getCampo('descricao');

                     $rsNewRecord = new RecordSet;
                     $rsNewRecord->preenche($arLinha11);
                     $arRecordSet[11] = $rsNewRecord;

                     //linha12
                     $arLinha12[0]['nome_documento'] = $rsTipoDocumento->getCampo('nome_documento');

                     $rsNewRecord = new RecordSet;
                     $rsNewRecord->preenche($arLinha12);
                     $arRecordSet[12] = $rsNewRecord;

                } elseif ($rsTipoDocumento->getCampo('cod_tipo') =='4') {
                     //linha10
                     $arLinha10[0]['exercicio']  = $rsTipoDocumento->getCampo('exercicio');
                     $arLinha10[0]['mes']        = $rsTipoDocumento->getCampo('mes');

                     $rsNewRecord = new RecordSet;
                     $rsNewRecord->preenche($arLinha10);
                     $arRecordSet[10] = $rsNewRecord;
                } elseif ($rsTipoDocumento->getCampo('cod_tipo') =='5') {
                     //linha10
                     $arLinha10[0]['numero_nota_fiscal'] = $rsTipoDocumento->getCampo('numero_nota_fiscal');
                     $arLinha10[0]['numero_serie']       = $rsTipoDocumento->getCampo('numero_serie');
                     $arLinha10[0]['numero_subserie']    = $rsTipoDocumento->getCampo('numero_subserie');
                     $arLinha10[0]['data']               = $rsTipoDocumento->getCampo('data');

                     $rsNewRecord = new RecordSet;
                     $rsNewRecord->preenche($arLinha10);
                     $rsNewRecord->addFormatacao("data","DATA_BR");
                     $arRecordSet[10] = $rsNewRecord;
                } elseif ($rsTipoDocumento->getCampo('cod_tipo') =='6') {
                     //linha10
                     $arLinha10[0]['descricao']   = $rsTipoDocumento->getCampo('descricao');

                     $rsNewRecord = new RecordSet;
                     $rsNewRecord->preenche($arLinha10);
                     $arRecordSet[10] = $rsNewRecord;
                     //linha11
                     $arLinha11[0]['numero'] = $rsTipoDocumento->getCampo('numero');
                     $arLinha11[0]['valor']  = $rsTipoDocumento->getCampo('valor');
                     $arLinha11[0]['data']   = $rsTipoDocumento->getCampo('data');

                     $rsNewRecord = new RecordSet;
                     $rsNewRecord->preenche($arLinha11);
                     $rsNewRecord->addFormatacao("valor","NUMERIC_BR");
                     $rsNewRecord->addFormatacao("data" ,"DATA_BR");
                     $arRecordSet[11] = $rsNewRecord;
                }
            }
        }
    }

    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 20) {
        include_once CAM_GPC_TCERN_MAPEAMENTO.'TTCERNNotaFiscal.class.php';

        $obTTCERNNotaFiscal = new TTCERNNotaFiscal();

        $stFiltroNF.= " AND   nota_liquidacao.cod_entidade = " .$this->inCodEntidade;
        $stFiltroNF.= " AND   nota_liquidacao.exercicio    = '".$this->stExercicio."'";
        $stFiltroNF.= " AND   nota_liquidacao.cod_nota     = " .$this->inCodNota;

        $obTTCERNNotaFiscal->recuperaRelacionamento($rsNotaFiscal, $stFiltroNF);

        //informações de nota fiscal para os municípios no Rio Grande do Norte
        $arLinha13[0]['numero_nota_fiscal'] = $rsNotaFiscal->getCampo('nro_nota');
        $arLinha13[0]['numero_serie'] = $rsNotaFiscal->getCampo('nro_serie');
        $arLinha13[0]['data_emissao'] = $rsNotaFiscal->getCampo('data_emissao');
        $arLinha13[0]['cod_validacao'] = $rsNotaFiscal->getCampo('cod_validacao');
        $arLinha13[0]['modelo'] = $rsNotaFiscal->getCampo('modelo');

        $rsNewRecordLine13 = new RecordSet;
        $rsNewRecordLine13->preenche($arLinha13);

        $arRecordSet[13] = $rsNewRecordLine13;
    }
    
    //TCEAL
    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02) {
        include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALDocumento.class.php';
        $obTTCEALDocumento = new TTCEALDocumento;
        
        $stFiltroALDocumento.= " WHERE documento.cod_entidade = " .$this->inCodEntidade;
        $stFiltroALDocumento.= " AND documento.exercicio      = '".$this->stExercicio."'";
        $stFiltroALDocumento.= " AND documento.cod_nota       = " .$this->inCodNota;

        $obTTCEALDocumento->recuperaTodos($rsNotaALDocumento, $stFiltroALDocumento);
        
        $arLinha14[0]['cod_tipo']      = $rsNotaALDocumento->getCampo('cod_tipo');
        $arLinha14[0]['exercicio']     = $rsNotaALDocumento->getCampo('exercicio');
        $arLinha14[0]['cod_entidade']  = $rsNotaALDocumento->getCampo('cod_entidade');
        $arLinha14[0]['cod_nota']      = $rsNotaALDocumento->getCampo('cod_nota');
        $arLinha14[0]['nro_documento'] = $rsNotaALDocumento->getCampo('nro_documento');
        $arLinha14[0]['dt_documento']  = $rsNotaALDocumento->getCampo('dt_documento');        
        $arLinha14[0]['descricao']     = $rsNotaALDocumento->getCampo('descricao');
       
        $rsNewRecord14 = new RecordSet;
        $rsNewRecord14->preenche($arLinha14);
        $arRecordSet[14] = $rsNewRecord14;
        
        $arLinha15[0]['autorizacao']   = $rsNotaALDocumento->getCampo('autorizacao');
        $arLinha15[0]['modelo']        = $rsNotaALDocumento->getCampo('modelo');
        $arLinha15[0]['nro_xml_nfe']   = $rsNotaALDocumento->getCampo('nro_xml_nfe');

        $rsNewRecord15 = new RecordSet;
        $rsNewRecord15->preenche($arLinha15);
        $arRecordSet[15] = $rsNewRecord15;
    }
    //TCETO
    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 27) {
        include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETONotaLiquidacaoDocumento.class.php';
        $obTTCETODocumento = new TTCETONotaLiquidacaoDocumento;
        
        $stFiltroTODocumento.= " WHERE nota_liquidacao_documento.cod_entidade = " .$this->inCodEntidade;
        $stFiltroTODocumento.= " AND nota_liquidacao_documento.exercicio      = '".$this->stExercicio."'";
        $stFiltroTODocumento.= " AND nota_liquidacao_documento.cod_nota       = " .$this->inCodNota;

        $obTTCETODocumento->recuperaTodos($rsNotaTODocumento, $stFiltroTODocumento);
        
        $arLinha14[0]['cod_tipo']      = $rsNotaTODocumento->getCampo('cod_tipo');
        $arLinha14[0]['exercicio']     = $rsNotaTODocumento->getCampo('exercicio');
        $arLinha14[0]['cod_entidade']  = $rsNotaTODocumento->getCampo('cod_entidade');
        $arLinha14[0]['cod_nota']      = $rsNotaTODocumento->getCampo('cod_nota');
        $arLinha14[0]['nro_documento'] = $rsNotaTODocumento->getCampo('nro_documento');
        $arLinha14[0]['dt_documento']  = $rsNotaTODocumento->getCampo('dt_documento');        
        $arLinha14[0]['descricao']     = $rsNotaTODocumento->getCampo('descricao');
       
        $rsNewRecord14 = new RecordSet;
        $rsNewRecord14->preenche($arLinha14);
        $arRecordSet[14] = $rsNewRecord14;
        
        $arLinha15[0]['autorizacao']   = $rsNotaTODocumento->getCampo('autorizacao');
        $arLinha15[0]['modelo']        = $rsNotaTODocumento->getCampo('modelo');

        $rsNewRecord15 = new RecordSet;
        $rsNewRecord15->preenche($arLinha15);
        $arRecordSet[15] = $rsNewRecord15;
    }
    
    //TCEPE
    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 16) {
        include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEDocumento.class.php';
        $obTTCEPEDocumento = new TTCEPEDocumento;
        
        $stFiltroPEDocumento.= " WHERE documento.cod_entidade = " .$this->inCodEntidade;
        $stFiltroPEDocumento.= " AND documento.exercicio      = '".$this->stExercicio."'";
        $stFiltroPEDocumento.= " AND documento.cod_nota       = " .$this->inCodNota;
        
        $obTTCEPEDocumento->recuperaDocumento($rsNotaPEDocumento, $stFiltroPEDocumento);
        
        $arLinha14[0]['cod_tipo']      = $rsNotaPEDocumento->getCampo('cod_tipo');
        $arLinha14[0]['descricao_tipo']= $rsNotaPEDocumento->getCampo('descricao_tipo');
        $arLinha14[0]['exercicio']     = $rsNotaPEDocumento->getCampo('exercicio');
        $arLinha14[0]['cod_entidade']  = $rsNotaPEDocumento->getCampo('cod_entidade');
        $arLinha14[0]['cod_nota']      = $rsNotaPEDocumento->getCampo('cod_nota');
        $arLinha14[0]['nro_documento'] = $rsNotaPEDocumento->getCampo('nro_documento');
        $arLinha14[0]['serie']         = $rsNotaPEDocumento->getCampo('serie');
        $arLinha14[0]['sigla_uf']      = $rsNotaPEDocumento->getCampo('sigla_uf');
       
        $rsNewRecord14 = new RecordSet;
        $rsNewRecord14->preenche($arLinha14);
        $arRecordSet[14] = $rsNewRecord14;
        
    }
    
    //TCEMG
    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 11) {
        include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscal.class.php";
        include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscalEmpenhoLiquidacao.class.php";
        include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTipoNotaFiscal.class.php";
        
        $obTTCEMGNotaFiscal = new TTCEMGNotaFiscal;
        $obTTCEMGNotaFiscalEmpenho = new TTCEMGNotaFiscalEmpenhoLiquidacao;
        $obTTCEMGTipoNotaFiscal = new TTCEMGTipoNotaFiscal;
        
        $stFiltro  = " WHERE nota_fiscal_empenho_liquidacao.cod_entidade        = " .$this->inCodEntidade;
        $stFiltro .= " AND nota_fiscal_empenho_liquidacao.exercicio_liquidacao  = '".$this->stExercicio."'";
        $stFiltro .= " AND nota_fiscal_empenho_liquidacao.cod_nota_liquidacao   = " .$this->inCodNota;

        $obTTCEMGNotaFiscalEmpenho->recuperaTodos($rsNfLiquidacao, $stFiltro);
        
        if($rsNfLiquidacao->getNumLinhas()==1){
            $arLinha16[0]['cod_nf']         = $rsNfLiquidacao->getCampo('cod_nota');
            $arLinha16[0]['exercicio_nf']   = $rsNfLiquidacao->getCampo('exercicio');
            
            $stFiltro  = " WHERE nota_fiscal.cod_entidade   = " .$this->inCodEntidade;
            $stFiltro .= " AND nota_fiscal.exercicio        = '".$rsNfLiquidacao->getCampo('exercicio')."'";
            $stFiltro .= " AND nota_fiscal.cod_nota         = " .$rsNfLiquidacao->getCampo('cod_nota');
            
            $obTTCEMGNotaFiscal->recuperaTodos($rsNF, $stFiltro);
            
            if($rsNF->getNumLinhas()==1){
                $arLinha16[0]['nro_nota']               = $rsNF->getCampo('nro_nota');
                $arLinha16[0]['nro_serie']              = $rsNF->getCampo('nro_serie');
                $arLinha16[0]['data_emissao']           = $rsNF->getCampo('data_emissao');
                $arLinha16[0]['cod_tipo']               = $rsNF->getCampo('cod_tipo');//

                $stFiltro  = " WHERE tipo_nota_fiscal.cod_tipo   = ".$rsNF->getCampo('cod_tipo');
                $obTTCEMGTipoNotaFiscal->recuperaTodos($rsTipoNota, $stFiltro);
                
                $arLinha16[0]['tipo_descricao']         = $rsTipoNota->getCampo('descricao');
                
                $rsNewRecord16 = new RecordSet;
                $rsNewRecord16->preenche($arLinha16);
                $arRecordSet[16] = $rsNewRecord16;
                
                $arLinha17[0]['aidf']                   = $rsNF->getCampo('aidf');
                if($rsNF->getCampo('chave_acesso_municipal')!=''){
                    $arLinha17[0]['chave_acesso']       = $rsNF->getCampo('chave_acesso_municipal');
                    $arLinha17[0]['tipo_chave']         = " MUNICIPAL";
                }else{
                    $arLinha17[0]['chave_acesso']       = $rsNF->getCampo('chave_acesso');
                    $arLinha17[0]['tipo_chave']         = "";
                }
                
                $rsNewRecord17 = new RecordSet;
                $rsNewRecord17->preenche($arLinha17);
                $arRecordSet[17] = $rsNewRecord17;
                
                $arLinha18[0]['inscricao_municipal']    = $rsNF->getCampo('inscricao_municipal');
                $arLinha18[0]['inscricao_estadual']     = $rsNF->getCampo('inscricao_estadual');
                
                $rsNewRecord18 = new RecordSet;
                $rsNewRecord18->preenche($arLinha18);
                $arRecordSet[18] = $rsNewRecord18;
                
                $arLinha19[0]['vl_liquidacao']      = number_format($rsNfLiquidacao->getCampo('vl_liquidacao') , 2, ",", ".");
                $arLinha19[0]['vl_associado']       = number_format($rsNfLiquidacao->getCampo('vl_associado')  , 2, ",", ".");
                $arLinha19[0]['vl_desconto']        = number_format($rsNF->getCampo('vl_desconto')     , 2, ",", ".");
                $arLinha19[0]['vl_total']           = number_format($rsNF->getCampo('vl_total')        , 2, ",", ".");
                $arLinha19[0]['vl_total_liquido']   = number_format($rsNF->getCampo('vl_total_liquido'), 2, ",", ".");
                
                $rsNewRecord19 = new RecordSet;
                $rsNewRecord19->preenche($arLinha19);
                $arRecordSet[19] = $rsNewRecord19;
            }
        }
    }

    //TCMBA
    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 5) {
        include_once(CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio()."/TTCMBANotaFiscalLiquidacao.class.php");
        $obTTCMBANotaFiscalLiquidacao = new TTCMBANotaFiscalLiquidacao();
        
        $stFiltroBANotaFiscal.= " WHERE cod_entidade         = " .$this->inCodEntidade;
        $stFiltroBANotaFiscal.= "   AND exercicio_liquidacao = '".$this->stExercicio."'";
        $stFiltroBANotaFiscal.= "   AND cod_nota_liquidacao  = " .$this->inCodNota;

        $obTTCMBANotaFiscalLiquidacao->recuperaTodos($rsNotaBA, $stFiltroBANotaFiscal,"",$boTransacao);

        $arLinha20[0]['cod_nota_liquidacao']  = $rsNotaBA->getCampo('cod_nota_liquidacao');
        $arLinha20[0]['exercicio_liquidacao'] = $rsNotaBA->getCampo('exercicio_liquidacao');
        $arLinha20[0]['cod_entidade']         = $rsNotaBA->getCampo('cod_entidade');
        $arLinha20[0]['ano']                  = $rsNotaBA->getCampo('ano');
        $arLinha20[0]['nro_nota']             = $rsNotaBA->getCampo('nro_nota');
        $arLinha20[0]['nro_serie']            = $rsNotaBA->getCampo('nro_serie');        
        $arLinha20[0]['nro_subserie']         = $rsNotaBA->getCampo('nro_subserie');
        $arLinha20[0]['data_emissao']         = $rsNotaBA->getCampo('data_emissao');
        $arLinha20[0]['vl_nota']              = number_format($rsNotaBA->getCampo('vl_nota'), 2, ",",".");
        $arLinha20[0]['descricao']            = $rsNotaBA->getCampo('descricao');
        $arLinha20[0]['cod_uf']               = $rsNotaBA->getCampo('cod_uf');

        $varCodUf = $rsNotaBA->getCampo('cod_uf');
        
        $stSiglaUF = !empty($varCodUf) ? SistemaLegado::pegaDado("sigla_uf","sw_uf"," WHERE cod_uf = ".$varCodUf." ") : "";
        $arLinha20[0]['sigla_uf'] = $stSiglaUF;
       
        $rsNewRecord20 = new RecordSet;
        $rsNewRecord20->preenche($arLinha20);
        $arRecordSet[20] = $rsNewRecord20;
    }

    //TCERS - Rio Grande do Sul
    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 23) {
        include_once CAM_GPC_TCERS_MAPEAMENTO.'TTCERSNotaFiscal.class.php';
        $obTTCERSNotaFiscal = new TTCERSNotaFiscal;
        
        $stFiltroDocumento = " WHERE nota_fiscal.cod_entidade = " .$this->inCodEntidade;
        $stFiltroDocumento.= "   AND nota_fiscal.exercicio    = '".$this->stExercicio."'";
        $stFiltroDocumento.= "   AND nota_fiscal.cod_nota     = " .$this->inCodNota;

        $obTTCERSNotaFiscal->recuperaTodos($rsDocumento, $stFiltroDocumento);
        
        $arLinha14[0]['exercicio']    = $rsDocumento->getCampo('exercicio');
        $arLinha14[0]['cod_entidade'] = $rsDocumento->getCampo('cod_entidade');
        $arLinha14[0]['cod_nota']     = $rsDocumento->getCampo('cod_nota');
        $arLinha14[0]['nro_nota']     = $rsDocumento->getCampo('nro_nota');
        $arLinha14[0]['nro_serie']    = $rsDocumento->getCampo('nro_serie');        
        $arLinha14[0]['data_emissao'] = $rsDocumento->getCampo('data_emissao');
       
        $rsNewRecord14 = new RecordSet;
        $rsNewRecord14->preenche($arLinha14);
        $arRecordSet[14] = $rsNewRecord14;
    }

    

}

}

?>