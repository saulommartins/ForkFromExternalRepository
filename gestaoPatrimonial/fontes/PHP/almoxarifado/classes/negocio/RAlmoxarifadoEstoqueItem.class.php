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
 * Classe de Regra de Catálogo
 * Data de Criação   : 03/02/2006

 * @author Analista: Diego Barbosa Victoria
 * @author Desenvolvedor: Tonismar Régis Bernardo

 * @package URBEM
 * @subpackage Regra

 * Casos de uso: uc-03.03.10

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoEstoqueMaterial.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarifado.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCentroDeCustos.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoItemMarca.class.php";
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPerecivel.class.php";
include_once CAM_GP_FRO_NEGOCIO."RFrotaItem.class.php";

class RAlmoxarifadoEstoqueItem extends RAlmoxarifadoItemMarca
{
    /**
       * @access Private
       * @var Object
    */
    public $obTransacao;
    /**
       * @access Private
       * @var Object
    */
    public $obRCentroDeCustos;
    /**
      * @access Private
      * @var Object
    */
    public $obRAlmoxarifado;

    /**
        * @access Private
        * @var Object
    */
    public $obRFrotaItem;
    /**
        * @access Private
        * @var Object
    */
    public $arRAlmoxarifadoPerecivel;

    /**
        * @access Private
        * @var Object
    */
    public $roUltimoPerecivel;

    /**
        * @access Public
        * @param Object $valor
    */
    public function setTransacao($valor) { $this->obTransacao                     = $valor; }
    /**
        * @access Public
        * @param Object $valor
    */
    public function setTAlmoxarifadoEstoqueMaterial($valor) { $this->obTAlmoxarifadoEstoqueMaterial  = $valor; }

    /**
        * @access Public
        * @return Object
    */
    public function getTransacao() { return $this->obTransacao;                     }
    /**
        * @access Public
        * @return Object
    */
    public function getTAlmoxarifadoEstoqueMaterial() { return $this->obTAlmoxarifadoEstoqueMaterial;  }

    /**
         * Método construtor
         * @access Private
    */

    public function RAlmoxarifadoEstoqueItem()
    {
        parent::RAlmoxarifadoItemMarca();
        $this->setTransacao( new Transacao );
        $this->obRAlmoxarifado = new RAlmoxarifadoAlmoxarifado;
        $this->obRCentroDeCustos = new RAlmoxarifadoCentroDeCustos;
        $this->obRFrotaItem = new RFrotaItem;
    }

    public function addPerecivel()
    {
       $this->arRAlmoxarifadoPerecivel[] = new RAlmoxarifadoPerecivel($this);
       $this->roUltimoPerecivel = &$this->arRAlmoxarifadoPerecivel[count($this->arRAlmoxarifadoPerecivel)-1];
    }

    public function listarPereciveis(&$rsRecordSet, $boTransacao = "")
    {
       $rsRecordSet = new RecordSet;
       $obRAlmxoxarifadoPereciveis = new RAlmoxarifadoPerecivel($this);
       $obRAlmxoxarifadoPereciveis->listar($rsRecordSet, '', '', $boTransacao);
    }

    /**
        * Executa um recuperaPorChave recursivo na classe Persistente
        * @access Public
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function consultar($obTransacao = "")
    {
        $obTEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
        $obTEstoqueMaterial->setDado( 'cod_almoxarifado', $this->obRAlmoxarifado->getCodigo() );
        $obTEstoqueMaterial->setDado( 'cod_item', $this->obRCatalogoItem->getCodigo() );
        $obTEstoqueMaterial->setDado( 'cod_marca', $this->obRMarca->getCodigo() );
        $obTEstoqueMaterial->setDado( 'cod_centro', $this->obRCentroDeCustos->getCodigo() );
        $obErro = $obTEstoqueMaterial->recuperaPorChave( $rsRecordSet, $obTransacao );

        if ( !$obErro->ocorreu() ) {
            $obErro = parent::consultar();
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obRMarca->consultar();
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->obRCatalogoItem->consultar();
                    if (!$obErro->ocorreu() ) {
                        $obErro = $this->obRCentroDeCustos->consultar();
                        if (!$obErro->ocorreu()) {
                            $obErro = $this->obRFrotaItem->consultar();
                            if (!$obErro->ocorreu()) {
                                $this->listarPereciveis($rsPereciveis, $boTransacao);
                                while (!$rsPereciveis->eof()) {
                                   $this->addPerecivel();
                                   $this->roUltimoPerecivel->setLote($rsPereciveis->getCampo('lote'));
                                   $obErro = $this->roUltimoPerecivel->consultar($boTransacao);
                                   $rsPereciveis->proximo();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
      * Executa a inclusao
      * @access Public
      * @param Object $obTransacao
      * @return Object Erro
    */
    public function incluir($obTransacao = "")
    {
        $obTEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $obTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTEstoqueMaterial->setDado( 'cod_almoxarifado', $this->obRAlmoxarifado->getCodigo()   );
            $obTEstoqueMaterial->setDado( 'cod_item'        , $this->obRCatalogoItem->getCodigo()   );
            $obTEstoqueMaterial->setDado( 'cod_marca'       , $this->obRMarca->getCodigo()          );
            $obTEstoqueMaterial->setDado( 'cod_centro'      , $this->obRCentroDecustos->getCodigo() );
            $obErro = $obTEstoqueMaterial->inclusao( $obTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $obTransacao, $obErro, $obTEstoqueMaterial );

        return $obErro;
    }

    /**
      * Executa a exclusao
      * @access Public
      * @param Object $obTransacao
      * @return Object $obErro
    */
    public function excluir($obTransacao = "")
    {
        $obTEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $obTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTEstoqueMaterial->setDado( 'cod_almoxarifado', $this->obRAlmoxarifado->getCodigo()   );
            $obTEstoqueMaterial->setDado( 'cod_item'        , $this->obRCatalogoItem->getCodigo()   );
            $obTEstoqueMaterial->setDado( 'cod_marca'       , $this->obRMarca->getCodigo()          );
            $obTEstoqueMaterial->setDado( 'cod_centro'      , $this->obRCentroDecustos->getCodigo() );
            $obErro = $obTEstoqueMaterial->exclusao( $obTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $obTransacao, $obErro, $obTEstoqueMaterial );

        return $obErro;
    }

    /**
      * Retorna o Saldo do Estoque
      * @acces Public
      * @param Numeric $inSaldo
      * @return Object $obErro
    */
    public function retornaSaldoEstoque(&$inSaldo, $obTransacao = "", $boUsarMarca = true)
    {
        $obTEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
        $stFiltro = "";

        if ($boUsarMarca && $this->obRMarca->getCodigo()) {
            $stFiltro .= " AND aem.cod_marca = ". $this->obRMarca->getCodigo();
        }

        if ($this->obRCentroDeCustos->getCodigo()) {
            $stFiltro .= " AND aem.cod_centro = ". $this->obRCentroDeCustos->getCodigo();
        }

        if ($this->obRAlmoxarifado->getCodigo()) {
            $stFiltro .= " AND aem.cod_almoxarifado = ". $this->obRAlmoxarifado->getCodigo();
        }

        if ($this->obRCatalogoItem->getCodigo()) {
            $stFiltro .= " AND aem.cod_item = ". $this->obRCatalogoItem->getCodigo();
        }

        $stFiltro .= " GROUP BY aem.cod_item ";
        $stOrdem   = " ORDER BY aem.cod_item ";

        $obErro = $obTEstoqueMaterial->recuperaSaldoEstoque( $rsRecordSet, $stFiltro, $stOrdem, $obTransacao );

        $inSaldo = $rsRecordSet->getCampo( 'saldo_estoque' );

        return $obErro;
    }

    /**
      * Lista centro de custo conforme filtro
      * @access Public
      * @param Object $rsRecodSet Object $obTransacao
      * @return Object $obErro
    */
    public function ListarEstoqueCentroDeCusto(&$rsrecordset, $obtransacao = "")
    {
        $stFiltro = "";
        $obTEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;

        if ( $this->obRCentroDeCustos->getCodigo() ) {
            $stFiltro .= " and aem.cod_centro = ". $this->obRCentroDecustos->getCodigo();
        }

        if ( $this->obRMarca->getCodigo() ) {
            $stFiltro .= " and am.cod_marca = ". $this->obRMarca->getCodigo();
        }
        if ($this->obRCentroDeCustos->roRPermissaoCentroDeCustos->obRCGMPessoaFisica->getNumCGM()) {
          $stFiltro .= " and accp.numcgm = ". $this->obRCentroDeCustos->roRPermissaoCentroDeCustos->obRCGMPessoaFisica->getNumCGM();
        }
        if ($this->obRAlmoxarifado->obRCGMResponsavel->getNumCGM()) {
          $stFiltro .= " and apa.cgm_almoxarife = ". $this->obRAlmoxarifado->obRCGMResponsavel->getNumCGM();
        }

        $stOrdem = " order by acc.cod_centro ";
        $obErro = $obTEstoqueMaterial->recuperaEstoqueCentroDeCusto( $rsRecordSet, $stFiltro, $stOrdem, $obTransacao );

        return $obErro;
    }

   public function listarCentroDeCustoAlmoxarifado(&$rsRecordSet, $obTransacao = "")
   {
        $stFiltro = "";
        $obTEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;

        if ($this->obRMarca->getCodigo()) {
            $stFiltro .= " and estoque_material.cod_marca        = ". $this->obRMarca->getCodigo();
        }

        if ($this->obRAlmoxarifado->getCodigo()) {
            $stFiltro .= " and estoque_material.cod_almoxarifado = ". $this->obRAlmoxarifado->getCodigo();
        }

        if ($this->obRCatalogoItem->getCodigo()) {
            $stFiltro .= " and estoque_material.cod_item         = ". $this->obRCatalogoItem->getCodigo();

        }

        $stOrdem = "";
        $obErro = $obTEstoqueMaterial->recuperaCentroDeCustoAlmoxarifado($rsRecordSet, $stFiltro, $stOrdem, $obTransacao);

        return $obErro;
    }
}
?>
