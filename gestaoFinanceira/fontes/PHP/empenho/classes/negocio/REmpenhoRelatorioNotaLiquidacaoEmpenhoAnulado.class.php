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

    $Revision: 30805 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.24
                    uc-02.03.04
*/

/*
$Log$
Revision 1.11  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO  );
include_once ( CAM_GA_ADM_NEGOCIO     ."RCadastroDinamico.class.php"      );
include_once ( CAM_GF_EMP_NEGOCIO     ."REmpenhoNotaLiquidacao.class.php" );
include_once ( CAM_GF_EMP_NEGOCIO     ."REmpenhoEmpenho.class.php"        );

/**
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class REmpenhoRelatorioNotaLiquidacaoEmpenhoAnulado extends PersistenteRelatorio
{
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stExercicioEmpenho;

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
/**
    * @var String
    * @access Private
*/
var $stTimestamp;

/**
     * @access public
     * @param string $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
/**
     * @access public
     * @param string $valor
*/
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
     * @access public
     * @param string $valor
*/
function setTimestamp($valor) { $this->stTimestamp = $valor; }

/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;     }
/**
     * @access Public
     * @return String
*/
function getExercicioEmpenho() { return $this->stExercicioEmpenho;     }

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
     * @access Public
     * @return String
*/
function getTimestamp() { return $this->stTimestamp;  }

/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioNotaLiquidacaoEmpenhoAnulado()
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
    $stFiltro   .= " AND   nl.exercicio     = " . $this->stExercicio;
    $obTEmpenhoNotaLiquidacao->setDado( 'exercicio' , $this->getExercicio() );
    $stOrder = " ORDER BY li.num_item ";
/*    if ( trim($this->boImplantado) == 't' ) {
        $obErro = $obTEmpenhoNotaLiquidacao->recuperaNotaLiquidacaoEmpenhoRestos( $rsRecordSet, $stFiltro, $stOrder );
    } else {
*/
        $obTEmpenhoNotaLiquidacao->setDado( 'cod_nota'    , $this->inCodNota     );
        $obTEmpenhoNotaLiquidacao->setDado( 'cod_entidade', $this->inCodEntidade );
        $obTEmpenhoNotaLiquidacao->setDado( 'exercicio'   , $this->stExercicioEmpenho   );
        $obTEmpenhoNotaLiquidacao->setDado( 'timestamp'   , $this->stTimestamp   );
        $obErro = $obTEmpenhoNotaLiquidacao->recuperaNotaLiquidacaoAnulada( $rsRecordSet );
//    }

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

    //$rsRecordSet->debug();
    //exit();

    if ( !$rsRecordSet->eof() ) {

        //Linha0
        $arLinha0[0]['entidade']   = $rsRecordSet->getCampo('cod_entidade') . ' - ' . $rsRecordSet->getCampo('nom_entidade');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha0);
        $arRecordSet[0] = $rsNewRecord;

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
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha3);
        $arRecordSet[3] = $rsNewRecord;

        //Linha4
        // $arLinha4[0]['descricao']     = $rsRecordSet->getCampo('descricao');
        // $rsNewRecord = new RecordSet;
        // $rsNewRecord->preenche($arLinha4);
        // $arRecordSet[4] = $rsNewRecord;
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

    }
    $inCount=0;
    while ( !$rsRecordSet->eof() ) {
        $arLinha5[$inCount]['Item']                  = $rsRecordSet->getCampo('num_item');
        $arLinha5[$inCount]['ValorEmpenhado']        = $rsRecordSet->getCampo('empenhado');
        $arLinha5[$inCount]['ValorLiquidadoAnulado'] = $rsRecordSet->getCampo('vl_anulado');
//        $arLinha5[$inCount]['ValorLiquidadoAnulado'] = sessao->transf5[$rsRecordSet->getCampo('num_item')]['valor'];
//        $vl_liquidado_anulado                        = str_replace('.','', $rsRecordSet->getCampo('vl_anulado'));
//        $vl_liquidado_anulado                        = str_replace(',','.',$vl_liquidado_anulado);
        $arLinha5[$inCount]['ValorLiquidado']        = $rsRecordSet->getCampo('liquidado');
//        $arLinha5[$inCount]['ValorLiquidado']        = $rsRecordSet->getCampo('liquidado') + $vl_liquidado_anulado;
        $nuTotal                                    += $rsRecordSet->getCampo('vl_anulado');

        $stNomItem                                  = strtoupper($rsRecordSet->getCampo('nom_item')." ".$rsRecordSet->getCampo('complemento'));
        $stNomItem                                  = str_replace( chr(10), "", $stNomItem );
        $stNomItem                                  = str_replace( chr(13), " ", $stNomItem );
        $stNomItem                                  = wordwrap( $stNomItem, 59, chr(13) );
        $arNomItem                                  = explode( chr(13), $stNomItem );
        foreach ($arNomItem as $stNomItem) {
            $arLinha5[$inCount]['Especificacao']    = $stNomItem;
            $inCount++;
        }

        $rsRecordSet->proximo();
    }
    $rsRecordSet->setPrimeiroElemento();
    //Bloco5
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha5);
    $rsNewRecord->addFormatacao("ValorEmpenhado","NUMERIC_BR");
    $rsNewRecord->addFormatacao("ValorLiquidado","NUMERIC_BR");
    $rsNewRecord->addFormatacao("ValorLiquidadoAnulado","NUMERIC_BR");
    $arRecordSet[5] = $rsNewRecord;

    //Bloco6
    $arLinha6[0]['Total']       = ' Total ';
    $arLinha6[0]['ValorTotal']  = $nuTotal;
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha6);
    $rsNewRecord->addFormatacao("ValorTotal","NUMERIC_BR");
    $arRecordSet[6] = $rsNewRecord;

    //Bloco7
//    $arLinha7[0]['1'] = ' LIQUIDAÇÃO ANULADA EM: '.$rsRecordSet->getCampo('dt_liquidacao');
    $arLinha7[0]['1'] = ' LIQUIDAÇÃO ANULADA EM: '. $rsRecordSet->getCampo( 'dt_anulacao' );
    $arLinha7[0]['2'] = '';
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha7);
    $arRecordSet[7] = $rsNewRecord;

    //Bloco8
    //$arLinha8 = array();
    $arLinha8[] = array("1"=>"");
    $stObs = str_replace( chr(10) , "", $rsRecordSet->getCampo('observacao') );
    $stObs = wordwrap( $stObs , 75, chr(13) );
    $arObs = explode( chr(13), $stObs );
    foreach ($arObs as $stObs) {
        $arObs[1] = $stObs;
        $arLinha8[] = $arObs;
    }
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha8);
    $arRecordSet[10] = $rsNewRecord;

    $inCount = 0;
    while ( !$rsAtributos->eof() ) {
        $arLinha9[$inCount]['Nome']     = $rsAtributos->getCampo('nom_atributo');
        if ($rsAtributos->getCampo('cod_tipo')==3) {
            // $arDescricoes = explode("[][][]",$rsAtributos->getCampo('valor_padrao_desc'));
            // $arLinha9[$inCount]['Valor']    = $arDescricoes[ (trim($rsAtributos->getCampo('valor'))-1) ];
            $arDescricoes = explode("[][][]",$rsAtributos->getCampo('valor_padrao_desc'));
            $arValores    = explode(",",$rsAtributos->getCampo('valor_padrao'));

            foreach ($arValores as $chave => $valor) {
                if ( $valor == $rsAtributos->getCampo('valor') ) {
                    $arLinha9[$inCount]['Valor']    = $arDescricoes[ $chave ];
                }
            }
        } elseif ($rsAtributos->getCampo('cod_tipo')==4) {
            $arDescricoes   = explode("[][][]",$rsAtributos->getCampo('valor_padrao_desc'));
            $arValores      = explode(",",$rsAtributos->getCampo('valor_desc'));
            $stValor        = "";
            for ($inIndice=0; $inIndice<count($arDescricoes); $inIndice++) {
                $stValor = $arDescricoes[ $arValores[($inIndice-1)] ];
            }
            $arLinha9[$inCount]['Valor']    = $stValor;
        } else {
            $arLinha9[$inCount]['Valor']    = ($rsAtributos->getCampo('valor')?$rsAtributos->getCampo('valor'):$rsAtributos->getCampo('valor_padrao'));
        }
        $inCount++;
        $rsAtributos->proximo();
    }
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha9);
    $arRecordSet[9] = $rsNewRecord;
}

}
