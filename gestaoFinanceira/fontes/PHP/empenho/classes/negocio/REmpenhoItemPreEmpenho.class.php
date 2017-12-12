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
    * Classe de Regra de Item Pre Empenho
    * Data de Criação   : 02/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Id: REmpenhoItemPreEmpenho.class.php 65158 2016-04-28 19:26:54Z evandro $

    * Casos de uso: uc-02.03.03, uc-02.03.02, uc-02.03.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS."Transacao.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoPreEmpenho.class.php";
include_once CAM_GA_ADM_NEGOCIO."RUnidadeMedida.class.php";
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoItemPreEmpenho.class.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoItemPreEmpenhoCompra.class.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoItemPreEmpenhoJulgamento.class.php';

class REmpenhoItemPreEmpenho
{
    /**
    * @access Private
    * @var Object
    **/
    public $obRUnidadeMedida;
    /**
    * @access Private
    * @var Integer
    **/
    public $inNumItem;
    /**
    * @access Private
    * @var Integer
    **/
    public $inCodMaterial;
    /**
    * @access Private
    * @var Integer
    **/
    public $inQuantidade;
    /**
    * @access Private
    * @var String
    **/
    public $stNomUnidade;
    /**
    * @access Private
    * @var String
    **/
    public $stSiglaUnidade;
    /**
    * @access Private
    * @var String
    **/
    public $stNomItem;
    /**
    * @access Private
    * @var String
    **/
    public $stComplemento;
    /**
    * @access Private
    * @var Numeric
    **/
    public $nuValorTotal;
    /**
    * @access Private
    * @var Numeric
    **/
    public $nuValorEmpenhadoAnulado;
    /**
    * @access Private
    * @var Numeric
    **/
    public $nuValorALiquidar;
    /**
    * @access Private
    * @var Numeric
    **/
    public $nuValorAAnular;
    /**
    * @access Private
    * @var Numeric
    **/
    public $nuValorLiquidado;
    /**
    * @access Private
    * @var Numeric
    **/
    public $nuValorLiquidadoAnulado;
    /**
    * @access Private
    * @var Reference Object
    **/
    public $roPreEmpenho;
    /**
    * @access Public;
    * @var Date;
    **/
    public $dtDataDocumento;
    /**
    * @access Public;
    * @var Boolean;
    **/
    public $boCompra;
    /**
    * @access Public;
    * @var Integer;
    **/
    public $inCodCotacao;
    /**
    * @access Public;
    * @var Integer;
    **/
    public $inExercicioJulgamento;
    /**
    * @access Public;
    * @var Integer;
    **/
    public $inCgmFornecedor;
    /**
    * @access Public;
    * @var Integer;
    **/
    public $inLoteCompras;
    /**
    * @access Public;
    * @var Integer;
    **/
    public $inCodItem;
    /**
    * @access Public;
    * @var Integer;
    **/
    public $inCodItemPreEmp;
    /**
    * @access Public;
    * @var Integer;
    **/
    public $inCodCentroCusto;

    public $inCodMarca;
    
    public $stNomeMarca;    
    
    /**
    * @access Public
    * @param Object $Valor
    **/
    public function setRUnidadeMedida($valor) { $this->obRUnidadeMedida = $valor; }
    /**
    * @access Public
    * @param Integer $Valor
    **/
    public function setNumItem($valor) { $this->inNumItem = $valor; }
    /**
    * @access Public
    * @param Integer $Valor
    **/
    public function setCodMaterial($valor) { $this->inCodMaterial = $valor; }
    /**
    * @access Public
    * @param Integer $Valor
    **/
    public function setQuantidade($valor) { $this->inQuantidade = $valor; }
    /**
    * @access Public
    * @param String $Valor
    **/
    public function setNomUnidade($valor) { $this->stNomUnidade = $valor; }
    /**
    * @access Public
    * @param String $Valor
    **/
    public function setSiglaUnidade($valor) { $this->stSiglaUnidade = $valor; }
    /**
    * @access Public
    * @param String $Valor
    **/
    public function setNomItem($valor) { $this->stNomItem = $valor; }
    /**
    * @access Public
    * @param String $Valor
    **/
    public function setComplemento($valor) { $this->stComplemento = $valor; }
    /**
    * @access Public
    * @param Numeric $Valor
    **/
    public function setValorTotal($valor) { $this->nuValorTotal = $valor; }
    /**
    * @access Public
    * @param Numeric $Valor
    **/
    public function setValorEmpenhadoAnulado($valor) { $this->nuValorEmpenhadoAnulado = $valor; }
    /**
    * @access Public
    * @param Numeric $Valor
    **/
    public function setValorALiquidar($valor) { $this->nuValorALiquidar = $valor; }
    /**
    * @access Public
    * @param Numeric $Valor
    **/
    public function setValorAAnular($valor) { $this->nuValorAAnular = $valor; }
    /**
    * @access Public
    * @param Numeric $Valor
    **/
    public function setValorLiquidado($valor) { $this->nuValorLiquidado = $valor; }
    /**
    * @access Public
    * @param Numeric $Valor
    **/
    public function setValorLiquidadoAnulado($valor) { $this->nuValorLiquidadoAnulado = $valor; }
    /**
    * @access Public
    * @param Date $Valor
    **/
    public function setDataDocumento($valor) { $this->dtDataDocumento = $valor; }
    /**
    * @access Public
    * @param Boolean $Valor
    **/
    public function setCompra($valor) { $this->boCompra = $valor; }
    /**
    * @access Public
    * @param Integer $Valor
    **/
    public function setCodItem($valor) { $this->inCodItem = $valor; }
    /**
    * @access Public
    * @param Integer $Valor
    **/
    public function setCodItemPreEmp($valor) { $this->inCodItemPreEmp = $valor; }
    /**
    * @access Public
    * @param Integer $Valor
    **/
    public function setCodCentroCusto($valor) { $this->inCodCentroCusto = $valor; }
    /**
    * @access Public
    * @param Integer $Valor
    **/
    public function setCodCotacao($valor) { $this->inCodCotacao = $valor; }
    public function setExercicioJulgamento($valor) { $this->inExercicioJulgamento = $valor; }
    public function setLoteCompras($valor) { $this->inLoteCompras = $valor; }
    public function setCgmFornecedor($valor) { $this->inCgmFornecedor = $valor; }
    public function setCodigoMarca($valor) { $this->inCodMarca = $valor; }
    public function setNomeMarca($valor) { $this->stNomeMarca = $valor; }

    /**
    * @access Public
    * @param Integer $Valor
    */
    public function setExercicioMapa($valor) { $this->inExercicioMapa= $valor; }
    /**
    * @access Public
    * @return Object
    **/
    public function getRUnidadeMedida() { return $this->obRUnidadeMedida; }
    /**
    * @access Public
    * @return Integer
    **/
    public function getNumItem() { return $this->inNumItem; }
    /**
    * @access Public
    * @return Integer
    **/
    public function getCodMaterial() { return $this->inCodMaterial; }
    /**
    * @access Public
    * @return Integer
    **/
    public function getQuantidade() { return $this->inQuantidade; }
    /**
    * @access Public
    * @return String
    **/
    public function getNomUnidade() { return $this->stNomUnidade; }
    /**
    * @access Public
    * @return String
    **/
    public function getSiglaUnidade() { return $this->stSiglaUnidade; }
    /**
    * @access Public
    * @return String
    **/
    public function getNomItem() { return $this->stNomItem; }
    /**
    * @access Public
    * @return String
    **/
    public function getComplemento() { return $this->stComplemento; }
    /**
    * @access Public
    * @return Numeric
    **/
    public function getValorTotal() { return $this->nuValorTotal; }
    /**
    * @access Public
    * @return Numeric
    **/
    public function getValorEmpenhadoAnulado() { return $this->nuValorEmpenhadoAnulado; }
    /**
    * @access Public
    * @return Numeric
    **/
    public function getValorALiquidar() { return $this->nuValorALiquidar; }
    /**
    * @access Public
    * @return Numeric
    **/
    public function getValorAAnular() { return $this->nuValorAAnular; }
    /**
    * @access Public
    * @return Numeric
    **/
    public function getValorLiquidado() { return $this->nuValorLiquidado; }
    /**
    * @access Public
    * @return Numeric
    **/
    public function getValorLiquidadoAnulado() { return $this->nuValorLiquidadoAnulado; }
    /**
    * @access Public
    * @return Date
    **/
    public function getDataDocumento() { return $this->dtDataDocumento; }
    /**
    * @access Public
    * @return Boolean
    **/
    public function getCompra() { return $this->boCompra; }
    /**
    * @access Public
    * @return Integer
    */
    public function getCodItemPreEmp() { return $this->inCodItemPreEmp; }
    /**
    * @access Public
    * @return Integer
    */
    public function getCodCentroCusto() { return $this->inCodCentroCusto; }
  
    public function getCodigoMarca() { return $this->inCodMarca; }

    public function getNomeMarca() { return $this->stNomeMarca; }
    /**
    * Método construtor
    * @access Public
    * @param Reference Object $roPreEmpenho
    **/
    public function REmpenhoItemPreEmpenho(&$roPreEmpenho)
    {
        $this->obTransacao      = new Transacao;
        $this->obRUnidadeMedida = new RUnidadeMedida;
        $this->roPreEmpenho     = &$roPreEmpenho;
    }

    /**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    **/
    public function consultar(&$rsRecordSet,$boTransacao = "")
    {
        $obTEmpenhoItemPreEmpenho  = new TEmpenhoItemPreEmpenho;

        $obTEmpenhoItemPreEmpenho->setDado( "cod_pre_empenho", $this->roPreEmpenho->getCodPreEmpenho() );
        $obTEmpenhoItemPreEmpenho->setDado( "exercicio"      , $this->roPreEmpenho->getExercicio()     );
        $obTEmpenhoItemPreEmpenho->setDado( "num_item"       , $this->inNumItem );
        $obErro = $obTEmpenhoItemPreEmpenho->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->inQuantidade  = $rsRecordSet->getCampo( "quantidade"  );
            $this->stNomUnidade  = $rsRecordSet->getCampo( "nom_unidade" );
            $this->nuValorTotal  = $rsRecordSet->getCampo( "vl_total"    );
            $this->stNomItem     = $rsRecordSet->getCampo( "nom_item"    );
            $this->stComplemento = $rsRecordSet->getCampo( "complemento" );
            $this->obRUnidadeMedida->setCodUnidade( $rsRecordSet->getCampo( "cod_unidade" ) );
            $this->obRUnidadeMedida->obRGrandeza->setCodGrandeza( $rsRecordSet->getCampo( "cod_grandeza" ) );
            $this->setCodigoMarca( $rsRecordSet->getCampo( "cod_marca" ) );            

        }

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    **/
    public function listar(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
    {
        $obTEmpenhoItemPreEmpenho  = new TEmpenhoItemPreEmpenho;

        if( $this->roPreEmpenho->getCodPreEmpenho() )
        $stFiltro  = " cod_pre_empenho = ".$this->roPreEmpenho->getCodPreEmpenho()."  AND ";
        if( $this->roPreEmpenho->getExercicio() )
        $stFiltro .= " exercicio = '".$this->roPreEmpenho->getExercicio()."' AND ";
        if( $this->inNumItem )
        $stFiltro  = " num_item = ".$this->inNumItem."  AND ";
        if( $this->inQuantidade )
        $stFiltro  = " quantidade = ".$this->inQuantidade."  AND ";
        if( $this->stNomUnidade )
        $stFiltro  = " lower(nom_unidade) like lower('%".$this->stNomUnidade."%')  AND ";
        if( $this->nuValorTotal )
        $stFiltro  = " vl_total = ".$this->nuValorTotal."  AND ";
        if( $this->stNomItem )
        $stFiltro  = " lower(nom_item) like lower('%".$this->stNomItem."%')  AND ";
        if( $this->stComplemento )
        $stFiltro .= " lower(complemento) like lower('%" . $this->stComplemento."%') AND ";
        if( $this->obRUnidadeMedida->getCodUnidade() )
        $stFiltro .= " cod_unidade = ".$this->obRUnidadeMedida->getCodUnidade()." AND ";
        if( $this->obRUnidadeMedida->obRGrandeza->getCodGrandeza() )
        $stFiltro .= " cod_grandeza = ".$this->obRUnidadeMedida->obRGrandeza->getCodGrandeza()." AND ";
        $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
        $stOrder = ($stOrder) ? $stOrder : "cod_pre_empenho";
        $obErro = $obTEmpenhoItemPreEmpenho->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

    public function consultaCodMaterial($boTransacao = "")
    {
        $obTEmpenhoItemPreEmpenhoCompra  = new TEmpenhoItemPreEmpenhoCompra;

        $obTEmpenhoItemPreEmpenhoCompra->setDado( 'cod_pre_empenho', $this->roPreEmpenho->getCodPreEmpenho() );
        $obTEmpenhoItemPreEmpenhoCompra->setDado( 'exercicio'      , $this->roPreEmpenho->getExercicio()     );
        $obTEmpenhoItemPreEmpenhoCompra->setDado( 'num_item'       , $this->getNumItem()                     );

        $obTEmpenhoItemPreEmpenhoCompra->consultar( $boTransacao );

        $this->inCodMaterial = $obTEmpenhoItemPreEmpenhoCompra->getDado( 'cod_item' );
    }

    public function consultaCodCentroCusto($boTransacao = "")
    {
        $obTEmpenhoItemPreEmpenhoJulgamento = new TEmpenhoItemPreEmpenhoJulgamento();

        $obTEmpenhoItemPreEmpenhoJulgamento->setDado( "cod_pre_empenho" , $this->roPreEmpenho->getCodPreEmpenho()   );
        $obTEmpenhoItemPreEmpenhoJulgamento->setDado( "exercicio"       , $this->roPreEmpenho->getExercicio()       );
        $obTEmpenhoItemPreEmpenhoJulgamento->setDado( "num_item"        , $this->getNumItem()                       );

        $obErro = $obTEmpenhoItemPreEmpenhoJulgamento->recuperaCentroCustoMapaItem( $rsCentroCusto, "", $boTransacao );

        if ( !$obErro->ocorreu() )
            $this->setCodCentroCusto( $rsCentroCusto->getCampo( "cod_centro" ) );
    }

    /**
    * Incluir Pre Empenho
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    **/
    public function incluir($boTransacao = "")
    {
        $obTEmpenhoItemPreEmpenho = new TEmpenhoItemPreEmpenho;
        $obTEmpenhoItemPreEmpenhoCompra = new TEmpenhoItemPreEmpenhoCompra;
        $obTEmpenhoItemPreEmpenhoJulgamento = new TEmpenhoItemPreEmpenhoJulgamento();

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            $obTEmpenhoItemPreEmpenho->setDado( "cod_pre_empenho" , $this->roPreEmpenho->getCodPreEmpenho() );
            $obTEmpenhoItemPreEmpenho->setDado( "exercicio"       , $this->roPreEmpenho->getExercicio()     );
            $obTEmpenhoItemPreEmpenho->proximoCod( $this->inNumItem, $boTransacao );

            $obTEmpenhoItemPreEmpenho->setDado( "exercicio"       , $this->roPreEmpenho->getExercicio()                    );
            $obTEmpenhoItemPreEmpenho->setDado( "cod_pre_empenho" , $this->roPreEmpenho->getCodPreEmpenho()                );
            $obTEmpenhoItemPreEmpenho->setDado( "num_item"        , $this->inNumItem                                       );
            $obTEmpenhoItemPreEmpenho->setDado( "quantidade"      , $this->inQuantidade                                    );
            $obTEmpenhoItemPreEmpenho->setDado( "nom_unidade"     , $this->stNomUnidade                                    );
            $obTEmpenhoItemPreEmpenho->setDado( "sigla_unidade"   , $this->stSiglaUnidade                                  );
            $obTEmpenhoItemPreEmpenho->setDado( "vl_total"        , $this->nuValorTotal                                    );
            $obTEmpenhoItemPreEmpenho->setDado( "nom_item"        , $this->stNomItem                                       );
            $obTEmpenhoItemPreEmpenho->setDado( "complemento"     , $this->stComplemento                                   );
            $obTEmpenhoItemPreEmpenho->setDado( "cod_unidade"     , $this->obRUnidadeMedida->getCodUnidade()               );
            $obTEmpenhoItemPreEmpenho->setDado( "cod_grandeza"    , $this->obRUnidadeMedida->obRGrandeza->getCodGrandeza() );
            $obTEmpenhoItemPreEmpenho->setDado( "cod_item"        , $this->inCodItemPreEmp                                 );
            $obTEmpenhoItemPreEmpenho->setDado( "cod_centro"      , $this->inCodCentroCusto                                );
            $obTEmpenhoItemPreEmpenho->setDado( "cod_marca"       , $this->getCodigoMarca()                                );

            $obErro = $obTEmpenhoItemPreEmpenho->inclusao( $boTransacao );

            if ( !$obErro->ocorreu() ) {
                if ( $this->getCodMaterial() ) {
                    $obTEmpenhoItemPreEmpenhoCompra->setDado( "cod_pre_empenho" , $this->roPreEmpenho->getCodPreEmpenho() );
                    $obTEmpenhoItemPreEmpenhoCompra->setDado( "exercicio"       , $this->roPreEmpenho->getExercicio()     );
                    $obTEmpenhoItemPreEmpenhoCompra->setDado( "num_item"        , $this->inNumItem                        );
                    $obTEmpenhoItemPreEmpenhoCompra->setDado( "cod_item"        , $this->inCodMaterial                    );

                    $obErro = $obTEmpenhoItemPreEmpenhoCompra->inclusao( $boTransacao );
                }
                if ( !$obErro->ocorreu() ) {
                    // vincula autorização ao compras
                    if ( $this->getCompra() ) {
                        $obTEmpenhoItemPreEmpenhoJulgamento->setDado( "cod_pre_empenho"     , $this->roPreEmpenho->getCodPreEmpenho()   );
                        $obTEmpenhoItemPreEmpenhoJulgamento->setDado( "exercicio"           , $this->roPreEmpenho->getExercicio()       );
                        $obTEmpenhoItemPreEmpenhoJulgamento->setDado( "num_item"            , $this->inNumItem                          );
                        $obTEmpenhoItemPreEmpenhoJulgamento->setDado( "cod_cotacao"         , $this->inCodCotacao                       );
                        $obTEmpenhoItemPreEmpenhoJulgamento->setDado( "exercicio_julgamento", $this->inExercicioJulgamento              );
                        $obTEmpenhoItemPreEmpenhoJulgamento->setDado( "cgm_fornecedor"      , $this->inCgmFornecedor                    );
                        $obTEmpenhoItemPreEmpenhoJulgamento->setDado( "lote"                , $this->inLoteCompras                      );
                        $obTEmpenhoItemPreEmpenhoJulgamento->setDado( "cod_item"            , $this->inCodItem                          );

                        $obErro = $obTEmpenhoItemPreEmpenhoJulgamento->inclusao( $boTransacao );
                    }
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoItemPreEmpenho );

        return $obErro;
    }

    /**
    * Alterar Pre Empenho
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    **/
    public function alterar($boTransacao = "")
    {
        $obTEmpenhoItemPreEmpenho = new TEmpenhoItemPreEmpenho;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTEmpenhoItemPreEmpenho->setDado( "exercicio"       , $this->roPreEmpenho->getExercicio()                    );
            $obTEmpenhoItemPreEmpenho->setDado( "cod_pre_empenho" , $this->roPreEmpenho->getCodPreEmpenho()                );
            $obTEmpenhoItemPreEmpenho->setDado( "num_item"        , $this->inNumItem                                       );
            $obTEmpenhoItemPreEmpenho->setDado( "quantidade"      , $this->inQuantidade                                    );
            $obTEmpenhoItemPreEmpenho->setDado( "nom_unidade"     , $this->stNomUnidade                                    );
            $obTEmpenhoItemPreEmpenho->setDado( "sigla_unidade"   , $this->stSiglaUnidade                                  );
            $obTEmpenhoItemPreEmpenho->setDado( "vl_total"        , $this->nuValorTotal                                    );
            $obTEmpenhoItemPreEmpenho->setDado( "nom_item"        , $this->stNomItem                                       );
            $obTEmpenhoItemPreEmpenho->setDado( "complemento"     , $this->stComplemento                                   );
            $obTEmpenhoItemPreEmpenho->setDado( "cod_unidade"     , $this->obRUnidadeMedida->getCodUnidade()               );
            $obTEmpenhoItemPreEmpenho->setDado( "cod_grandeza"    , $this->obRUnidadeMedida->obRGrandeza->getCodGrandeza() );
            $obTEmpenhoItemPreEmpenho->setDado( "cod_marca"       , $this->getCodigoMarca()                                );

            $obErro = $obTEmpenhoItemPreEmpenho->alteracao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoItemPreEmpenho );

        return $obErro;
    }

    /**
    * Exclui Pre empenho
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
    **/
    public function excluir($boTransacao = "")
    {
        $obTEmpenhoItemPreEmpenho = new TEmpenhoItemPreEmpenho;
        $obTEmpenhoItemPreEmpenhoCompra = new TEmpenhoItemPreEmpenhoCompra;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $obTEmpenhoItemPreEmpenhoCompra->setDado("cod_pre_empenho", $this->roPreEmpenho->getCodPreEmpenho() );
            $obTEmpenhoItemPreEmpenhoCompra->setDado("exercicio"      , $this->roPreEmpenho->getExercicio()     );
            $obErro = $obTEmpenhoItemPreEmpenhoCompra->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTEmpenhoItemPreEmpenho->setDado("cod_pre_empenho", $this->roPreEmpenho->getCodPreEmpenho() );
                $obTEmpenhoItemPreEmpenho->setDado("exercicio"      , $this->roPreEmpenho->getExercicio()     );
                $stCampoCodTEMP = $obTEmpenhoItemPreEmpenho->getCampoCod();
                $obTEmpenhoItemPreEmpenho->setCampoCod( "" );
                $obErro = $obTEmpenhoItemPreEmpenho->exclusao( $boTransacao );
                $obTEmpenhoItemPreEmpenho->setCampoCod( $stCampoCodTEMP );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoItemPreEmpenho );

        return $obErro;
    }

}
