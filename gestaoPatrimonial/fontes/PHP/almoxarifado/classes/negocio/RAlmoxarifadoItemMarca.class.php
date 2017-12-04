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
                    uc-03.03.14
*/

/*
$Log$
Revision 1.8  2006/07/06 14:05:21  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 13:30:43  gelson
correção de interface 'Ítem' para 'Item'

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php" );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoMarca.class.php" );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php" );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItemMarca.class.php" );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItemBarras.class.php" );

/**
    * Classe de Regra de ItemMarca
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Tonismar Régis Bernardo
*/

class RAlmoxarifadoItemMarca
{
    /**
      * @access Private
      * @var String
    */
    public $stCodigoBarras;

    /**
      * @access Private
      * @var Object
    */
    public $obRMarca;

    /**
      * @access Private;
      * @var Object;
    */
    public $obRCatalogoItem;

    /**
      * @access Public
      * @param String
    */
    public function setCodigoBarras($valor) { $this->stCodigoBarras = $valor; }

    /**
      * @access Public
      * @return String
    */
    public function getCodigoBarras() { return $this->stCodigoBarras; }

    /**
      * Método Construtor
      * @access Public
    */
    public function RAlmoxarifadoItemMarca()
    {
        $this->obTransacao     = new Transacao;
        $this->obRMarca        = new RAlmoxarifadoMarca;
        $this->obRCatalogoItem = new RAlmoxarifadoCatalogoItem;
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
        $obTCatalogoItemBarras = new TAlmoxarifadoCatalogoItemBarras;
        $obTCatalogoItemBarras->setDado( 'cod_marca', $this->obRMarca->getCodigo());
        $obTCatalogoItemBarras->setDado( 'cod_item' , $this->obRCatalogoItem->getCodigo());
        $obErro = $obTCatalogoItemBarras->recuperaPorChave( $rsRecordSet, $obTransacao);
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obRMarca->consultar( $obTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obRCatalogoItem->consultar( $obTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->stCodigoBarras = $rsRecordSet->getCampo( 'codigo_barras' );
                }
            }
        }

        return $obErro;
    }

    /**
      * @access Public
      * @param Object $obTransacao
      * @return Object $obErro
    */
    public function salvar($obTransacao)
    {
     $this->listar($teste,$boTransacao);
     echo $teste;
    }

    /**
      * Efetua a inclusão de um registro
      * @access Public
      * @param Object $obTransacao
      * @return Object $obErro
    */
    public function incluir($obTransacao = "")
    {
        $obTCatalogoItemBarras = new TAlmoxarifadoCatalogoItemBarras;
        $obTCatalogoItemMarca  = new TAlmoxarifadoCatalogoItemMarca;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $obTransacao );

        if (!$obErro->ocorreu()) {
            $obTCatalogoItemMarca->setDado( 'cod_marca' , $this->obRMarca->getCodigo() );
            $obTCatalogoItemMarca->setDado( 'cod_item'  , $this->obRCatalogoItem->getCodigo() );

            $obErro = $obTCatalogoItemMarca->inclusao( $obTransacao );

                if (!$obErro->ocorreu()) {
                    if ($this->getCodigoBarras()) {
                        $obTCatalogoItemBarras->setDado( 'cod_marca' , $this->obRMarca->getCodigo() );
                        $obTCatalogoItemBarras->setDado( 'cod_item'  , $this->obRCatalogoItem->getCodigo() );
                        $obErro = $obTCatalogoItemBarras->inclusao( $obTransacao );
                    }
               }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $obTransacao, $obErro, $obTCatalogoItemBarras );

        return $obErro;
    }

    /**
      * Efetua a exclusão de um registro
      * @access Public
      * @param Object $obTransacao
      * @return Object $obErro
    */
    public function excluir($obTransacao = "")
    {
        $obTCatalogoItemBarras = new TAlmoxarifadoCatalogoItemBarras;
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $obTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTCatalogoItemBarras->setDado( 'cod_marca' , $this->obRMarca->getCodigo() );
            $obTCatalogoItemBarras->setDado( 'cod_item'  , $this->obRCatalogoItem->getCodigo() );
            $obErro = $obTCatalogoItemBarras->exclusao( $obTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $obTransacao, $obErro, $obTCatalogoItemBarras );

        return $obErro;
    }

    /**
      * Lista as marcas conforme o item
      * @access Public
      * @param Object $obTransacao
      * @return Object $obErro
    */
    public function listar(&$rsRecordSet, $obTransacao = "")
    {
        $stFiltro = "";
        $obTCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca;

        if ( $this->obRCatalogoItem->getCodigo() ) {
            $stFiltro .= " and acim.cod_item = ".$this->obRCatalogoItem->getCodigo();
        }

        if ( $this->obRMarca->getCodigo() ) {
            $stFiltro .= " and am.cod_marca = ".$this->obRMarca->getCodigo();
        }

        $stOrdem = " order by cod_marca";
        $obErro = $obTCatalogoItemMarca->recuperaItemMarca( $rsRecordSet, $stFiltro, $stOrdem, $obTransacao );

        return $obErro;
    }
}
